"""
Memory Store Module
===================

Provides SQLite-vec based vector storage and retrieval operations.
Handles database initialization, memory storage, search, and management.
"""

import asyncio
import sqlite3
import sqlite_vec
import json
import os
import re
import numpy as np
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import List, Optional, Dict, Any, Tuple, Set

from .models import MemoryEntry, MemoryCategory, SearchResult, MemoryStats, Config
from .security import (
    SecurityError, sanitize_input, validate_tags, validate_category,
    validate_search_params, validate_cleanup_params, generate_content_hash,
    check_resource_limits, validate_file_path
)
from .embeddings import get_embedding_model, EmbeddingModel


def _normalize_tag_for_embedding(tag: str) -> str:
    """
    Normalize tag for embedding comparison.
    
    - lowercase
    - replace _ - with space
    - normalize version prefixes (version/ver -> v)
    - add space after v before digit (v2 -> v 2)
    """
    tag = tag.lower()
    tag = re.sub(r'[-_]+', ' ', tag)
    tag = re.sub(r'\bversion\b', 'v', tag)
    tag = re.sub(r'\bver\b', 'v', tag)
    tag = re.sub(r'\bv(\d)', r'v \1', tag)
    tag = ' '.join(tag.split())
    return tag


def _extract_version(tag: str) -> Optional[str]:
    """
    Extract version number from tag.
    
    Matches: v1, v2.0, v1.2.3, version 2, ver 3.0
    Returns normalized version like '1', '2.0', '1.2.3' or None
    """
    tag_lower = tag.lower()
    tag_lower = re.sub(r'[-_]+', ' ', tag_lower)
    
    patterns = [
        r'\bv\s*(\d+(?:\.\d+)*)',
        r'\bversion\s+(\d+(?:\.\d+)*)',
        r'\bver\s+(\d+(?:\.\d+)*)',
        r'\bapi\s+(\d+(?:\.\d+)*)',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, tag_lower)
        if match:
            return _normalize_version_number(match.group(1))
    return None


def _normalize_version_number(version: str) -> str:
    """Normalize version number: 2 -> 2.0, 2.0 -> 2.0, 01 -> 1"""
    parts = version.split('.')
    parts = [str(int(p)) for p in parts]
    if len(parts) == 1:
        parts.append('0')
    return '.'.join(parts)


def _extract_numbers(tag: str) -> Set[str]:
    """
    Extract all numbers from tag.
    
    Returns set of normalized numbers as strings.
    """
    tag_lower = tag.lower()
    tag_lower = re.sub(r'[-_]+', ' ', tag_lower)
    numbers = re.findall(r'\b(\d+(?:\.\d+)?)\b', tag_lower)
    return {_normalize_version_number(n) for n in numbers}


def _split_colon_tag(tag: str) -> Tuple[Optional[str], Optional[str]]:
    """
    Split tag by colon into prefix and suffix.
    
    Args:
        tag: Tag string (e.g., "type:refactor")
        
    Returns:
        Tuple of (prefix, suffix) or (None, None) if no colon
    """
    if ':' in tag:
        parts = tag.split(':', 1)
        if len(parts) == 2:
            return parts[0].lower().strip(), parts[1].lower().strip()
    return None, None


