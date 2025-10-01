"""
Memory Store Module
===================

Provides SQLite-vec based vector storage and retrieval operations.
Handles database initialization, memory storage, search, and management.
"""

import sqlite3
import sqlite_vec
import json
import os
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import List, Optional, Dict, Any, Tuple

from .models import MemoryEntry, MemoryCategory, SearchResult, MemoryStats, Config
from .security import (
    SecurityError, sanitize_input, validate_tags, validate_category,
    validate_search_params, validate_cleanup_params, generate_content_hash,
    check_resource_limits, validate_file_path
)
from .embeddings import get_embedding_model


class VectorMemoryStore:
    """
    Thread-safe vector memory storage using sqlite-vec.
    """
    
    def __init__(self, db_path: Path, embedding_model_name: str = None):
        """
        Initialize vector memory store.
        
        Args:
            db_path: Path to SQLite database file
            embedding_model_name: Name of embedding model to use
        """
        self.db_path = Path(db_path)
        self.embedding_model_name = embedding_model_name or Config.EMBEDDING_MODEL
        
        # Validate database path
        validate_file_path(self.db_path)
        
        # Initialize database and embedding model
        self._init_database()
        self.embedding_model = get_embedding_model(self.embedding_model_name)
    
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
        # Enable WAL mode for safe concurrent access
        conn.execute("PRAGMA journal_mode=WAL")
        conn.enable_load_extension(False)
        return conn
    
    def store_memory(self, content: str, category: str, tags: List[str]) -> Dict[str, Any]:
        """
        Store a new memory with vector embedding.
        
        Args:
            content: Memory content
            category: Memory category
            tags: List of tags
            
        Returns:
            Dict with operation result and metadata
        """
        # Input validation
        content = sanitize_input(content)
        category = validate_category(category)
        tags = validate_tags(tags)
        
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
            if count >= Config.MAX_TOTAL_MEMORIES:
                return {
                    "success": False,
                    "message": f"Memory limit reached ({count}). Use clear_old_memories to free space.",
                    "memory_id": None
                }
            
            # Generate embedding
            embedding = self.embedding_model.encode_single(content)
            
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
    
    def search_memories(self, query: str, limit: int = 10, category: Optional[str] = None) -> List[SearchResult]:
        """
        Search memories using vector similarity.
        
        Args:
            query: Search query
            limit: Maximum number of results
            category: Optional category filter
            
        Returns:
            List of SearchResult objects
        """
        query, limit, category = validate_search_params(query, limit, category)
        
        try:
            conn = self._get_connection()
        except Exception as e:
            raise RuntimeError(f"Failed to store memory: {e}")
        
        try:
            # Generate query embedding
            query_embedding = self.embedding_model.encode_single(query)
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
            
            if category:
                base_query += " WHERE m.category = ?"
                params.append(category)
            
            base_query += " ORDER BY distance LIMIT ?"
            params.append(limit)
            
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
            
            return search_results
            
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
            usage_pct = (total_memories / Config.MAX_TOTAL_MEMORIES) * 100
            if usage_pct < 70:
                health_status = "Healthy"
            elif usage_pct < 90:
                health_status = "Monitor - Consider cleanup"
            else:
                health_status = "Warning - Near limit"
            
            stats = MemoryStats(
                total_memories=total_memories,
                memory_limit=Config.MAX_TOTAL_MEMORIES,
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
