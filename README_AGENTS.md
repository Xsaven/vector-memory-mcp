# Vector Memory MCP - Agent Documentation

Complete reference for AI agents using the Vector Memory MCP Server.

---

## Table of Contents

1. [Overview](#overview)
2. [How It Works](#how-it-works)
3. [MCP Tools Reference](#mcp-tools-reference)
4. [Semantic Normalization Deep Dive](#semantic-normalization-deep-dive)
5. [IDF Tag Weights](#idf-tag-weights)
6. [Categories](#categories)
7. [Best Practices](#best-practices)
8. [Common Patterns](#common-patterns)
9. [Error Handling](#error-handling)

---

## Overview

Vector Memory MCP provides persistent semantic memory using:
- **sqlite-vec** for vector storage
- **sentence-transformers/all-MiniLM-L6-v2** for 384D embeddings
- **Semantic normalization** for consistent tags/categories
- **IDF weighting** for search relevance

---

## How It Works

### Storage Flow

```
Input: content, category, tags
    ↓
1. Validate & sanitize input
    ↓
2. Semantic category normalization
   - Exact match? → use it
   - Short (< 5 chars)? → dictionary fallback
   - Otherwise → embedding similarity
    ↓
3. Semantic tag normalization
   - For each tag:
     - Exact match in canonical? → use it, increment frequency
     - Similar tag exists? → merge, increment frequency
     - No match? → create new canonical tag
    ↓
4. Generate content embedding
    ↓
5. Store to SQLite (metadata + vectors)
```

### Search Flow

```
Input: query, filters
    ↓
1. Generate query embedding
    ↓
2. Vector similarity search (cosine distance)
    ↓
3. Apply filters (category, tags)
    ↓
4. Return ranked results with similarity scores
```

### Similarity Scoring

| Score | Interpretation |
|-------|----------------|
| 0.9+ | Extremely relevant, almost exact |
| 0.8-0.9 | Highly relevant |
| 0.7-0.8 | Moderately relevant |
| 0.6-0.7 | Somewhat relevant |
| <0.6 | Low relevance |

---

## MCP Tools Reference

### 1. store_memory

Store a memory with automatic semantic normalization.

```
mcp__vector-memory__store_memory({
    "content": "Memory content (max 10,000 chars)",
    "category": "bug-fix",  // optional, auto-normalized
    "tags": ["tag1", "tag2"]  // optional, auto-normalized, max 10
})
```

**Returns:**
```json
{
    "success": true,
    "memory_id": 123,
    "content_preview": "...",
    "category": "bug-fix",
    "tags": ["normalized-tag"],
    "created_at": "2026-02-19T..."
}
```

**What happens:**
1. Content hashed for deduplication
2. Category normalized (semantic + dictionary)
3. Each tag normalized to canonical form
4. Tag frequencies incremented
5. Embedding generated
6. Stored atomically

**Duplicate detection:** Same content hash returns existing memory_id without storing.

---

### 2. search_memories

Semantic vector search with optional filters.

```
mcp__vector-memory__search_memories({
    "query": "authentication bug fix",
    "limit": 10,           // 1-50, default 10
    "category": "bug-fix", // optional, exact match
    "tags": ["api", "auth"], // optional, OR logic (any tag matches)
    "offset": 0            // pagination
})
```

**Returns:**
```json
{
    "success": true,
    "query": "authentication bug fix",
    "results": [
        {
            "id": 123,
            "content": "...",
            "category": "bug-fix",
            "tags": ["api", "auth"],
            "similarity": 0.85,
            "distance": 0.15,
            "created_at": "...",
            "access_count": 5
        }
    ],
    "total": 42,
    "count": 10
}
```

**Tag filter behavior:** OR logic - returns memories with ANY of the specified tags.

---

### 3. list_recent_memories

Get most recently stored memories (by creation time).

```
mcp__vector-memory__list_recent_memories({
    "limit": 10  // 1-50, default 10
})
```

---

### 4. get_by_memory_id

Retrieve full details of a specific memory.

```
mcp__vector-memory__get_by_memory_id({
    "memory_id": 123
})
```

**Returns:** Complete memory object with all fields.

---

### 5. delete_by_memory_id

Permanently delete a memory (cannot be undone).

```
mcp__vector-memory__delete_by_memory_id({
    "memory_id": 123
})
```

**Note:** Does NOT decrement tag frequencies. Tags remain in canonical_tags table.

---

### 6. get_memory_stats

Database statistics and health check.

```
mcp__vector-memory__get_memory_stats({})
```

**Returns:**
```json
{
    "total_memories": 247,
    "memory_limit": 100000,
    "usage_percentage": 0.25,
    "categories": {"code-solution": 89, "bug-fix": 67},
    "recent_week_count": 12,
    "database_size_mb": 15.7,
    "health_status": "Healthy"
}
```

---

### 7. clear_old_memories

Intelligent cleanup based on age and access patterns.

```
mcp__vector-memory__clear_old_memories({
    "days_old": 30,    // minimum age to consider
    "max_to_keep": 1000  // max total memories after cleanup
})
```

**Cleanup algorithm prioritizes:**
1. Keeps frequently accessed memories
2. Keeps recent memories
3. Removes old, unused memories first

---

### 8. get_unique_tags

List all unique tags from memory metadata (raw tags stored).

```
mcp__vector-memory__get_unique_tags({})
```

---

### 9. get_canonical_tags

List canonical (normalized) tags from the canonical_tags table.

```
mcp__vector-memory__get_canonical_tags({})
```

**Difference from get_unique_tags:**
- `get_unique_tags`: Tags as stored in memories (may have duplicates merged)
- `get_canonical_tags`: All canonical tags with embeddings (includes frequencies)

---

### 10. get_tag_frequencies

Tag usage statistics - how often each canonical tag is used.

```
mcp__vector-memory__get_tag_frequencies({})
```

**Returns:**
```json
{
    "success": true,
    "frequencies": {"api": 50, "laravel": 10, "module:terminal": 2},
    "count": 42
}
```

**Use cases:**
- Identify most common tags
- Find rare/discriminative tags
- Understand tag distribution

---

### 11. get_tag_weights

IDF-based weights for search relevance tuning.

```
mcp__vector-memory__get_tag_weights({})
```

**Returns:**
```json
{
    "success": true,
    "weights": {"api": 0.26, "laravel": 0.43, "module:terminal": 1.44}
}
```

---

## Semantic Normalization Deep Dive

### Why Normalization?

Without normalization:
- `API v2.0`, `api 2`, `API version 2` → 3 different tags
- Search for `api 2` misses `API v2.0`
- Tag explosion, poor recall

With normalization:
- All variants → `api v2.0` (canonical)
- Search finds all related memories
- Consistent tag space

### Tag Normalization Algorithm

```
For each input tag:
    1. Lowercase, trim
    2. Exact match in canonical_tags? → use it, done
    3. Generate embedding
    4. Find best matching canonical tag:
       - similarity >= threshold (0.90)
       - passes all guards
    5. Found match? → use canonical, increment frequency
    6. No match? → create new canonical tag (frequency=1)
```

### All Guards Explained

| Guard | Rule | Example | Why |
|-------|------|---------|-----|
| **Version** | Different versions never merge | `api v1` ≠ `api v2` | v1 ≠ v2 are different APIs |
| **Number** | Different numbers rarely merge | `php7` ≠ `php8` | Different major versions |
| **Colon** | Same prefix, different suffix → NO | `type:refactor` ≠ `type:bug` | Different facets |
| **Prefix** | Structured vs plain → NO | `type:refactor` ≠ `refactor` | Preserve structure |
| **Substring stop** | Stop-words never boost | `api` ≠ `rest api` | Too generic |
| **Substring length** | len < 4 never boost | `ui` ≠ `web ui` | Too short, ambiguous |

### Version Guard Details

Extracts versions from patterns:
- `v1`, `v2.0`, `v1.2.3`
- `version 2`, `ver 3.0`
- `api 2` (number after known prefix)

```
api v1 vs api v2 → versions 1.0 vs 2.0 → NO MERGE
api v2.0 vs api 2 → versions 2.0 vs 2.0 → CAN MERGE (threshold 0.85)
```

### Substring Boost Details

When one tag is a subset of another:

```
laravel ⊂ laravel framework
    → similarity boosted by +0.03
    → 0.8959 + 0.03 = 0.9259
    → >= 0.90 → MERGE
```

**Restrictions:**
- Shorter word must be >= 4 chars
- Shorter word must NOT be a stop-word

### Category Normalization

Two-phase approach:

**Phase 1: Dictionary Fallback (for short inputs)**
```
auth → security (dictionary)
bug → bug-fix (dictionary)
perf → performance (dictionary)
```

**Phase 2: Semantic Similarity (for longer inputs)**
```
"optimization" → embedding → compare with canonical categories
    → best match >= 0.50? → use it
    → must be 0.10 better than "other" → ensures real match
```

### Thresholds Reference

| Threshold | Value | Purpose |
|-----------|-------|---------|
| Tag merge | 0.90 | Default for semantic merge |
| Same version | 0.85 | Lower for same-version tags |
| Substring boost | +0.03 | Boost amount for subset |
| Category | 0.50 | Category matching |
| Category margin | 0.10 | Must be better than "other" |
| Min substring length | 4 | Minimum for boost |

### Stop-Words List

These tags never get substring boost (too generic, would cause over-merging):

```
api, ui, db, test, auth, infra, ci, cd,
app, lib, sdk, cli, gui, web, sql, orm,
log, cfg, env, dev, prod, stg
```

### Colon Tag Whitelist

Only these prefixes allowed for structured tags:

```
type, domain, strict, cognitive, batch,
module, vendor, priority, scope, layer
```

**Why whitelist?** Prevents `random:stuff`, `file:...`, `note:...` explosion.

Invalid prefixes silently rejected.

---

## IDF Tag Weights

### Formula

```
weight = 1 / log(1 + frequency)
```

### Examples

| Tag | Frequency | Calculation | Weight |
|-----|-----------|-------------|--------|
| `api` | 50 | 1 / log(51) | 0.26 |
| `laravel` | 10 | 1 / log(11) | 0.43 |
| `module:terminal` | 2 | 1 / log(3) | 0.91 |
| `vendor:stripe` | 1 | 1 / log(2) | 1.44 |

### Interpretation

- **High weight (>0.8)**: Rare, specific tag → strong signal
- **Medium weight (0.4-0.8)**: Common but not ubiquitous
- **Low weight (<0.4)**: Very common → weak discriminative power

### Use Cases

1. **Reranking search results**: Weight rare tags higher
2. **Identifying unique content**: High-weight tags indicate specific domains
3. **Tag hygiene**: Very high frequency tags may be too generic

---

## Categories

| Category | Use For | Examples |
|----------|---------|----------|
| `code-solution` | Working implementations, patterns | "Use eager loading to fix N+1" |
| `bug-fix` | Bug fixes, root causes | "Memory leak caused by missing cleanup" |
| `architecture` | Design decisions, trade-offs | "Chose Redis for session storage because..." |
| `learning` | Insights, discoveries | "Closures capture outer scope" |
| `tool-usage` | Tool configs, commands | "phpstan.neon for strict analysis" |
| `debugging` | Troubleshooting steps | "Enable query log to find N+1" |
| `performance` | Optimizations, benchmarks | "Added index, query 10x faster" |
| `security` | Vulnerabilities, fixes | "XSS via unsanitized input" |
| `other` | Everything else | Miscellaneous |

---

## Best Practices

### Tag Hygiene

**Principle:** Tags describe SUBJECT/INTENT, not TOOLS/ACTIVITIES.

**Good tags** (subject/domain):
```json
["authentication", "laravel", "middleware", "api v2", "module:billing"]
```

**Bad tags** (tools/activities):
```json
["phpstan", "ci", "tests", "run-migration", "checked", "fixed"]
```

**Rule of thumb:** Would this tag help find related memories? If not, skip it.

### Content Quality

Store actionable knowledge with full context:

```
BAD: "Fixed bug in UserController"
     - No context, no pattern, not reusable

GOOD: "UserController@store: N+1 query on roles. 
       Fix: eager load with ->with('roles').
       Pattern: always check query count in store methods.
       Gotcha: ->with() must be before ->get()"
     - Problem, solution, pattern, gotcha all included
```

**Required elements:**
1. **WHAT** happened/what was done
2. **WHY** it works/why it happened
3. **WHEN** to apply this pattern
4. **GOTCHAS** to watch out for

### Search Strategy

**Multi-probe searches** for complex queries:

Instead of one generic query:
```
search("authentication problem")  // Too broad
```

Use multiple focused probes:
```
search("jwt token invalid")       // Specific symptom
search("token refresh flow")      // Related concept
search("authentication middleware") // Architecture
```

**Combine with filters:**
```
search("cache issue", category="performance", tags=["redis"])
```

### Memory Lifecycle

1. **Store** with rich context and proper tags
2. **Search** before storing to avoid duplicates
3. **Access** increases access_count (used for cleanup prioritization)
4. **Cleanup** removes old, unused memories

---

## Common Patterns

### Pattern: Store Solution

```
1. Search for similar issues first
2. If not found, store with:
   - category: "code-solution" or "bug-fix"
   - tags: [domain, technology, pattern]
   - content: problem + solution + why + gotchas
```

### Pattern: Research Topic

```
1. search("topic overview")
2. search("topic implementation")
3. search("topic best practices")
4. Synthesize findings
5. Store summary as learning
```

### Pattern: Debug Flow

```
1. Store initial bug description (category: "debugging")
2. Store each discovery
3. Store final fix (category: "bug-fix")
4. Link with tags: [component, bug-type]
```

### Pattern: Architecture Decision

```
1. Store context: what, why, constraints
2. category: "architecture"
3. tags: [system, concern, decision-type]
4. Include: alternatives considered, trade-offs
```

---

## Error Handling

### Response Format

All tools return consistent format:

```json
// Success
{
    "success": true,
    "data": ...,
    "message": "..."
}

// Error
{
    "success": false,
    "error": "Error type",
    "message": "Detailed description"
}
```

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| `Memory already exists` | Duplicate content hash | Check returned memory_id |
| `Memory limit reached` | Max memories stored | Run `clear_old_memories` |
| `Memory not found` | Invalid ID | Check ID with `search` |
| `Tags must be a list` | Wrong parameter type | Pass array of strings |
| `Invalid category` | Unknown category | Use valid category or let auto-normalize |

### Handling Duplicates

```javascript
result = store_memory(content, category, tags)
if (!result.success && result.message.includes("already exists")) {
    // Memory already stored, use result.memory_id to reference it
}
```

---

## Quick Reference

### Tools Summary

| Tool | Purpose | Side Effects |
|------|---------|--------------|
| store_memory | Store knowledge | Creates memory, increments tag frequencies |
| search_memories | Find memories | Increments access_count |
| list_recent_memories | Browse recent | None |
| get_by_memory_id | Get specific | Increments access_count |
| delete_by_memory_id | Remove memory | Permanent deletion |
| get_memory_stats | Health check | None |
| clear_old_memories | Cleanup | Removes memories |
| get_unique_tags | List stored tags | None |
| get_canonical_tags | List normalized tags | None |
| get_tag_frequencies | Tag stats | None |
| get_tag_weights | IDF weights | None |

### Limits

| Resource | Limit |
|----------|-------|
| Memory content | 10,000 chars |
| Tags per memory | 10 |
| Tag length | 100 chars |
| Search results | 50 |
| Default memory limit | 10,000 |
| Max memory limit | 10,000,000 |