def _can_merge_tags(tag1: str, tag2: str, similarity: float) -> bool:
    """
    Check if two tags can be merged based on semantic similarity and guards.
    
    Guards:
    - Version guard: different versions never merge
    - Colon guard: same prefix, different suffix → NO MERGE
    - Prefix guard: structured vs plain → NO MERGE  
    - Number guard: different numbers rarely merge
    - Substring boost: if one tag is subset of other (with restrictions)
    """
    v1 = _extract_version(tag1)
    v2 = _extract_version(tag2)
    
    # Different versions: never merge
    if v1 is not None and v2 is not None and v1 != v2:
        return False
    
    t1_lower = tag1.lower()
    t2_lower = tag2.lower()
    
    # Colon guard: extract prefix:suffix
    prefix1, suffix1 = _split_colon_tag(t1_lower)
    prefix2, suffix2 = _split_colon_tag(t2_lower)
    
    # Same prefix, different suffix -> NO MERGE (type:refactor vs type:bug)
    if prefix1 and prefix2 and prefix1 == prefix2 and suffix1 != suffix2:
        return False
    
    # Prefix asymmetry guard: structured vs plain -> NO MERGE (type:refactor vs refactor)
    if (prefix1 and not prefix2) or (prefix2 and not prefix1):
        return False
    
    # Substring boost for non-versioned, non-structured tags
    if v1 is None and v2 is None and prefix1 is None and prefix2 is None:
        nums1 = _extract_numbers(tag1)
        nums2 = _extract_numbers(tag2)
        
        if not nums1 and not nums2:
            words1 = set(t1_lower.split())
            words2 = set(t2_lower.split())
            
            if words1 and words2 and (words1 < words2 or words2 < words1):
                # Find shorter tag
                shorter = t1_lower if len(t1_lower) < len(t2_lower) else t2_lower
                shorter_word = list(words1 if words1 < words2 else words2)[0]
                
                # Check restrictions for substring boost
                can_boost = True
                
                # Min length check
                if len(shorter_word) < Config.TAG_SUBSTRING_MIN_LENGTH:
                    can_boost = False
                
                # Stop-words check
                if shorter_word in Config.TAG_SUBSTRING_STOP_WORDS:
                    can_boost = False
                
                if can_boost:
                    similarity = min(1.0, similarity + Config.TAG_SUBSTRING_BOOST)
    
    # Same version (or no version): use lower threshold
    if v1 is not None and v2 is not None and v1 == v2:
        threshold = Config.TAG_RELATED_THRESHOLD
    else:
        threshold = Config.TAG_SIMILARITY_THRESHOLD
    
    if similarity < threshold:
        return False
    
    # Check number guard for non-versioned tags
    if v1 is None and v2 is None:
        nums1 = _extract_numbers(tag1)
        nums2 = _extract_numbers(tag2)
        
        if nums1 and nums2 and nums1 != nums2:
            if similarity < 0.95:
                return False
    
    return True


