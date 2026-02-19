"""
Security Utilities
==================

Provides security validation, input sanitization, and path validation
for the vector memory MCP server.
"""

import re
import os
from pathlib import Path
from typing import List

from .models import Config


class SecurityError(Exception):
    """Raised when security validation fails"""
    pass


def validate_working_dir(working_dir: str) -> Path:
    """
    Validate and normalize working directory path.
    
    Args:
        working_dir: Directory path to validate
        
    Returns:
        Path: Validated memory directory path
        
    Raises:
        SecurityError: If validation fails
    """
    try:
        # Normalize path
        path = Path(working_dir).resolve()
        
        # Security checks
        path_str = str(path)
        if re.search(r'[;&|`$]', path_str):
            raise SecurityError("Invalid characters in path")
        
        # Check for null bytes/control chars
        if re.search(r'[\x00-\x1F\x7F]', path_str):
            raise SecurityError("Control characters in path")
        
        # Ensure directory exists or can be created
        path.mkdir(parents=True, exist_ok=True)
        
        # Create memory subdirectory
        memory_dir = path / "memory"
        memory_dir.mkdir(exist_ok=True)
        
        return memory_dir
        
    except PermissionError as e:
        raise SecurityError(f"Permission denied: {e}")
    except OSError as e:
        raise SecurityError(f"Invalid path: {e}")
    except Exception as e:
        raise SecurityError(f"Path validation failed: {e}")


def sanitize_input(text: str, max_length: int = None) -> str:
    """
    Sanitize and validate user input.
    
    Args:
        text: Input text to sanitize
        max_length: Maximum allowed length (defaults to Config.MAX_MEMORY_LENGTH)
        
    Returns:
        str: Sanitized text
        
    Raises:
        SecurityError: If validation fails
    """
    if max_length is None:
        max_length = Config.MAX_MEMORY_LENGTH
        
    if not isinstance(text, str):
        raise SecurityError("Input must be a string")
    
    # Remove null bytes and control characters (except newlines/tabs)
    text = re.sub(r'[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]', '', text)
    
    # Limit length
    if len(text) > max_length:
        text = text[:max_length]
    
    # Basic validation
    if not text.strip():
        raise SecurityError("Input cannot be empty")
    
    return text.strip()


def validate_tags(tags: List[str]) -> List[str]:
    """
    Validate and sanitize tags.

    Tags are pre-validated here for basic safety.
    Final normalization happens in semantic tag normalization.
    
    Allows structured tags with colon (e.g., type:refactor, priority:high).
    Only whitelisted prefixes allowed for colon tags.
    
    Args:
        tags: List of tag strings
        
    Returns:
        List[str]: Pre-validated tags (lowercase, safe chars only)
        
    Raises:
        SecurityError: If validation fails
    """
    if not isinstance(tags, list):
        raise SecurityError("Tags must be a list")
    
    validated_tags = []
    for tag in tags[:Config.MAX_TAGS_PER_MEMORY]:
        if isinstance(tag, str):
            try:
                clean_tag = sanitize_input(tag, Config.MAX_TAG_LENGTH).lower()
                # Allow alphanumeric, spaces, hyphens, underscores, dots, colons
                # Colon enables structured tags (type:refactor, priority:high)
                if re.match(r'^[a-z0-9\-_ .:]+$', clean_tag):
                    # Normalize whitespace: collapse multiple spaces, trim
                    clean_tag = ' '.join(clean_tag.split())
                    
                    # Colon tag validation: only whitelisted prefixes
                    if ':' in clean_tag:
                        parts = clean_tag.split(':', 1)
                        if len(parts) == 2:
                            prefix = parts[0].strip()
                            if prefix not in Config.ALLOWED_COLON_PREFIXES:
                                # Invalid prefix → skip tag
                                continue
                            # Reconstruct with normalized parts
                            clean_tag = f"{prefix}:{parts[1].strip()}"
                    
                    if clean_tag and clean_tag not in validated_tags:
                        validated_tags.append(clean_tag)
            except SecurityError:
                continue
    
    return validated_tags


