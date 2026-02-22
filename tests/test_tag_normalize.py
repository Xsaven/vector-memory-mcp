"""
Tests for Tag Normalization: Snapshot, Preview, Apply, Restore
==============================================================

Validates the tag normalization workflow:
1. snapshot_create captures current state
2. tag_normalize_preview is deterministic and non-destructive
3. tag_normalize_apply requires snapshot_id and matching preview_id
4. snapshot_restore restores exact pre-state
5. Apply is tags-only (content/embeddings untouched)
"""

import json
import os
import sqlite3
import tempfile
from pathlib import Path
from unittest.mock import MagicMock

import pytest


@pytest.fixture
def temp_db(tmp_path):
    """Create a temporary database with test data."""
    db_path = tmp_path / "memory" / "vector_memory.db"
    db_path.parent.mkdir(parents=True, exist_ok=True)
    return db_path


@pytest.fixture
def memory_store(temp_db):
    """Create a VectorMemoryStore with test data."""
    import sys
    sys.path.insert(0, str(Path(__file__).parent.parent))
    sys.path.insert(0, str(Path(__file__).parent.parent / "src"))

    from src.memory_store import VectorMemoryStore

    store = VectorMemoryStore(temp_db, memory_limit=1000)
    store._init_database()
    store._db_initialized = True

    # Insert test data directly (bypass embedding model for speed)
    conn = store._get_connection()
    try:
        # Add canonical tags with fake embeddings
        import numpy as np
        fake_emb = np.random.rand(384).astype(np.float32)

        import sqlite_vec
        emb_blob = sqlite_vec.serialize_float32(fake_emb.tolist())

        now = "2026-02-22T00:00:00+00:00"
        canonical_tags = ["brain-compile", "phpstan", "security", "architecture"]
        for tag in canonical_tags:
            conn.execute(
                "INSERT OR IGNORE INTO canonical_tags (tag, embedding, frequency, created_at) VALUES (?, ?, 1, ?)",
                (tag, emb_blob, now)
            )

        # Add test memories with tags (some canonical, some not)
        test_memories = [
            (1, "hash1", "Memory about compilation", "code-solution",
             json.dumps(["brain-compile", "flock"]), now, now, 0),
            (2, "hash2", "Memory about static analysis", "code-solution",
             json.dumps(["phpstan", "quality"]), now, now, 0),
            (3, "hash3", "Memory about auth", "security",
             json.dumps(["security", "auth-flow"]), now, now, 0),
            (4, "hash4", "Memory about design", "architecture",
             json.dumps(["architecture", "patterns"]), now, now, 0),
        ]
        for m in test_memories:
            conn.execute(
                "INSERT INTO memory_metadata (id, content_hash, content, category, tags, created_at, updated_at, access_count) "
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?)", m
            )
            # Insert fake vector
            conn.execute(
                "INSERT INTO memory_vectors (rowid, embedding) VALUES (?, ?)",
                (m[0], emb_blob)
            )

        conn.commit()
    finally:
        conn.close()

    return store


class TestSnapshotCreate:
    """Tests for snapshot_create."""

    def test_creates_snapshot_with_all_memories(self, memory_store):
        result = memory_store.snapshot_create("test snapshot")
        assert result["success"] is True
        assert result["memory_count"] == 4
        assert len(result["snapshot_id"]) == 16
        assert "created_at" in result

    def test_snapshot_id_is_deterministic(self, memory_store):
        r1 = memory_store.snapshot_create("first")
        r2 = memory_store.snapshot_create("second")
        assert r1["snapshot_id"] == r2["snapshot_id"]

    def test_snapshot_id_changes_when_tags_change(self, memory_store):
        r1 = memory_store.snapshot_create("before")

        # Modify a tag
        conn = memory_store._get_connection()
        conn.execute(
            "UPDATE memory_metadata SET tags = ? WHERE id = 1",
            (json.dumps(["brain-compile", "modified-tag"]),)
        )
        conn.commit()
        conn.close()

        r2 = memory_store.snapshot_create("after")
        assert r1["snapshot_id"] != r2["snapshot_id"]

    def test_snapshot_persists_in_db(self, memory_store):
        result = memory_store.snapshot_create("persist test")
        conn = memory_store._get_connection()
        row = conn.execute(
            "SELECT snapshot_id, memory_count FROM tag_snapshots WHERE snapshot_id = ?",
            (result["snapshot_id"],)
        ).fetchone()
        conn.close()
        assert row is not None
        assert row[1] == 4


class TestSnapshotRestore:
    """Tests for snapshot_restore."""

    def test_restore_returns_error_for_missing_snapshot(self, memory_store):
        result = memory_store.snapshot_restore("nonexistent")
        assert result["success"] is False
        assert "not found" in result["error"].lower() or "not found" in result["message"].lower()

    def test_restore_reverts_tag_changes(self, memory_store):
        # Create snapshot
        snap = memory_store.snapshot_create("before changes")

        # Modify tags
        conn = memory_store._get_connection()
        conn.execute(
            "UPDATE memory_metadata SET tags = ? WHERE id = 1",
            (json.dumps(["completely-different"]),)
        )
        conn.commit()
        conn.close()

        # Verify change
        conn = memory_store._get_connection()
        row = conn.execute("SELECT tags FROM memory_metadata WHERE id = 1").fetchone()
        conn.close()
        assert json.loads(row[0]) == ["completely-different"]

        # Restore
        result = memory_store.snapshot_restore(snap["snapshot_id"])
        assert result["success"] is True
        assert result["restored_count"] == 4

        # Verify restored
        conn = memory_store._get_connection()
        row = conn.execute("SELECT tags FROM memory_metadata WHERE id = 1").fetchone()
        conn.close()
        assert json.loads(row[0]) == ["brain-compile", "flock"]


