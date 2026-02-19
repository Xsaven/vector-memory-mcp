# Vector Memory MCP - Agent Documentation

Self-documenting reference for AI agents. Four verbosity levels for progressive understanding.

---

## Level 0: Identity & Quick Start

### What Is This

Vector Memory MCP is a semantic memory system for AI agents. Stores knowledge with vector embeddings, retrieves via semantic similarity, normalizes tags/categories automatically.

**Core value:** Persistent memory that understands meaning, not just keywords.

### When To Use

- Remember solutions, patterns, bug fixes across sessions
- Find similar problems you've solved before
- Build cumulative knowledge about codebases/projects
- Share context between different agents/tasks

### Quick Example

```
Store:
mcp__vector-memory__store_memory({
    "content": "UserController@store: N+1 on roles. Fix: ->with('roles'). Pattern: eager load relationships in store methods.",
    "category": "bug-fix",
    "tags": ["laravel", "eloquent", "n+1"]
})

Search:
mcp__vector-memory__search_memories({
    "query": "database query performance issue",
    "limit": 5
})
```

### Key Concept

**Semantic search** = finds meaning, not keywords. "Slow database" finds "N+1 query problem" because they're semantically related, even without shared words.

### Available Tools (Overview)

| Tool | What It Does |
|------|--------------|
| `store_memory` | Save knowledge with auto-normalization |
| `search_memories` | Semantic search with filters |
| `get_by_memory_id` | Retrieve specific memory |
| `delete_by_memory_id` | Remove memory |
| `list_recent_memories` | Browse recent stores |
| `get_memory_stats` | Database health |
| `clear_old_memories` | Cleanup old data |
| `get_unique_tags` | Raw tags list |
| `get_canonical_tags` | Normalized tags |
| `get_tag_frequencies` | Tag usage stats |
| `get_tag_weights` | IDF weights for ranking |

### Five Essential Patterns (CRITICAL)

1. **Multi-probe search** - NEVER single query, ALWAYS 2-3 focused probes
2. **Search before store** - check duplicates first (similarity >= 0.90 → skip)
3. **Rich content** - include WHAT, WHY, WHEN-TO-USE, GOTCHAS
4. **Subject tags** - describe domain, not activities
5. **Task integration** - memory = primary, comments = links only

---
---

## Level 1: Practical Usage

### MCP Tools Reference

#### store_memory

Store knowledge with automatic semantic normalization.

```
mcp__vector-memory__store_memory({
    "content": "string (required, max 10000 chars)",
    "category": "string (optional, auto-normalized)",
    "tags": ["array", "of", "strings"]  // optional, max 10, auto-normalized
})
```

**Returns:** `{success, memory_id, content_preview, category, tags, created_at}`

**Side effects:** Creates memory, increments tag frequencies, may merge tags.

**Duplicate detection:** Same content → returns existing memory_id without storing.

---

#### search_memories

Semantic vector search with optional filters.

```
mcp__vector-memory__search_memories({
    "query": "string (required)",
    "limit": 10,           // 1-50
    "category": "string",  // optional, exact match
    "tags": ["array"],     // optional, OR logic (any match)
    "offset": 0            // pagination
})
```

**Returns:**
```
{
    success: true,
    query: "...",
    results: [{
        id, content, category, tags,
        similarity, distance,
        created_at, access_count
    }],
    total, count
}
```

**Tag filter:** OR logic - returns memories with ANY specified tag.

---

#### list_recent_memories

Get most recent by creation time.

```
mcp__vector-memory__list_recent_memories({
    "limit": 10  // 1-50
})
```

---

#### get_by_memory_id

Retrieve full memory details.

```
mcp__vector-memory__get_by_memory_id({
    "memory_id": 123
})
```

**Side effect:** Increments access_count (used for cleanup prioritization).

---

#### delete_by_memory_id

Permanent deletion.

```
mcp__vector-memory__delete_by_memory_id({
    "memory_id": 123
})
```

**Note:** Does NOT decrement tag frequencies.

---

#### get_memory_stats

Database health and statistics.

```
mcp__vector-memory__get_memory_stats({})
```

**Returns:**
```
{
    total_memories, memory_limit, usage_percentage,
    categories: {"bug-fix": 67, "code-solution": 89},
    recent_week_count, database_size_mb, health_status
}
```

---

#### clear_old_memories

Intelligent cleanup.

```
mcp__vector-memory__clear_old_memories({
    "days_old": 30,      // minimum age
    "max_to_keep": 1000  // max after cleanup
})
```

**Priority:** Keeps frequently accessed, keeps recent, removes old unused.

---

#### get_unique_tags

Tags as stored in memories (raw).

