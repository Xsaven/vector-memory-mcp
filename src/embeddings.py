"""
Embeddings Module
=================

Provides text embedding generation using sentence-transformers.
Handles model initialization, caching, and vector operations.
"""

import os
import sys
from typing import List, Optional
import numpy as np
from sentence_transformers import SentenceTransformer

from .models import Config
from .security import SecurityError


class EmbeddingModel:
    """
    Wrapper for sentence-transformers model with caching and validation.
    """
    
    def __init__(self, model_name: str = None, cache_dir: str = None):
        """
        Initialize embedding model.
        
        Args:
            model_name: Name of the sentence-transformers model
            cache_dir: Directory to cache the model
        """
        self.model_name = model_name or Config.EMBEDDING_MODEL
        self.cache_dir = cache_dir
        self.model: Optional[SentenceTransformer] = None
        self._embedding_dim: Optional[int] = None
        
    def _initialize_model(self) -> None:
        """Initialize the sentence transformer model."""
        try:
            # Set cache directory if provided
            if self.cache_dir:
                os.environ['SENTENCE_TRANSFORMERS_HOME'] = self.cache_dir
            
            print(f"Loading embedding model: {self.model_name}", file=sys.stderr)
            self.model = SentenceTransformer(self.model_name)
            
            # Verify model dimensions
            test_embedding = self.model.encode(["test"], normalize_embeddings=True)
            self._embedding_dim = test_embedding.shape[1]
            
            if self._embedding_dim != Config.EMBEDDING_DIM:
                raise ValueError(
                    f"Model dimension mismatch: expected {Config.EMBEDDING_DIM}, "
                    f"got {self._embedding_dim}"
                )
            
            print(f"Model loaded successfully. Dimensions: {self._embedding_dim}", file=sys.stderr)
            
        except Exception as e:
            raise RuntimeError(f"Failed to initialize embedding model: {e}")
    
    @property
    def embedding_dim(self) -> int:
        """Get embedding dimensions."""
        if self._embedding_dim is None:
            if self.model is None:
                self._initialize_model()
            return self._embedding_dim
        return self._embedding_dim
    
    def encode(self, texts: List[str], normalize: bool = True) -> np.ndarray:
        """
        Generate embeddings for a list of texts.
        
        Args:
            texts: List of text strings to encode
            normalize: Whether to normalize embeddings to unit length
            
        Returns:
            np.ndarray: Array of embeddings with shape (len(texts), embedding_dim)
            
        Raises:
            SecurityError: If input validation fails
            RuntimeError: If encoding fails
        """
        if not isinstance(texts, list):
            raise SecurityError("Input must be a list of strings")
        
        if not texts:
            raise SecurityError("Input list cannot be empty")
        
        # Validate each text
        for i, text in enumerate(texts):
            if not isinstance(text, str):
                raise SecurityError(f"Text at index {i} must be a string")
            if not text.strip():
                raise SecurityError(f"Text at index {i} cannot be empty")
        
        # Initialize model if needed
        if self.model is None:
            self._initialize_model()
        
        try:
            embeddings = self.model.encode(
                texts,
                normalize_embeddings=normalize,
                convert_to_numpy=True
            )
            return embeddings
            
        except Exception as e:
            raise RuntimeError(f"Failed to generate embeddings: {e}")
    
    def encode_single(self, text: str, normalize: bool = True) -> List[float]:
        """
        Generate embedding for a single text.
        
        Args:
            text: Text string to encode
            normalize: Whether to normalize embedding to unit length
            
        Returns:
            List[float]: Embedding vector as list of floats
            
        Raises:
            SecurityError: If input validation fails
            RuntimeError: If encoding fails
        """
        embeddings = self.encode([text], normalize=normalize)
        return embeddings[0].tolist()
    
    def similarity(self, text1: str, text2: str) -> float:
        """
        Calculate cosine similarity between two texts.
        
        Args:
            text1: First text
            text2: Second text
            
        Returns:
            float: Cosine similarity score (0-1)
        """
        embeddings = self.encode([text1, text2], normalize=True)
        
        # Cosine similarity with normalized vectors is just dot product
        similarity = np.dot(embeddings[0], embeddings[1])
        return float(similarity)
    
    def batch_similarity(self, query: str, texts: List[str]) -> List[float]:
        """
        Calculate similarity between a query and multiple texts.
        
        Args:
            query: Query text
            texts: List of texts to compare against
            
        Returns:
            List[float]: Similarity scores for each text
        """
        all_texts = [query] + texts
        embeddings = self.encode(all_texts, normalize=True)
        
        query_embedding = embeddings[0]
        text_embeddings = embeddings[1:]
        
        # Calculate dot products (cosine similarity with normalized vectors)
        similarities = [
            float(np.dot(query_embedding, text_emb))
            for text_emb in text_embeddings
        ]
        
        return similarities
    
    def get_model_info(self) -> dict:
        """
        Get information about the loaded model.
        
        Returns:
            dict: Model information
        """
        if self.model is None:
            self._initialize_model()
        
        return {
            "model_name": self.model_name,
            "embedding_dimensions": self.embedding_dim,
            "max_sequence_length": getattr(self.model, 'max_seq_length', 'Unknown'),
            "device": str(self.model.device) if hasattr(self.model, 'device') else 'Unknown',
            "cache_dir": self.cache_dir or 'Default'
        }
    
    def validate_embedding(self, embedding: List[float]) -> bool:
        """
        Validate that an embedding has the correct dimensions.
        
        Args:
            embedding: Embedding vector to validate
            
        Returns:
            bool: True if valid, False otherwise
        """
        return (
            isinstance(embedding, list) and
            len(embedding) == self.embedding_dim and
            all(isinstance(x, (int, float)) for x in embedding)
        )


# Global model instance for efficient reuse
_global_model: Optional[EmbeddingModel] = None


def get_embedding_model(model_name: str = None, cache_dir: str = None) -> EmbeddingModel:
    """
    Get global embedding model instance (singleton pattern).
    
    Args:
        model_name: Name of the model (only used on first call)
        cache_dir: Cache directory (only used on first call)
        
    Returns:
        EmbeddingModel: Global model instance
    """
    global _global_model
    
    if _global_model is None:
        _global_model = EmbeddingModel(model_name, cache_dir)
    
    return _global_model


def reset_embedding_model() -> None:
    """Reset global model instance (useful for testing)."""
    global _global_model
    _global_model = None