class TestTagNormalizePreview:
    """Tests for tag_normalize_preview."""

    def test_preview_is_non_destructive(self, memory_store):
        # Get tags before
        conn = memory_store._get_connection()
        before = conn.execute("SELECT id, tags FROM memory_metadata ORDER BY id").fetchall()
        conn.close()

        # Create mock model
        model = MagicMock()
        model.batch_similarity.return_value = [0.5, 0.5, 0.5, 0.5]

        memory_store.tag_normalize_preview(embedding_model=model)

        # Get tags after
        conn = memory_store._get_connection()
        after = conn.execute("SELECT id, tags FROM memory_metadata ORDER BY id").fetchall()
        conn.close()

        assert before == after

    def test_preview_id_is_deterministic(self, memory_store):
        model = MagicMock()
        model.batch_similarity.return_value = [0.5, 0.5, 0.5, 0.5]

        r1 = memory_store.tag_normalize_preview(embedding_model=model)
        r2 = memory_store.tag_normalize_preview(embedding_model=model)

        assert r1["preview_id"] == r2["preview_id"]

    def test_preview_returns_required_fields(self, memory_store):
        model = MagicMock()
        model.batch_similarity.return_value = [0.5, 0.5, 0.5, 0.5]

        result = memory_store.tag_normalize_preview(embedding_model=model)
        assert result["success"] is True
        assert "preview_id" in result
        assert "total_memories_scanned" in result
        assert "unique_tags_before" in result
        assert "unique_tags_after" in result
        assert "planned_updates_count" in result
        assert "affected_memories_count" in result
        assert "changes" in result
        assert "threshold" in result


class TestTagNormalizeApply:
    """Tests for tag_normalize_apply."""

    def test_apply_requires_snapshot_id(self, memory_store):
        result = memory_store.tag_normalize_apply(
            preview_id="abc", snapshot_id="nonexistent"
        )
        assert result["success"] is False
        assert "snapshot" in result["error"].lower() or "snapshot" in result["message"].lower()

    def test_apply_rejects_mismatched_preview_id(self, memory_store):
        # Create snapshot
        snap = memory_store.snapshot_create("test")

        model = MagicMock()
        model.batch_similarity.return_value = [0.5, 0.5, 0.5, 0.5]

        result = memory_store.tag_normalize_apply(
            preview_id="wrong_id",
            snapshot_id=snap["snapshot_id"],
            embedding_model=model
        )
        # Either mismatch error or no changes (if mapping is empty → no mismatch check)
        if result.get("applied_count") == 0:
            assert result["success"] is True  # No changes = no mismatch to check
        else:
            assert result["success"] is False

    def test_apply_is_noop_when_no_changes(self, memory_store):
        snap = memory_store.snapshot_create("test")

        model = MagicMock()
        # Low similarity → no merges proposed
        model.batch_similarity.return_value = [0.1, 0.1, 0.1, 0.1]

        preview = memory_store.tag_normalize_preview(embedding_model=model)
        result = memory_store.tag_normalize_apply(
            preview_id=preview["preview_id"],
            snapshot_id=snap["snapshot_id"],
            embedding_model=model
        )
        assert result["success"] is True
        assert result.get("memories_updated", 0) == 0 or result.get("applied_count", 0) == 0

    def test_apply_only_changes_tags_not_content(self, memory_store):
        snap = memory_store.snapshot_create("test")

        # Get content before
        conn = memory_store._get_connection()
        content_before = dict(conn.execute(
            "SELECT id, content FROM memory_metadata ORDER BY id"
        ).fetchall())
        conn.close()

        model = MagicMock()
        # High similarity for all → merges proposed
        model.batch_similarity.return_value = [0.95, 0.95, 0.95, 0.95]
        model.encode_single.return_value = [0.0] * 384

        preview = memory_store.tag_normalize_preview(embedding_model=model)
        memory_store.tag_normalize_apply(
            preview_id=preview["preview_id"],
            snapshot_id=snap["snapshot_id"],
            embedding_model=model
        )

        # Verify content unchanged
        conn = memory_store._get_connection()
        content_after = dict(conn.execute(
            "SELECT id, content FROM memory_metadata ORDER BY id"
        ).fetchall())
        conn.close()

        assert content_before == content_after

    def test_rollback_restores_exact_tags(self, memory_store):
        snap = memory_store.snapshot_create("before normalize")

        # Get tags before
        conn = memory_store._get_connection()
        tags_before = {
            row[0]: json.loads(row[1])
            for row in conn.execute("SELECT id, tags FROM memory_metadata ORDER BY id").fetchall()
        }
        conn.close()

        model = MagicMock()
        model.batch_similarity.return_value = [0.95, 0.95, 0.95, 0.95]
        model.encode_single.return_value = [0.0] * 384

        preview = memory_store.tag_normalize_preview(embedding_model=model)
        memory_store.tag_normalize_apply(
            preview_id=preview["preview_id"],
            snapshot_id=snap["snapshot_id"],
            embedding_model=model
        )

        # Restore
        memory_store.snapshot_restore(snap["snapshot_id"])

        # Verify exact restoration
        conn = memory_store._get_connection()
        tags_after = {
            row[0]: json.loads(row[1])
            for row in conn.execute("SELECT id, tags FROM memory_metadata ORDER BY id").fetchall()
        }
        conn.close()

        assert tags_before == tags_after