```
mcp__vector-memory__get_unique_tags({})
```

---

#### get_canonical_tags

Normalized tags from canonical_tags table (with frequencies).

```
mcp__vector-memory__get_canonical_tags({})
```

**Difference:** `get_unique_tags` = raw stored, `get_canonical_tags` = normalized with embeddings.

---

#### get_tag_frequencies

Usage count per canonical tag.

```
mcp__vector-memory__get_tag_frequencies({})
```

**Returns:** `{"api": 50, "laravel": 10, ...}`

---

#### get_tag_weights

IDF-based weights for search ranking.

```
mcp__vector-memory__get_tag_weights({})
```

**Returns:** `{"api": 0.26, "laravel": 0.43, "module:terminal": 0.91}`

**Use:** Weight rare tags higher when reranking results.

---

### Categories

| Category | Use For |
|----------|---------|
| `code-solution` | Working implementations, patterns |
| `bug-fix` | Bug fixes with root causes |
| `architecture` | Design decisions, trade-offs |
| `learning` | Insights, discoveries |
| `tool-usage` | Tool configs, commands |
| `debugging` | Troubleshooting steps |
| `performance` | Optimizations, benchmarks |
| `security` | Vulnerabilities, fixes |
| `other` | Everything else |

**Auto-normalization:** Input like "bug", "fix", "bugs" → `bug-fix`

---

### Tag Rules

**Valid format:** `^[a-z0-9\-_ .:]+$` (lowercase, alphanumeric, dash, underscore, space, dot, colon)

**Colon tags:** Structured format `prefix:value`. Only allowed prefixes:
```
type, domain, strict, cognitive, batch,
module, vendor, priority, scope, layer
```

**Examples:**
- `type:refactor` ✓
- `module:billing` ✓
- `vendor:stripe` ✓
- `random:stuff` ✗ (prefix not whitelisted)

**Max:** 10 tags per memory.

---

### Content Quality

Store actionable knowledge with full context:

```
BAD: "Fixed bug in UserController"

GOOD: "UserController@store: N+1 query on roles.
       Fix: eager load with ->with('roles').
       Pattern: always check query count in store methods.
       Gotcha: ->with() must be before ->get()"
```

**Required elements:**
1. **WHAT** - what happened/was done
2. **WHY** - why it works/happened
3. **WHEN** - when to apply this pattern
4. **GOTCHAS** - what to watch out for

---

### Tag Hygiene

Tags describe **SUBJECT/INTENT**, not **TOOLS/ACTIVITIES**.

```
GOOD: ["authentication", "laravel", "middleware", "api v2"]
BAD:  ["phpstan", "ci", "tests", "run-migration", "checked"]
```

**Rule of thumb:** Would this tag help find related memories? If not, skip it.

---

### Typical Workflows

**Store Solution:**
1. Search for similar issues first
2. If not found → store with category, tags, rich content

**Research Topic:**
1. Search "topic overview"
2. Search "topic implementation"  
3. Search "topic best practices"
4. Synthesize and store summary

**Debug Flow:**
1. Store bug description (debugging)
2. Store discoveries
3. Store fix (bug-fix)
4. Link with common tags

---
---

## Level 2: Advanced Patterns

### Multi-Probe Search Strategy

Single query = single semantic radius. Complex topics need multiple probes.

**Instead of:**
```
search("authentication problem")  // Too broad
```

**Do:**
```
search("jwt token invalid")        // Specific symptom
search("token refresh flow")       // Related concept
search("authentication middleware") // Architecture
```

**Why:** Vector search finds semantically similar content. Different phrasings reveal different memories.

---

### Combining Filters

Layer filters for precision:

```
search({
    query: "cache performance",
    category: "performance",
    tags: ["redis", "laravel"],
    limit: 10
})
```

- `query` = semantic matching
- `category` = exact filter
- `tags` = OR filter (any match)

---

### IDF Weight Usage

**Formula:** `weight = 1 / log(1 + frequency)`

| Tag | Freq | Weight | Meaning |
|-----|------|--------|---------|
| `api` | 50 | 0.26 | Common, weak signal |
| `laravel` | 10 | 0.43 | Moderate |
| `module:terminal` | 2 | 0.91 | Rare, strong signal |
| `vendor:stripe` | 1 | 1.44 | Very rare, very specific |

**Application:**
- Rerank search results by tag weight
- Identify unique/specific content
- Find domain-specific memories

---

### Semantic Tag Normalization

Tags are automatically normalized to canonical forms.

**Example:**
- Input: `["API v2.0", "api 2", "Laravel Framework"]`
- Stored: `["api v2.0", "laravel framework"]` (canonical)