def validate_category(category: str) -> str:
    """
    Validate memory category.
    
    Args:
        category: Category string to validate
        
    Returns:
        str: Validated category or "other" as fallback
    """
    if not isinstance(category, str):
        return "other"
    
    category = category.lower().strip()
    if category in Config.MEMORY_CATEGORIES:
        return category
    else:
        return "other"


def validate_comment(comment: str) -> str:
    """
    Validate and sanitize task comment.

    Args:
        comment: Comment string to validate

    Returns:
        Optional[str]: Sanitized comment or None if empty/invalid
    """
    if not isinstance(comment, str):
        return None

    # Sanitize using existing sanitize_input function
    # Max length: Config.MAX_MEMORY_LENGTH (10,000 chars - same as content)
    try:
        sanitized = sanitize_input(comment, max_length=Config.MAX_MEMORY_LENGTH)
        return sanitized
    except SecurityError:
        # If validation fails, return None (comment is optional)
        return None


def validate_search_params(query: str, limit: int, category: str = None) -> tuple:
    """
    Validate search parameters.

    Args:
        query: Search query string
        limit: Maximum results limit
        category: Optional category filter

    Returns:
        tuple: (sanitized_query, validated_limit, validated_category)

    Raises:
        SecurityError: If validation fails
    """
    # Validate query
    if not isinstance(query, str) or not query.strip():
        raise SecurityError("Search query cannot be empty")

    sanitized_query = sanitize_input(query, 1000)  # Reasonable query length

    # Validate limit
    if not isinstance(limit, int) or limit < 1:
        limit = 10
    limit = min(limit, Config.MAX_MEMORIES_PER_SEARCH)

    # Validate category
    validated_category = None
    if category is not None:
        validated_category = validate_category(category)
        if validated_category == "other" and category != "other":
            validated_category = None  # Invalid category, ignore filter

    return sanitized_query, limit, validated_category


def validate_cleanup_params(days_old: int, max_to_keep: int) -> tuple:
    """
    Validate cleanup parameters.
    
    Args:
        days_old: Minimum age in days for cleanup candidates
        max_to_keep: Maximum total memories to keep
        
    Returns:
        tuple: (validated_days_old, validated_max_to_keep)
        
    Raises:
        SecurityError: If validation fails
    """
    if not isinstance(days_old, int) or days_old < 1:
        raise SecurityError("days_old must be a positive integer")
    
    if not isinstance(max_to_keep, int) or max_to_keep < 100:
        raise SecurityError("max_to_keep must be at least 100")
    
    # Reasonable limits
    days_old = min(days_old, 365)  # Max 1 year
    max_to_keep = min(max_to_keep, Config.MAX_TOTAL_MEMORIES)
    
    return days_old, max_to_keep


def generate_content_hash(content: str) -> str:
    """
    Generate hash for content deduplication.
    
    Args:
        content: Content to hash
        
    Returns:
        str: Content hash (16 characters)
    """
    import hashlib
    return hashlib.sha256(content.encode('utf-8')).hexdigest()[:16]


def check_resource_limits(current_count: int) -> None:
    """
    Check if resource limits would be exceeded.

    Args:
        current_count: Current number of memories in database

    Raises:
        SecurityError: If limits would be exceeded
    """
    if current_count >= Config.MAX_TOTAL_MEMORIES:
        raise SecurityError(
            f"Memory limit reached ({Config.MAX_TOTAL_MEMORIES}). "
            "Use clear_old_memories to free space."
        )


def validate_file_path(file_path: Path) -> None:
    """
    Validate database file path for security.

    Args:
        file_path: Path to validate

    Raises:
        SecurityError: If path is unsafe
    """
    # Check file extension
    if file_path.suffix != '.db':
        raise SecurityError("Database file must have .db extension")

    # Check path components for directory traversal
    for part in file_path.parts:
        # Block parent directory traversal
        if '..' in part:
            raise SecurityError("Path traversal attempt detected")
        # Block hidden files in filename (last component), but allow hidden directories
        if part == file_path.name and part.startswith('.'):
            raise SecurityError("Hidden database files not allowed")

    # Check parent directory exists and is writable
    parent = file_path.parent
    if not parent.exists():
        raise SecurityError("Parent directory does not exist")

    if not os.access(parent, os.W_OK):
        raise SecurityError("Parent directory is not writable")
