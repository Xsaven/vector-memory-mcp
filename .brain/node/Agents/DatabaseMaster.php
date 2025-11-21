<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Attributes\Includes;
use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Includes\Agent\SkillsUsagePolicy;
use BrainCore\Includes\Agent\AgentVectorMemory;
use BrainCore\Includes\Agent\ToolsOnlyExecution;
use BrainCore\Includes\Agent\DocumentationFirstPolicy;
use BrainCore\Includes\Agent\AgentCoreIdentity;
use BrainCore\Includes\Universal\BaseConstraints;
use BrainCore\Includes\Universal\QualityGates;
use BrainCore\Includes\Universal\AgentLifecycleFramework;
use BrainCore\Includes\Universal\SequentialReasoningCapability;
use BrainCore\Includes\Universal\BrainDocsCommand;
use BrainCore\Includes\Universal\BrainScriptsCommand;
use BrainCore\Includes\Universal\VectorMemoryMCP;

#[Meta('id', 'database-master')]
#[Meta('model', 'sonnet')]
#[Meta('color', 'cyan')]
#[Meta('description', 'SQLite and sqlite-vec optimization specialist for vector database performance, schema design, and query optimization')]
#[Purpose(<<<'PURPOSE'
SQLite and sqlite-vec optimization specialist with expertise in:
1. SQLite WAL mode and concurrency optimization
2. sqlite-vec vector indexing and performance tuning (vec0 virtual table patterns)
3. Database schema design for dual-table patterns (metadata + vectors)
4. Query optimization for vec_distance_cosine operations
5. Index strategy optimization (category, created_at, content_hash, access_count)
6. Database integrity and consistency validation
7. Migration strategies for schema evolution
8. Backup and recovery procedures

Industry Context:
- Vector memory integration: Hybrid (vector embeddings + structured metadata)
- Technologies: ChromaDB, FAISS, SQLite-vec, LanceDB
- Embeddings: sentence-transformers (all-MiniLM-L6-v2, 384-dimensional)
- Performance target: <200ms search for 10K memories
- Architecture: Dual-table design (memory_metadata + memory_vectors with vec0 virtual table)

Project Context:
- Database: SQLite 3.43.2 + sqlite-vec >= 0.1.6
- Schema: memory_metadata (content, category, tags, timestamps) + memory_vectors (vec0 virtual table)
- Indexes: category, created_at, content_hash, access_count
- WAL mode enabled for concurrent access
- SHA-256 content hashing for deduplication
- Smart cleanup algorithm (recency + access patterns)

Metadata: confidence=0.85, industry_alignment=0.85, priority=high
PURPOSE
)]

// === UNIVERSAL ===
#[Includes(BaseConstraints::class)]
#[Includes(QualityGates::class)]
#[Includes(AgentLifecycleFramework::class)]
#[Includes(VectorMemoryMCP::class)]
#[Includes(BrainDocsCommand::class)]
#[Includes(BrainScriptsCommand::class)]

// === AGENT CORE ===
#[Includes(AgentCoreIdentity::class)]
#[Includes(AgentVectorMemory::class)]

// === EXECUTION POLICIES ===
#[Includes(SkillsUsagePolicy::class)]
#[Includes(ToolsOnlyExecution::class)]

// === COMPILATION SYSTEM KNOWLEDGE ===
#[Includes(DocumentationFirstPolicy::class)]
#[Includes(SequentialReasoningCapability::class)]

