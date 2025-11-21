"""
Data Models and Type Definitions
================================

Defines the core data structures used throughout the vector memory system.
"""

from dataclasses import dataclass
from datetime import datetime
from enum import Enum
from typing import List, Optional, Dict, Any
import json


class MemoryCategory(Enum):
    """Memory categories for better organization"""
    CODE_SOLUTION = "code-solution"
    BUG_FIX = "bug-fix"
    ARCHITECTURE = "architecture"
    LEARNING = "learning"
    TOOL_USAGE = "tool-usage"
    DEBUGGING = "debugging"
    PERFORMANCE = "performance"
    SECURITY = "security"
    OTHER = "other"

    @classmethod
    def list_values(cls) -> List[str]:
        """Get list of all category values"""
        return [category.value for category in cls]

    @classmethod
    def is_valid(cls, value: str) -> bool:
        """Check if a value is a valid category"""
        return value in cls.list_values()


@dataclass
class MemoryEntry:
    """Represents a stored memory entry"""
    id: Optional[int] = None
    content: str = ""
    category: str = MemoryCategory.OTHER.value
    tags: List[str] = None
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    access_count: int = 0
    content_hash: Optional[str] = None
    
    def __post_init__(self):
        """Initialize default values"""
        if self.tags is None:
            self.tags = []
        if self.created_at is None:
            self.created_at = datetime.utcnow()
        if self.updated_at is None:
            self.updated_at = datetime.utcnow()
    
    def to_dict(self) -> Dict[str, Any]:
        """Convert to dictionary for JSON serialization"""
        return {
            "id": self.id,
            "content": self.content,
            "category": self.category,
            "tags": self.tags,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
            "access_count": self.access_count,
            "content_hash": self.content_hash
        }
    
    @classmethod
    def from_db_row(cls, row: tuple) -> 'MemoryEntry':
        """Create MemoryEntry from database row"""
        return cls(
            id=row[0],
            content=row[1],
            category=row[2],
            tags=json.loads(row[3]) if row[3] else [],
            created_at=datetime.fromisoformat(row[4]) if row[4] else None,
            updated_at=datetime.fromisoformat(row[5]) if row[5] else None,
            access_count=row[6] if len(row) > 6 else 0,
            content_hash=row[7] if len(row) > 7 else None
        )


@dataclass
class SearchResult:
    """Represents a search result with similarity scoring"""
    memory: MemoryEntry
    similarity: float
    distance: float

    def to_dict(self) -> Dict[str, Any]:
        """Convert to dictionary for JSON serialization"""
        result = self.memory.to_dict()
        result["similarity"] = round(self.similarity, 3)
        result["distance"] = round(self.distance, 3)
        return result


@dataclass
class MemoryStats:
    """Database statistics and health information"""
    total_memories: int = 0
    memory_limit: int = 10000
    categories: Dict[str, int] = None
    recent_week_count: int = 0
    database_size_mb: float = 0.0
    embedding_model: str = ""
    embedding_dimensions: int = 384
    top_accessed: List[Dict[str, Any]] = None
    health_status: str = "Unknown"

    def __post_init__(self):
        """Initialize default values"""
        if self.categories is None:
            self.categories = {}
        if self.top_accessed is None:
            self.top_accessed = []

    @property
    def usage_percentage(self) -> float:
        """Calculate usage percentage"""
        if self.memory_limit == 0:
            return 0.0
        return round((self.total_memories / self.memory_limit) * 100, 1)

    def to_dict(self) -> Dict[str, Any]:
        """Convert to dictionary for JSON serialization"""
        return {
            "total_memories": self.total_memories,
            "memory_limit": self.memory_limit,
            "usage_percentage": self.usage_percentage,
            "categories": self.categories,
            "recent_week_count": self.recent_week_count,
            "database_size_mb": self.database_size_mb,
            "embedding_model": self.embedding_model,
            "embedding_dimensions": self.embedding_dimensions,
            "top_accessed": self.top_accessed,
            "health_status": self.health_status
        }


# Configuration constants
class Config:
    """Configuration constants"""
    
    # Server configuration
    SERVER_NAME = "Vector Memory MCP Server"
    SERVER_VERSION = "1.0.0"
    
    # Security limits
    MAX_MEMORY_LENGTH = 10000
    MAX_MEMORIES_PER_SEARCH = 50
    MAX_TOTAL_MEMORIES = 10000
    MAX_TAG_LENGTH = 100
    MAX_TAGS_PER_MEMORY = 10

    # Database configuration
    DB_NAME = "vector_memory.db"
    EMBEDDING_MODEL = "sentence-transformers/all-MiniLM-L6-v2"
    EMBEDDING_DIM = 384
    
    # Memory categories
    MEMORY_CATEGORIES = MemoryCategory.list_values()


@dataclass
class SimilarityScoring:
    """Similarity scoring interpretation ranges.

    Scores are calculated as: similarity = 1 - cosine_distance
    where cosine_distance is in range [0, 2] and similarity in [0, 1].
    """

    # Thresholds
    EXTREMELY_RELEVANT = 0.9  # Almost exact matches
    HIGHLY_RELEVANT = 0.8     # Strong semantic similarity
    MODERATELY_RELEVANT = 0.7 # Good contextual match
    SOMEWHAT_RELEVANT = 0.6   # Might be useful

    @staticmethod
    def interpret(similarity: float) -> str:
        """Interpret similarity score with human-readable description.

        Args:
            similarity: Cosine similarity score (0-1, higher = better)

        Returns:
            Human-readable interpretation string
        """
        if similarity >= 0.9:
            return "Extremely relevant - almost exact match"
        elif similarity >= 0.8:
            return "Highly relevant - strong semantic similarity"
        elif similarity >= 0.7:
            return "Moderately relevant - good contextual match"
        elif similarity >= 0.6:
            return "Somewhat relevant - might be useful"
        else:
            return "Low relevance - probably not helpful"