**Benefits:**
- Search for "api 2" finds "API v2.0" memories
- No tag explosion from variants
- Consistent tag space

**Guards prevent bad merges:**
- `api v1` ≠ `api v2` (version guard)
- `php7` ≠ `php8` (number guard)
- `type:bug` ≠ `type:refactor` (colon guard)

---

### Category Normalization

Two-phase approach:

**Phase 1: Dictionary (short inputs)**
```
auth → security
bug → bug-fix
perf → performance
```

**Phase 2: Semantic (longer inputs)**
```
"optimization" → embedding → best match >= 0.50?
```

Must be 0.10 better than "other" category.

---

### Anti-Patterns

**❌ Storing Without Searching**
```
BAD: store immediately
GOOD: search first → if not found → store
```

**❌ Single Generic Search**
```
BAD: search("fix the problem")
GOOD: 2-3 focused probes
```

**❌ Over-Tagging**
```
BAD: 10 generic tags
GOOD: 3-5 specific tags
```

**❌ Storing Execution Logs**
```
BAD: "Ran phpstan, found 5 errors, fixed"
GOOD: "PhpStan rule: strict_types prevents type coercion"
```

**❌ Ignoring Duplicates**
```
BAD: Store anyway on "already exists"
GOOD: Reference existing memory_id
```

---

## Vector Task MCP Integration

### Cross-Reference Format

Both MCPs use `#N` format for bidirectional linking:

```
Memory → Task: "Discovered during task #42"
Task → Memory: "Insights stored in memory #15"
```

### Memory Categories ↔ Task Types

| Memory Category | Task Types | Use When |
|----------------|------------|----------|
| `code-solution` | feature, refactor, test | Working implementations |
| `bug-fix` | bugfix, hotfix | Root causes, fixes |
| `architecture` | spike, research | Design decisions |
| `learning` | research, docs | Discoveries |
| `debugging` | bugfix | Failed approaches |
| `project-context` | chore, docs | Conventions |

### Memory Tags for Task Integration

| Tag Type | Examples | Purpose |
|----------|----------|---------|
| CONTENT | `solution`, `failure`, `decision`, `insight` | Content type |
| SCOPE | `reusable`, `project-wide`, `module-specific` | Applicability |

Formula: 1 CONTENT + 0-1 SCOPE

### Memory → Task Signals

When memory suggests task creation:

| Category | Signal | Task Type |
|----------|--------|-----------|
| `code-solution` | Needs implementation | feature |
| `bug-fix` | Needs investigation | bugfix |
| `architecture` | Decision needs docs | docs |
| `debugging` | Failure needs fix | bugfix |

Tag `needs-implementation` to mark memories requiring tasks.

### Workflow Integration (with `mcp__vector-task__*`)

**Pre-Task Mining:**
```
1. mcp__vector-task__task_get({task_id}) → understand scope
2. mcp__vector-memory__search_memories({query: "{topic}"}) → solutions
3. mcp__vector-memory__search_memories({query: "{topic} failure", category: "debugging"}) → avoid
```

**Post-Task Storage:**
```
1. mcp__vector-memory__search_memories({query: "{insight}"}) → check duplicates
2. IF unique: mcp__vector-memory__store_memory({content, category, tags})
3. mcp__vector-task__task_update({task_id, comment: "See memory #ID", append_comment: true})
```

### Task Tools Reference

| Tool | Purpose |
|------|---------|
| `mcp__vector-task__task_get` | Get task by ID |
| `mcp__vector-task__task_create` | Create new task |
| `mcp__vector-task__task_update` | Update task (status, comment, tags) |
| `mcp__vector-task__task_list` | Search/list tasks |
| `mcp__vector-task__task_next` | Get next task to work on |

### Comment Strategy

Memory = PRIMARY storage. Task comments = CRITICAL links only.

```
Comment format:
"Findings stored in memory #42, #43. See related #38."
"Modified: src/Auth/Login.php:45-78. Created: tests/AuthTest.php"
"BLOCKED: waiting for API spec. Resume when #15 completed."
"DECISION: Chose JWT over sessions. Rationale in memory #50."
```

---

### Error Handling

**Response format:**
```
{success: true, data: ..., message: "..."}
{success: false, error: "...", message: "..."}
```

**Common errors:**

| Error | Solution |
|-------|----------|
| `Memory already exists` | Use returned memory_id |
| `Memory limit reached` | Run `clear_old_memories` |
| `Memory not found` | Verify ID with search |
| `Tags must be a list` | Pass array of strings |

**Handling duplicates:**
```
result = mcp__vector-memory__store_memory(...)
if (!result.success && "already exists" in result.message):
    // Use result.memory_id to reference existing
```

---
---