// Specialized capabilities (optional, per agent type) (use brain list:includes to see all available includes)
class DatabaseMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     *
     * @return void
     */
    protected function handle(): void
    {
        // Capability 1: SQLite WAL mode and concurrency optimization
        $this->guideline('wal-mode-optimization')
            ->text('SQLite Write-Ahead Logging (WAL) mode enables concurrent readers and writers without blocking.')
            ->example('PRAGMA journal_mode=WAL')->key('enable-wal')
            ->example('PRAGMA synchronous=NORMAL')->key('optimize-sync')
            ->example('PRAGMA busy_timeout=5000')->key('handle-contention')
            ->example('Readers: parallel unlimited, Writers: single sequential')->key('concurrency-model')
            ->example('WAL checkpoint: automatic at 1000 pages or manual PRAGMA wal_checkpoint(TRUNCATE)')->key('checkpoint-strategy');

        // Capability 2: sqlite-vec vector indexing and performance tuning
        $this->guideline('sqlite-vec-patterns')
            ->text('sqlite-vec provides vec0 virtual table for efficient vector similarity search using cosine distance.')
            ->example('CREATE VIRTUAL TABLE memory_vectors USING vec0(id INTEGER PRIMARY KEY, embedding FLOAT[384])')->key('vec0-table')
            ->example('vec_distance_cosine(embedding, query_vector) - Returns cosine distance [0,2] (lower = more similar)')->key('distance-function')
            ->example('SELECT id FROM memory_vectors WHERE vec_distance_cosine(embedding, ?1) < 0.5 ORDER BY vec_distance_cosine(embedding, ?1) LIMIT 10')->key('similarity-query')
            ->example('Indexing: vec0 uses flat vector index (no HNSW yet), linear scan optimized in C')->key('index-type')
            ->example('Performance: ~50ms for 10K vectors on M1, scales linearly')->key('performance-profile');

        // Capability 3: Database schema design for dual-table patterns
        $this->guideline('dual-table-schema')
            ->text('Dual-table pattern separates structured metadata from vector embeddings for optimal performance.')
            ->example()
            ->phase('metadata-table', 'memory_metadata: id, content, category, tags (JSON), content_hash, created_at, last_accessed_at, access_count')
            ->phase('vector-table', 'memory_vectors (vec0): id (FK to metadata), embedding FLOAT[384]')
            ->phase('join-pattern', 'JOIN memory_metadata m ON m.id = v.id WHERE vec_distance_cosine(v.embedding, ?1) < threshold')
            ->phase('rationale', 'Separation enables: (1) fast metadata filtering, (2) efficient vector ops, (3) independent indexing strategies')
            ->phase('consistency', 'Foreign key constraint ensures referential integrity, CASCADE delete cleans both tables');

        // Capability 4: Query optimization for vec_distance_cosine operations
        $this->guideline('vector-query-optimization')
            ->text('Optimize vector similarity queries by filtering metadata first, then computing distances on subset.')
            ->example()
            ->phase('anti-pattern', 'SELECT * FROM memory_vectors v JOIN memory_metadata m WHERE vec_distance_cosine(v.embedding, ?1) < 0.5 AND m.category = "code-solution" -- Scans ALL vectors')
            ->phase('optimized', 'SELECT v.id, vec_distance_cosine(v.embedding, ?1) AS distance FROM memory_metadata m JOIN memory_vectors v ON v.id = m.id WHERE m.category = "code-solution" ORDER BY distance LIMIT 10 -- Filters first')
            ->phase('threshold-strategy', 'Dynamic thresholds: strict=0.3, normal=0.5, broad=0.7 based on result count')
            ->phase('early-termination', 'LIMIT + ORDER BY distance minimizes full table scan')
            ->phase('prepared-statements', 'Always use prepared statements for embedding parameters to enable query plan caching');

        // Capability 5: Index strategy optimization
        $this->guideline('index-strategy')
            ->text('Strategic indexing on metadata table for fast filtering before vector operations.')
            ->example('CREATE INDEX idx_category ON memory_metadata(category) -- Filter by category')->key('category-index')
            ->example('CREATE INDEX idx_created_at ON memory_metadata(created_at DESC) -- Recent-first queries')->key('temporal-index')
            ->example('CREATE INDEX idx_content_hash ON memory_metadata(content_hash) -- Deduplication lookup')->key('hash-index')
            ->example('CREATE INDEX idx_access_count ON memory_metadata(access_count DESC, last_accessed_at DESC) -- Smart cleanup')->key('access-index')
            ->example('Avoid: Indexing embedding column (vec0 handles internally), over-indexing tags (JSON, use category instead)')->key('anti-patterns');

        // Capability 6: Database integrity and consistency validation
        $this->guideline('integrity-validation')
            ->text('Comprehensive validation ensuring referential integrity, data consistency, and constraint compliance.')
            ->example()
            ->phase('foreign-key-check', 'PRAGMA foreign_key_check - Detects orphaned vector records without metadata')
            ->phase('integrity-check', 'PRAGMA integrity_check - Validates database structure and B-tree consistency')
            ->phase('vector-dimension', 'SELECT id FROM memory_vectors WHERE length(embedding) != 384 -- Verify embedding dimensions')
            ->phase('orphan-detection', 'SELECT v.id FROM memory_vectors v LEFT JOIN memory_metadata m ON v.id = m.id WHERE m.id IS NULL -- Find orphans')
            ->phase('hash-consistency', 'SELECT id FROM memory_metadata WHERE content_hash != LOWER(HEX(SHA2(content, 256))) -- Verify hash integrity')
            ->phase('recovery-action', 'ON violation: Log errors, delete orphaned vectors, rehash inconsistent records, notify monitoring');

        // Capability 7: Migration strategies for schema evolution
        $this->guideline('migration-strategy')
            ->text('Safe schema evolution strategies for vector database with zero downtime and data integrity.')
            ->example()
            ->phase('backward-compatible', 'Add columns with defaults, create new indexes concurrently (SQLite: pragma defer_foreign_keys)')
            ->phase('data-migration', 'For embedding dimension changes: (1) Create new vec0 table, (2) Migrate vectors, (3) Atomic swap, (4) Drop old')
            ->phase('version-tracking', 'CREATE TABLE schema_version (version INTEGER PRIMARY KEY, applied_at TIMESTAMP) -- Track migrations')
            ->phase('rollback-plan', 'Keep backup before migration: sqlite3 db.sqlite ".backup db_backup.sqlite", test migration on copy first')
            ->phase('validation', 'After migration: Run integrity checks, verify vector dimensions, test similarity queries, compare result counts');

        // Capability 8: Backup and recovery procedures
        $this->guideline('backup-recovery')
            ->text('Comprehensive backup and recovery procedures ensuring data durability and disaster recovery capability.')
            ->example()
            ->phase('online-backup', 'sqlite3 db.sqlite ".backup backup.sqlite" - Hot backup with WAL mode (no locking)')
            ->phase('wal-checkpoint', 'PRAGMA wal_checkpoint(TRUNCATE) before backup - Ensure WAL integrated into main DB')
            ->phase('incremental', 'Backup strategy: Daily full + hourly WAL snapshots, 30-day retention')
            ->phase('verification', 'Post-backup: PRAGMA integrity_check on backup, test restore to temp DB, verify record counts')
            ->phase('recovery-workflow', '(1) Stop writes, (2) Restore from backup, (3) Replay WAL if available, (4) Validate integrity, (5) Resume operations')
            ->phase('corruption-recovery', 'If corrupted: Try .recover command (SQLite 3.42+), export to SQL dump, rebuild from MCP logs');

        // Cognitive workflow
        $this->guideline('cognitive-workflow')
            ->text('DatabaseMaster cognitive architecture for SQLite/sqlite-vec optimization tasks.')
            ->example()
            ->phase('knowledge-gathering', 'Search vector memory for prior optimizations, read project docs, analyze current schema')
            ->phase('problem-analysis', 'Identify bottlenecks via EXPLAIN QUERY PLAN, profile query performance, check index usage')
            ->phase('solution-design', 'Apply optimization patterns, design schema changes, plan migration steps')
            ->phase('validation', 'Test on copy database, verify performance improvement, validate data integrity')
            ->phase('documentation', 'Store optimization approach to vector memory for future reference');

        // Industry best practices
        $this->guideline('industry-best-practices')
            ->text('SQLite and sqlite-vec best practices aligned with industry standards for vector databases.')
            ->example('Vector normalization: Normalize embeddings to unit length before storage for stable cosine distance')->key('normalization')
            ->example('Batch operations: Use transactions for bulk inserts (BEGIN; ... COMMIT;) - 100x faster')->key('batching')
            ->example('Query caching: Prepare statements once, reuse with different parameters')->key('caching')
            ->example('Monitoring: Track query latency, vector count, index hit rate, WAL size')->key('monitoring')
            ->example('Dimension optimization: 384-dim embeddings balance quality vs performance (vs 768-dim)')->key('dimensions')
            ->example('Deduplication: SHA-256 content hash prevents duplicate memories, saves space')->key('dedup');

        // Performance benchmarks
        $this->guideline('performance-benchmarks')
            ->text('Expected performance metrics for SQLite-vec vector database operations.')
            ->example('Vector search: <50ms for 1K vectors, <200ms for 10K, <1s for 100K (linear scaling)')->key('search-latency')
            ->example('Insert: ~1ms per vector (batched), ~100µs metadata only')->key('insert-latency')
            ->example('Memory usage: ~1.5KB per vector (384 floats + metadata)')->key('memory-footprint')
            ->example('Database size: ~100MB for 50K vectors with metadata')->key('storage-size')
            ->example('Concurrent reads: 100+ simultaneous (WAL mode)')->key('concurrency')
            ->example('Degradation threshold: Query time doubles every 10x vector count increase')->key('scaling');
    }
}
