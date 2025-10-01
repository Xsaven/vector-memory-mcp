"""
Vector Memory MCP Server - Core Package
=======================================

This package provides vector-based memory capabilities for Claude Desktop
using sqlite-vec and sentence-transformers.

Modules:
    models: Data models and type definitions
    security: Security utilities and validation
    embeddings: Sentence transformer wrapper (requires sentence-transformers)
    memory_store: SQLite-vec operations and storage (requires sqlite-vec)
"""

__version__ = "1.0.0"
__author__ = "Vector Memory MCP Server"

# Core modules that don't require external dependencies
from .models import MemoryEntry, MemoryCategory, SearchResult, Config
from .security import SecurityError, validate_working_dir, sanitize_input

# Optional imports that require external dependencies
# These are imported lazily to avoid import errors when dependencies aren't available

def get_embedding_model():
    """Get embedding model (requires sentence-transformers)"""
    from .embeddings import EmbeddingModel
    return EmbeddingModel

def get_vector_memory_store():
    """Get vector memory store (requires sqlite-vec)"""
    from .memory_store import VectorMemoryStore
    return VectorMemoryStore

__all__ = [
    "MemoryEntry",
    "MemoryCategory", 
    "SearchResult",
    "Config",
    "SecurityError",
    "validate_working_dir",
    "sanitize_input",
    "get_embedding_model",
    "get_vector_memory_store"
]