class VectorMemoryStore:
    """
    Thread-safe vector memory storage using sqlite-vec.
    """
    
    # Pre-computed canonical category embeddings (set on first use)
    _canonical_categories_embeddings: Optional[Dict[str, List[float]]] = None
    
    def __init__(self, db_path: Path, embedding_model_name: str = None, memory_limit: int = None):
        """
        Initialize vector memory store.

        Args:
            db_path: Path to SQLite database file
            embedding_model_name: Name of embedding model to use
            memory_limit: Maximum number of memories to store (default from Config)
        """
        self.db_path = Path(db_path)
        self.embedding_model_name = embedding_model_name or Config.EMBEDDING_MODEL
        self.memory_limit = memory_limit or Config.MAX_TOTAL_MEMORIES

        # Validate database path
        validate_file_path(self.db_path)

        # Lazy-loaded embedding model (async initialization)
        self._embedding_model: EmbeddingModel | None = None
        self._model_loading_task: asyncio.Task | None = None

        # Lazy-loaded database initialization (async)
        self._db_initialized: bool = False
        self._db_init_task: asyncio.Task | None = None

    async def _ensure_db_initialized_async(self) -> None:
        """
        Ensure database is initialized with async lazy loading.

        Creates asyncio.Task on first call for background initialization.
        All concurrent callers await the SAME task (no duplicate initialization).
        """
        if self._db_initialized:
            return

        if self._db_init_task is None:
            self._db_init_task = asyncio.create_task(
                asyncio.to_thread(self._init_database)
            )

        await self._db_init_task
        self._db_initialized = True

    def _ensure_db_initialized_sync(self) -> None:
        """
        Ensure database is initialized with synchronous loading (fallback for non-async contexts).

        Blocks if database not yet initialized (synchronous fallback).
        """
        if not self._db_initialized:
            self._init_database()
            self._db_initialized = True

    async def get_embedding_model_async(self) -> EmbeddingModel:
        """
        Get embedding model with async lazy loading.

        Returns cached model if already loaded.
        Creates asyncio.Task on first call for background loading.
        All concurrent callers await the SAME task (no duplicate loading).

        Returns:
            EmbeddingModel instance
        """
        if self._embedding_model is not None:
            return self._embedding_model

        if self._model_loading_task is None:
            self._model_loading_task = asyncio.create_task(
                asyncio.to_thread(get_embedding_model, self.embedding_model_name)
            )

        self._embedding_model = await self._model_loading_task
        return self._embedding_model

    def _get_embedding_model_sync(self) -> EmbeddingModel:
        """
        Get embedding model with synchronous loading (fallback for non-async contexts).

        Returns cached model if already loaded.
        Blocks if model not yet loaded (synchronous fallback).

        Returns:
            EmbeddingModel instance
        """
        if self._embedding_model is None:
            self._embedding_model = get_embedding_model(self.embedding_model_name)
        return self._embedding_model

    @property
    def embedding_model(self) -> EmbeddingModel:
        """
        Property for backwards compatibility.

        Provides synchronous access to embedding model.
        Use get_embedding_model_async() for async contexts.

        Returns:
            EmbeddingModel instance
        """
        return self._get_embedding_model_sync()

    def _init_database(self) -> None:
        """Initialize sqlite-vec database with required tables."""
        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            # Create metadata table
            conn.execute("""
                CREATE TABLE IF NOT EXISTS memory_metadata (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    content_hash TEXT UNIQUE NOT NULL,
                    content TEXT NOT NULL,
                    category TEXT NOT NULL,
                    tags TEXT NOT NULL,  -- JSON array
                    created_at TEXT NOT NULL,
                    updated_at TEXT NOT NULL,
                    access_count INTEGER DEFAULT 0
                )
            """)
            
            # Create vector table using vec0
            conn.execute(f"""
                CREATE VIRTUAL TABLE IF NOT EXISTS memory_vectors USING vec0(
                    embedding float[{Config.EMBEDDING_DIM}]
                );
            """)
            
            # Create canonical tags table for semantic normalization
            conn.execute("""
                CREATE TABLE IF NOT EXISTS canonical_tags (
                    tag TEXT PRIMARY KEY,
                    embedding BLOB NOT NULL,
                    frequency INTEGER DEFAULT 1,
                    created_at TEXT NOT NULL
                )
            """)

            # Migration: add frequency column if not exists (backward compatible)
            try:
                conn.execute("ALTER TABLE canonical_tags ADD COLUMN frequency INTEGER DEFAULT 1")
            except sqlite3.OperationalError:
                pass  # Column already exists

            # Create indexes for performance
            conn.execute("CREATE INDEX IF NOT EXISTS idx_category ON memory_metadata(category)")
            conn.execute("CREATE INDEX IF NOT EXISTS idx_created_at ON memory_metadata(created_at)")
            conn.execute("CREATE INDEX IF NOT EXISTS idx_hash ON memory_metadata(content_hash)")
            conn.execute("CREATE INDEX IF NOT EXISTS idx_access_count ON memory_metadata(access_count)")
            
            conn.commit()
            
        except Exception as e:
            conn.rollback()
            raise RuntimeError(f"Failed to initialize database: {e}")
        finally:
            conn.close()
    
    def _get_connection(self) -> sqlite3.Connection:
        """Get SQLite connection with sqlite-vec loaded."""
        conn = sqlite3.connect(str(self.db_path))
        conn.enable_load_extension(True)
        sqlite_vec.load(conn)
        conn.enable_load_extension(False)
        return conn

    def _get_canonical_tags(self, conn: sqlite3.Connection) -> Dict[str, List[float]]:
        """
        Load all canonical tags with their embeddings.

        Args:
            conn: Database connection

        Returns:
            Dict mapping tag string to embedding vector
        """
        results = conn.execute("SELECT tag, embedding FROM canonical_tags").fetchall()
        return {
            row[0]: np.frombuffer(row[1], dtype=np.float32).tolist()
            for row in results
        }

    def _add_canonical_tag(
        self, conn: sqlite3.Connection, tag: str, embedding: List[float]
    ) -> None:
        """
        Add a new canonical tag with frequency=1.

        Args:
            conn: Database connection
            tag: Canonical tag string
            embedding: Tag embedding vector
        """
        now = datetime.now(timezone.utc).isoformat()
        embedding_blob = sqlite_vec.serialize_float32(embedding)
        conn.execute(
            "INSERT OR IGNORE INTO canonical_tags (tag, embedding, frequency, created_at) VALUES (?, ?, 1, ?)",
            (tag, embedding_blob, now)
        )

    def _increment_tag_frequency(self, conn: sqlite3.Connection, tag: str) -> None:
        """
        Increment frequency for an existing canonical tag.

        Args:
            conn: Database connection
            tag: Canonical tag string
        """
        conn.execute(
            "UPDATE canonical_tags SET frequency = frequency + 1 WHERE tag = ?",
            (tag,)
        )

    def _get_tag_weights(self, conn: sqlite3.Connection) -> Dict[str, float]:
        """
        Get IDF-based weights for all canonical tags.
        
        Weight formula: 1 / log(1 + frequency)
        - High frequency tags (api, auth) → lower weight
        - Rare tags (module:terminal) → higher weight

        Args:
            conn: Database connection

        Returns:
            Dict mapping tag to weight (0.0 - 1.0)
        """
        results = conn.execute("SELECT tag, frequency FROM canonical_tags").fetchall()
        return {row[0]: 1.0 / np.log(1 + row[1]) for row in results}

    def _normalize_tags_semantic(
        self, tags: List[str], model: EmbeddingModel, conn: sqlite3.Connection
    ) -> List[str]:
        """
        Normalize tags using semantic similarity to canonical tags with guards.

        Guards prevent merging of:
        - Different versions (api v1 vs api v2)
        - Different numbers (unless very high similarity >= 0.95)

        For each tag:
        1. Normalize for embedding comparison
        2. Find best matching canonical tag (with guards)
        3. If mergeable -> use canonical tag, increment frequency
        4. If not -> add as new canonical tag

        Args:
            tags: List of tags to normalize
            model: Embedding model
            conn: Database connection

        Returns:
            List of normalized canonical tags
        """
        if not tags:
            return []

        # Load existing canonical tags from DB
        canonical_tags = self._get_canonical_tags(conn)
        normalized = []
        incremented = set()  # Track which tags were incremented in this batch

        for tag in tags:
            if not tag or not tag.strip():
                continue

            tag_lower = tag.strip().lower()

            # Exact match in canonical tags
            if tag_lower in canonical_tags:
                if tag_lower not in normalized:
                    normalized.append(tag_lower)
                    # Increment frequency once per unique tag in this memory
                    if tag_lower not in incremented:
                        self._increment_tag_frequency(conn, tag_lower)
                        incremented.add(tag_lower)
                continue

            # Normalize for embedding comparison
            tag_normalized = _normalize_tag_for_embedding(tag_lower)
            tag_embedding = model.encode_single(tag_normalized)

            # Find best matching canonical tag (with guards)
            best_match = None
            best_similarity = 0.0

            if canonical_tags:
                canonical_tag_list = list(canonical_tags.keys())
                similarities = model.batch_similarity(tag_normalized, canonical_tag_list)

                for i, sim in enumerate(similarities):
                    canonical_tag = canonical_tag_list[i]
                    if _can_merge_tags(tag_normalized, _normalize_tag_for_embedding(canonical_tag), sim):
                        if sim > best_similarity:
                            best_similarity = sim
                            best_match = canonical_tag

            if best_match:
                # Found a mergeable match
                if best_match not in normalized:
                    normalized.append(best_match)
                    # Increment frequency for merged canonical tag
                    if best_match not in incremented:
                        self._increment_tag_frequency(conn, best_match)
                        incremented.add(best_match)
            else:
                # No match found -> add as new canonical tag (frequency=1 by default)
                self._add_canonical_tag(conn, tag_lower, tag_embedding)
                # Update in-memory cache for subsequent tags in this batch
                canonical_tags[tag_lower] = tag_embedding
                if tag_lower not in normalized:
                    normalized.append(tag_lower)

        return normalized

    def _get_canonical_categories_embeddings(self, model: EmbeddingModel) -> Dict[str, List[float]]:
        """
        Get pre-computed embeddings for all canonical categories.
        
        Computes once and caches for subsequent calls.
        
        Args:
            model: Embedding model
            
        Returns:
            Dict mapping canonical category to embedding
        """
        if VectorMemoryStore._canonical_categories_embeddings is None:
            categories = Config.MEMORY_CATEGORIES
            embeddings = {}
            
            # Create human-readable forms for better embedding
            category_labels = {
                'code-solution': 'code solution implementation',
                'bug-fix': 'bug fix error correction',
                'architecture': 'architecture design structure',
                'learning': 'learning knowledge discovery',
                'tool-usage': 'tool usage utility',
                'debugging': 'debugging troubleshooting diagnosis',
                'performance': 'performance optimization speed',
                'security': 'security vulnerability protection',
                'other': 'other miscellaneous general'
            }
            
            for cat in categories:
                label = category_labels.get(cat, cat.replace('-', ' '))
                embeddings[cat] = model.encode_single(label)
            
            VectorMemoryStore._canonical_categories_embeddings = embeddings
        
        return VectorMemoryStore._canonical_categories_embeddings

    def _normalize_category_semantic(
        self, category: str, model: EmbeddingModel
    ) -> str:
        """
        Normalize category using semantic similarity to canonical categories.
        
        Strategy:
        - Dictionary fallback for short tokens (< 5 chars)
        - Pick best matching category if similarity >= threshold
        - AND best is significantly better than "other" (margin check)
        
        Args:
            category: Input category string
            model: Embedding model
            
        Returns:
            Canonical category string
        """
        if not isinstance(category, str):
            return 'other'
        
        category_lower = category.lower().strip()
        
        if not category_lower:
            return 'other'
        
        # Exact match
        if category_lower in Config.MEMORY_CATEGORIES:
            return category_lower
        
        # Dictionary fallback for short tokens (embeddings unreliable for < 5 chars)
        SHORT_CATEGORY_ALIASES = {
            'bug': 'bug-fix',
            'fix': 'bug-fix',
            'auth': 'security',
            'sec': 'security',
            'perf': 'performance',
            'opt': 'performance',
            'debug': 'debugging',
            'arch': 'architecture',
            'design': 'architecture',
            'impl': 'code-solution',
            'sol': 'code-solution',
            'learn': 'learning',
            'tool': 'tool-usage',
        }
        
        if len(category_lower) < 5 and category_lower in SHORT_CATEGORY_ALIASES:
            return SHORT_CATEGORY_ALIASES[category_lower]
        
        # Get canonical category embeddings
        canonical_embeddings = self._get_canonical_categories_embeddings(model)
        
        # Compute similarity with all canonical categories
        category_embedding = model.encode_single(category_lower)
        
        similarities = {}
        for canonical_cat, canonical_emb in canonical_embeddings.items():
            similarities[canonical_cat] = float(np.dot(category_embedding, canonical_emb))
        
        # Find best match (excluding "other")
        best_match = None
        best_similarity = 0.0
        
        for cat, sim in similarities.items():
            if cat != 'other' and sim > best_similarity:
                best_similarity = sim
                best_match = cat
        
        other_similarity = similarities.get('other', 0.0)
        
        # Accept if above threshold AND significantly better than "other"
        if (best_match and 
            best_similarity >= Config.CATEGORY_SIMILARITY_THRESHOLD and
            best_similarity >= other_similarity + Config.CATEGORY_MIN_MARGIN):
            return best_match
        
        return 'other'
    
    def store_memory(
        self,
        content: str,
        category: str,
        tags: List[str],
        embedding_model: Optional[EmbeddingModel] = None
    ) -> Dict[str, Any]:
        """
        Store a new memory with vector embedding.

        Args:
            content: Memory content
            category: Memory category
            tags: List of tags
            embedding_model: Optional pre-loaded embedding model (for async contexts)

        Returns:
            Dict with operation result and metadata
        """
        # Input validation
        content = sanitize_input(content)
        tags = validate_tags(tags)

        self._ensure_db_initialized_sync()
        # Use provided model or fall back to sync loading
        model = embedding_model or self._get_embedding_model_sync()
        
        # Semantic category normalization
        category = self._normalize_category_semantic(category, model)

        # Check for duplicates
        content_hash = generate_content_hash(content)
        
        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            # Check if memory already exists
            existing = conn.execute(
                "SELECT id FROM memory_metadata WHERE content_hash = ?",
                (content_hash,)
            ).fetchone()
            
            if existing:
                return {
                    "success": False,
                    "message": "Memory already exists",
                    "memory_id": existing[0]
                }
            
            # Check memory limit
            count = conn.execute("SELECT COUNT(*) FROM memory_metadata").fetchone()[0]
            if count >= self.memory_limit:
                return {
                    "success": False,
                    "message": f"Memory limit reached ({count}/{self.memory_limit}). Use clear_old_memories to free space.",
                    "memory_id": None
                }

            # Semantic tag normalization (after validation, before storage)
            tags = self._normalize_tags_semantic(tags, model, conn)
            
            # Generate embedding
            embedding = model.encode_single(content)
            
            # Store metadata
            now = datetime.now(timezone.utc).isoformat()
            cursor = conn.execute("""
                INSERT INTO memory_metadata (content_hash, content, category, tags, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?)
            """, (content_hash, content, category, json.dumps(tags), now, now))
            
            memory_id = cursor.lastrowid
            
            # Store vector using sqlite-vec serialization
            embedding_blob = sqlite_vec.serialize_float32(embedding)
            conn.execute(
                "INSERT INTO memory_vectors (rowid, embedding) VALUES (?, ?)",
                (memory_id, embedding_blob)
            )
            
            conn.commit()
            
            return {
                "success": True,
                "memory_id": memory_id,
                "content_preview": content[:100] + "..." if len(content) > 100 else content,
                "category": category,
                "tags": tags,
                "created_at": now
            }
            
        except SecurityError as e:
            conn.rollback()
            raise e
        except Exception as e:
            conn.rollback()
            raise RuntimeError(f"Failed to store memory: {e}")
        finally:
            conn.close()
    
    def search_memories(
        self,
        query: str,
        limit: int = 10,
        category: Optional[str] = None,
        offset: int = 0,
        tags: Optional[List[str]] = None,
        embedding_model: Optional[EmbeddingModel] = None
    ) -> Tuple[List[SearchResult], int]:
        """
        Search memories using vector similarity.

        Args:
            query: Search query
            limit: Maximum number of results
            category: Optional category filter
            offset: Number of results to skip for pagination (default: 0)
            tags: Optional list of tags to filter by (matches if ANY tag is present)
            embedding_model: Optional pre-loaded embedding model (for async contexts)

        Returns:
            Tuple of (List of SearchResult objects, total count matching filters)
        """
        query, limit, category = validate_search_params(query, limit, category)

        # Validate offset parameter
        if not isinstance(offset, int) or offset < 0:
            raise ValueError("offset must be a non-negative integer")
        if offset > 10000:
            raise ValueError("offset must not exceed 10000")

        # Validate tags parameter
        if tags is not None:
            if not isinstance(tags, list):
                raise ValueError("tags must be a list of strings")
            tags = [sanitize_input(str(tag)) for tag in tags if tag]
            if not tags:
                tags = None  # Empty list treated as no filter

        self._ensure_db_initialized_sync()
        # Use provided model or fall back to sync loading
        model = embedding_model or self._get_embedding_model_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")

        try:
            # Generate query embedding
            query_embedding = model.encode_single(query)
            query_blob = sqlite_vec.serialize_float32(query_embedding)
            
            # Build search query
            base_query = """
                SELECT
                    m.id, m.content, m.category, m.tags, m.created_at, m.updated_at, m.access_count, m.content_hash,
                    vec_distance_cosine(v.embedding, ?) as distance
                FROM memory_metadata m
                JOIN memory_vectors v ON m.id = v.rowid
            """

            params = [query_blob]
            where_clauses = []

            # Add category filter
            if category:
                where_clauses.append("m.category = ?")
                params.append(category)

            # Add tags filter (match if ANY tag is present)
            if tags:
                # Build OR conditions for each tag using JSON search
                tag_conditions = []
                for tag in tags:
                    # Use json_each to search within JSON array
                    tag_conditions.append("EXISTS (SELECT 1 FROM json_each(m.tags) WHERE value = ?)")
                    params.append(tag)
                where_clauses.append(f"({' OR '.join(tag_conditions)})")

            # Add WHERE clause if filters exist
            if where_clauses:
                base_query += " WHERE " + " AND ".join(where_clauses)

            # Get total count of results matching filters (without limit/offset)
            count_query = """
                SELECT COUNT(DISTINCT m.id)
                FROM memory_metadata m
                JOIN memory_vectors v ON m.id = v.rowid
            """
            if where_clauses:
                count_query += " WHERE " + " AND ".join(where_clauses)

            # Execute count query with same params (excluding query_blob, limit, offset)
            count_params = params[1:] if len(params) > 1 else []  # Skip query_blob
            total_count = conn.execute(count_query, count_params).fetchone()[0]

            # Add ORDER BY, LIMIT, and OFFSET
            base_query += " ORDER BY distance LIMIT ? OFFSET ?"
            params.append(limit)
            params.append(offset)

            results = conn.execute(base_query, params).fetchall()
            
            # Update access counts for returned memories
            if results:
                memory_ids = [str(r[0]) for r in results]
                placeholders = ",".join(["?"] * len(memory_ids))
                conn.execute(f"""
                    UPDATE memory_metadata 
                    SET access_count = access_count + 1,
                        updated_at = ?
                    WHERE id IN ({placeholders})
                """, [datetime.now(timezone.utc).isoformat()] + memory_ids)
                conn.commit()
            
            # Format results
            search_results = []
            for row in results:
                memory = MemoryEntry.from_db_row(row[:-1])  # Exclude distance
                memory.access_count += 1  # Include current access

                distance = row[-1]
                similarity = 1 - distance  # Convert distance to similarity

                search_results.append(SearchResult(
                    memory=memory,
                    similarity=similarity,
                    distance=distance
                ))

            return (search_results, total_count)
            
        except SecurityError as e:
            raise e
        except Exception as e:
            raise RuntimeError(f"Search failed: {e}")
        finally:
            conn.close()
    
    def get_recent_memories(self, limit: int = 10) -> List[MemoryEntry]:
        """
        Get recently stored memories.

        Args:
            limit: Maximum number of memories to return

        Returns:
            List of MemoryEntry objects
        """
        self._ensure_db_initialized_sync()
        limit = min(max(1, limit), Config.MAX_MEMORIES_PER_SEARCH)

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            results = conn.execute("""
                SELECT id, content, category, tags, created_at, updated_at, access_count, content_hash
                FROM memory_metadata
                ORDER BY created_at DESC
                LIMIT ?
            """, (limit,)).fetchall()
            
            memories = [MemoryEntry.from_db_row(row) for row in results]
            return memories
            
        except Exception as e:
            raise RuntimeError(f"Failed to get recent memories: {e}")
        finally:
            conn.close()
    
    def get_stats(self) -> MemoryStats:
        """
        Get database statistics.

        Returns:
            MemoryStats object with comprehensive statistics
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            # Basic counts
            total_memories = conn.execute("SELECT COUNT(*) FROM memory_metadata").fetchone()[0]
            
            # Category breakdown
            categories = dict(conn.execute("""
                SELECT category, COUNT(*) 
                FROM memory_metadata 
                GROUP BY category 
                ORDER BY COUNT(*) DESC
            """).fetchall())
            
            # Recent activity
            week_ago = (datetime.now(timezone.utc) - timedelta(days=7)).isoformat()
            recent_count = conn.execute(
                "SELECT COUNT(*) FROM memory_metadata WHERE created_at > ?",
                (week_ago,)
            ).fetchone()[0]
            
            # Database size
            db_size = os.path.getsize(self.db_path) if self.db_path.exists() else 0
            
            # Most accessed memories
            top_memories = conn.execute("""
                SELECT content, access_count 
                FROM memory_metadata 
                ORDER BY access_count DESC 
                LIMIT 5
            """).fetchall()
            
            # Health status
            usage_pct = (total_memories / self.memory_limit) * 100
            if usage_pct < 70:
                health_status = "Healthy"
            elif usage_pct < 90:
                health_status = "Monitor - Consider cleanup"
            else:
                health_status = "Warning - Near limit"

            stats = MemoryStats(
                total_memories=total_memories,
                memory_limit=self.memory_limit,
                categories=categories,
                recent_week_count=recent_count,
                database_size_mb=round(db_size / 1024 / 1024, 2),
                embedding_model=self.embedding_model_name,
                embedding_dimensions=Config.EMBEDDING_DIM,
                top_accessed=[
                    {
                        "content_preview": content[:100] + "..." if len(content) > 100 else content,
                        "access_count": count
                    }
                    for content, count in top_memories
                ],
                health_status=health_status
            )
            
            return stats
            
        except Exception as e:
            raise RuntimeError(f"Failed to get statistics: {e}")
        finally:
            conn.close()
    
    def clear_old_memories(self, days_old: int = 30, max_to_keep: int = 1000) -> Dict[str, Any]:
        """
        Clear old, less accessed memories.

        Args:
            days_old: Minimum age for cleanup candidates
            max_to_keep: Maximum total memories to keep

        Returns:
            Dict with cleanup results
        """
        days_old, max_to_keep = validate_cleanup_params(days_old, max_to_keep)

        self._ensure_db_initialized_sync()

        cutoff_date = (datetime.now(timezone.utc) - timedelta(days=days_old)).isoformat()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            # Find candidates for deletion (old + low access)
            candidates = conn.execute("""
                SELECT id 
                FROM memory_metadata 
                WHERE created_at < ? 
                ORDER BY access_count ASC, created_at ASC
            """, (cutoff_date,)).fetchall()
            
            total_count = conn.execute("SELECT COUNT(*) FROM memory_metadata").fetchone()[0]
            
            # Determine how many to delete
            to_delete_count = max(0, min(len(candidates), total_count - max_to_keep))
            
            if to_delete_count == 0:
                return {
                    "success": True,
                    "deleted_count": 0,
                    "message": "No memories need to be deleted"
                }
            
            # Get IDs to delete
            delete_ids = [str(row[0]) for row in candidates[:to_delete_count]]
            placeholders = ",".join(["?"] * len(delete_ids))
            
            # Delete from both tables
            conn.execute(f"DELETE FROM memory_metadata WHERE id IN ({placeholders})", delete_ids)
            conn.execute(f"DELETE FROM memory_vectors WHERE rowid IN ({placeholders})", delete_ids)
            
            conn.commit()
            
            return {
                "success": True,
                "deleted_count": to_delete_count,
                "remaining_count": total_count - to_delete_count,
                "message": f"Deleted {to_delete_count} old memories"
            }
            
        except SecurityError as e:
            conn.rollback()
            raise e
        except Exception as e:
            conn.rollback()
            raise RuntimeError(f"Failed to clear old memories: {e}")
        finally:
            conn.close()
    
    def get_memory_by_id(self, memory_id: int) -> Optional[MemoryEntry]:
        """
        Get a specific memory by ID.

        Args:
            memory_id: Memory ID to retrieve

        Returns:
            MemoryEntry object or None if not found
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            result = conn.execute("""
                SELECT id, content, category, tags, created_at, updated_at, access_count, content_hash
                FROM memory_metadata
                WHERE id = ?
            """, (memory_id,)).fetchone()
            
            if result:
                return MemoryEntry.from_db_row(result)
            return None
            
        except Exception as e:
            raise RuntimeError(f"Failed to get memory by ID: {e}")
        finally:
            conn.close()
    
    def delete_memory(self, memory_id: int) -> bool:
        """
        Delete a specific memory by ID.

        Args:
            memory_id: Memory ID to delete

        Returns:
            bool: True if deleted, False if not found
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            # Check if memory exists
            exists = conn.execute(
                "SELECT 1 FROM memory_metadata WHERE id = ?",
                (memory_id,)
            ).fetchone()
            
            if not exists:
                return False
            
            # Delete from both tables
            conn.execute("DELETE FROM memory_metadata WHERE id = ?", (memory_id,))
            conn.execute("DELETE FROM memory_vectors WHERE rowid = ?", (memory_id,))
            
            conn.commit()
            return True
            
        except Exception as e:
            conn.rollback()
            raise RuntimeError(f"Failed to delete memory: {e}")
        finally:
            conn.close()

    def get_unique_tags(self) -> List[str]:
        """
        Get all unique tags from the database.

        Returns:
            List of unique tag strings sorted alphabetically
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to get unique tags: {e}")

        try:
            # Get all tags from memory_metadata
            results = conn.execute("SELECT tags FROM memory_metadata").fetchall()

            # Collect unique tags
            unique_tags = set()
            for row in results:
                tags_json = row[0]
                try:
                    tags_list = json.loads(tags_json)
                    if isinstance(tags_list, list):
                        unique_tags.update(tags_list)
                except json.JSONDecodeError:
                    continue

            # Return sorted list
            return sorted(list(unique_tags))

        except Exception as e:
            raise RuntimeError(f"Failed to get unique tags: {e}")
        finally:
            conn.close()

    def get_canonical_tags(self) -> List[str]:
        """
        Get all canonical tags from the database.

        Returns:
            List of canonical tag strings sorted alphabetically
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to get canonical tags: {e}")

        try:
            results = conn.execute(
                "SELECT tag FROM canonical_tags ORDER BY tag"
            ).fetchall()
            return [row[0] for row in results]

        except Exception as e:
            raise RuntimeError(f"Failed to get canonical tags: {e}")
        finally:
            conn.close()

    def get_tag_frequencies(self) -> Dict[str, int]:
        """
        Get frequency count for all canonical tags.

        Returns:
            Dict mapping tag to frequency count
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to get tag frequencies: {e}")

        try:
            results = conn.execute(
                "SELECT tag, frequency FROM canonical_tags ORDER BY frequency DESC"
            ).fetchall()
            return {row[0]: row[1] for row in results}

        except Exception as e:
            raise RuntimeError(f"Failed to get tag frequencies: {e}")
        finally:
            conn.close()

    def get_tag_weights(self) -> Dict[str, float]:
        """
        Get IDF-based weights for all canonical tags.
        
        Weight formula: 1 / log(1 + frequency)
        - High frequency tags (common) → lower weight (less discriminative)
        - Low frequency tags (rare) → higher weight (more discriminative)

        Returns:
            Dict mapping tag to IDF weight
        """
        self._ensure_db_initialized_sync()

        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to get tag weights: {e}")

        try:
            return self._get_tag_weights(conn)

        except Exception as e:
            raise RuntimeError(f"Failed to get tag weights: {e}")
        finally:
            conn.close()