## Level 3: Architecture & Internals

### Storage Flow

```
Input: content, category?, tags[]
    ↓
1. Content hash check (deduplication)
    ↓
2. Category normalization
   ├─ Exact match in canonical? → use it
   ├─ Short (< 5 chars)? → dictionary fallback
   └─ Otherwise → embedding similarity (threshold: 0.50)
    ↓
3. Tag normalization (each tag)
   ├─ Exact match in canonical_tags? → use it, freq++
   ├─ Similar exists + guards pass? → merge, freq++
   └─ No match? → create canonical (freq=1)
    ↓
4. Generate content embedding (384D)
    ↓
5. Store to SQLite (memories + vectors tables)
```

### Search Flow

```
Input: query, filters
    ↓
1. Generate query embedding
    ↓
2. Vector similarity (cosine distance)
   SELECT * FROM memories
   ORDER BY vec_distance_cosine(embedding, query_embedding)
    ↓
3. Apply filters
   ├─ category: exact match
   └─ tags: OR (any tag in filter matches)
    ↓
4. Return ranked with similarity scores
```

### Similarity Scoring

| Score | Interpretation |
|-------|----------------|
| 0.9+ | Extremely relevant, near-exact |
| 0.8-0.9 | Highly relevant |
| 0.7-0.8 | Moderately relevant |
| 0.6-0.7 | Somewhat relevant |
| <0.6 | Low relevance |

---

### All Normalization Guards

Guards run before any tag merge. If any guard fails → no merge.

| Guard | Rule | Example | Rationale |
|-------|------|---------|-----------|
| **Version** | Different versions never merge | `api v1` ≠ `api v2` | v1 ≠ v2 are different APIs |
| **Number** | Different numbers rarely merge | `php7` ≠ `php8` | Different major versions |
| **Colon** | Same prefix, different suffix → NO | `type:refactor` ≠ `type:bug` | Different facets |
| **Prefix** | Structured vs plain → NO | `type:refactor` ≠ `refactor` | Preserve structure |
| **Substring stop** | Stop-words never boost | `api` ≠ `rest api` | Too generic |
| **Substring length** | len < 4 never boost | `ui` ≠ `web ui` | Too ambiguous |

---

### Version Guard Details

Extracts versions from patterns:
- `v1`, `v2.0`, `v1.2.3`
- `version 2`, `ver 3.0`
- `api 2` (number after known prefix)

**Logic:**
```
v1.0 vs v2.0 → different → NO MERGE
v2.0 vs v2 → same version → CAN MERGE (threshold 0.85)
```

---

### Substring Boost

When one tag is a subset of another, similarity is boosted.

```
"laravel" ⊂ "laravel framework"
    → base_similarity: 0.8959
    → boost: +0.03
    → final: 0.9259
    → >= 0.90 → MERGE
```

**Restrictions:**
- Shorter word >= 4 chars
- Shorter word NOT a stop-word

---

### Thresholds Reference

| Threshold | Value | Purpose |
|-----------|-------|---------|
| Tag merge | 0.90 | Default for semantic merge |
| Same version | 0.85 | Lower threshold when versions match |
| Substring boost | +0.03 | Boost amount |
| Category | 0.50 | Category matching |
| Category margin | 0.10 | Must be better than "other" |
| Min substring length | 4 | Minimum for boost |

---

### Stop-Words

Never get substring boost (too generic, would cause over-merging):

```
api, ui, db, test, auth, infra, ci, cd,
app, lib, sdk, cli, gui, web, sql, orm,
log, cfg, env, dev, prod, stg
```

---

### Colon Tag Whitelist

Only these prefixes allowed for structured tags:

```
type, domain, strict, cognitive, batch,
module, vendor, priority, scope, layer
```

Invalid prefixes silently rejected during validation.

---

### Database Schema

**memories table:**
```sql
id, content, content_hash, category, tags,
embedding (BLOB), created_at, access_count
```

**canonical_tags table:**
```sql
id, tag, embedding (BLOB), frequency, created_at
```

**Embedding:** 384-dimensional float array (all-MiniLM-L6-v2)

---

### Limits

| Resource | Limit |
|----------|-------|
| Memory content | 10,000 chars |
| Tags per memory | 10 |
| Tag length | 100 chars |
| Search results | 50 max |
| Default memory limit | 10,000 |
| Max memory limit | 10,000,000 |

---

### Cleanup Algorithm

`clear_old_memories` prioritizes:

1. **Keep:** Frequently accessed (access_count)
2. **Keep:** Recent (created_at)
3. **Remove:** Old + unused

**Logic:** Delete memories older than `days_old` that have low access_count, respecting `max_to_keep` limit.
