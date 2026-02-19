# Vector Memory MCP - Use Cases for Brain Agents

Scenario-based reference for AI agents in the Brain ecosystem.

---

## Cookbook Usage Scenarios
<!-- description: How to use cookbook() tool - init, search, pagination, docs levels. Start here. -->

### Essential Patterns (CRITICAL)

Master these patterns first. They appear repeatedly across ALL categories.

```
1. MULTI-PROBE SEARCH
   NEVER single query. ALWAYS 2-3 focused probes.
   BAD:  mcp__vector-memory__search_memories({query: "auth problem"})
   GOOD: mcp__vector-memory__search_memories({query: "JWT token invalid"})
         mcp__vector-memory__search_memories({query: "token refresh flow"})
         mcp__vector-memory__search_memories({query: "auth middleware"})

2. SEARCH BEFORE STORE
   ALWAYS check for duplicates before storing.
   Flow: search → if similarity < 0.90 → store
         search → if similarity >= 0.90 → skip or update

3. TASK INTEGRATION
   Memory = PRIMARY storage for reusable knowledge
   Task comments = CRITICAL links only (memory IDs, file paths)
   Format: "Findings stored in memory #42. See related #38."

4. CONTENT QUALITY
   Every memory needs: WHAT + WHY + WHEN-TO-USE + GOTCHAS
   BAD:  "Fixed N+1 in UserController"
   GOOD: "UserController@store: N+1 on roles. Fix: ->with('roles'). 
          Pattern: eager load relationships. Gotcha: ->with() before ->get()"

5. COOKBOOK FIRST
   When uncertain: mcp__vector-memory__cookbook()
   Contains all patterns, tools, and best practices.
```

---

### cookbook() Parameters Reference

```
mcp__vector-memory__cookbook(
    include: str = "init",       # What to return
    level: int = 0,              # Docs verbosity (0-3)
    case_category: str = None,   # Filter cases by key or title
    query: str = None,           # Text search in content
    limit: int = 10,             # Max results (1-50)
    offset: int = 0              # Pagination offset
)

include options:
  "init"        → FIRST READ - quick start + available resources
  "docs"        → Documentation by level
  "cases"       → Use case scenarios (all or filtered)
  "categories"  → List categories with keys and descriptions
  "all"         → Everything combined (large!)

level options (for docs):
  0 → Identity & Quick Start
  1 → Practical Usage
  2 → Advanced Patterns
  3 → Architecture & Internals
```

---

### First: Initialize Context

ALWAYS call cookbook() first when uncertain.

```python
mcp__vector-memory__cookbook()
# or
mcp__vector-memory__cookbook(include="init")

# Returns:
# {
#   "critical": "READ THIS FIRST",
#   "quick_start": "...Level 0 docs...",
#   "available_resources": {
#     "cookbook_docs": {"levels": [0,1,2,3], ...},
#     "cookbook_cases": {"categories": [...], "keys": [...]},
#     "cookbook_search": {"usage": "..."},
#     ...
#   }
# }
```

---

### List Categories with Keys

Get all available categories with keys for filtering.

```python
mcp__vector-memory__cookbook(include="categories")

# Returns:
# {
#   "categories": [
#     {"key": "cookbook-usage", "title": "Cookbook Usage Scenarios", 
#      "description": "How to use cookbook() tool..."},
#     {"key": "store", "title": "Store Scenarios",
#      "description": "Store memories with deduplication..."},
#     {"key": "search", "title": "Search Scenarios",
#      "description": "Multi-probe search, filtered search..."},
#     {"key": "gates-rules", "title": "Gates & Rules Scenarios",
#      "description": "CRITICAL and HIGH priority rules..."},
#     ...
#   ],
#   "keys": ["cookbook-usage", "store", "search", ...],
#   "total": 12
# }
```

---

### Get Cases by Key

Retrieve specific category using key (exact match).

```python
# By key (recommended - exact match)
mcp__vector-memory__cookbook(include="cases", case_category="gates-rules")
mcp__vector-memory__cookbook(include="cases", case_category="task-integration")
mcp__vector-memory__cookbook(include="cases", case_category="store")

# By title (partial match, fallback)
mcp__vector-memory__cookbook(include="cases", case_category="Gates")
mcp__vector-memory__cookbook(include="cases", case_category="Search")
```

---

### Search in Cookbook

Text search across all docs and cases.

```python
# Search in cases
mcp__vector-memory__cookbook(include="cases", query="JWT token")
mcp__vector-memory__cookbook(include="cases", query="critical")

# Search in docs
mcp__vector-memory__cookbook(include="docs", query="tag normalization", level=2)

# Search everywhere
mcp__vector-memory__cookbook(include="all", query="error handling")
```

---

### Pagination

Control result size with limit and offset.

```python
# First page
mcp__vector-memory__cookbook(include="cases", query="task", limit=5, offset=0)

# Next page
mcp__vector-memory__cookbook(include="cases", query="task", limit=5, offset=5)

# Parameters
#   limit:  Max results (default 10, max 50)
#   offset: Starting position (default 0)
```

---

### Get Documentation by Level

Progressive documentation depth.

```python
mcp__vector-memory__cookbook(include="docs", level=0)  # Quick start
mcp__vector-memory__cookbook(include="docs", level=1)  # Practical usage
mcp__vector-memory__cookbook(include="docs", level=2)  # Advanced patterns
mcp__vector-memory__cookbook(include="docs", level=3)  # Architecture/internals

# When to use:
#   level=0: First time using MCP, quick reference
#   level=1: Need tool parameters, categories, workflows
#   level=2: Complex patterns, multi-probe, IDF weights
#   level=3: Debugging MCP itself, understanding internals
```

---

### Full Reference (Debug Mode)

Everything combined - use sparingly.

```python
mcp__vector-memory__cookbook(include="all", level=3)

# Returns: docs + cases + categories (large response!)
```

---

### Category Keys Reference

| Key | Title | Description |
|-----|-------|-------------|
| `cookbook-usage` | Cookbook Usage | How to use cookbook() tool |
| `store` | Store | Store memories with deduplication |
| `search` | Search | Multi-probe search, pre-task mining |
| `statistics` | Statistics | Memory stats, tag frequencies |
| `task-management` | Task Management | Memory integration with Task MCP |
| `brain-docs` | Brain Docs | CLI docs indexing, keyword search |
| `agent-coordination` | Agent Coordination | Brain delegation, multi-agent |
| `integration` | Integration | Multi-source knowledge, error recovery |
| `debugging` | Debugging | Debug flow with memory capture |
| `cleanup` | Cleanup | Delete operations, cleanup by age |
| `gates-rules` | Gates & Rules | CRITICAL/HIGH priority rules |
| `task-integration` | Task Integration | Memory-Task workflow patterns |

---
---

## Store Scenarios
<!-- description: Store memories with deduplication, categories, tags. Core write operations. -->

### Basic Store

Simple memory storage for quick knowledge capture.

```
Trigger: Found useful pattern, solution, or insight
Flow:
  1. Parse content → extract key elements
  2. mcp__vector-memory__store_memory({
       content: "...",
       category: "code-solution",
       tags: ["domain", "technology"]
     })
  3. Return memory_id for reference

Example:
  User: "Store this: Laravel queues need retry_after in config"
  → mcp__vector-memory__store_memory({
      content: "Laravel queues: retry_after must be set in config/queue.php. Without it, jobs retry indefinitely.",
      category: "learning",
      tags: ["laravel", "queues", "config"]
    })
```

---

### Smart Store with Deduplication

Search before storing to prevent duplicates.

```
Trigger: Significant insight or solution
Flow:
  1. mcp__vector-memory__search_memories({
       query: "{content_summary}",
       limit: 3
     })
  2. IF similarity >= 0.90 → WARN: "Similar exists: #{id}"
  3. IF user confirms new → store
  4. IF user wants update → delete old + store new

Example:
  Found: "N+1 in UserController"
  Search: query="UserController N+1 query"
  Result: 0.75 similarity (different controller)
  Decision: Store as new

  Found: "N+1 in OrderController"  
  Search: query="OrderController N+1 query"
  Result: 0.92 similarity (same issue)
  Decision: Skip or update existing
```

---

### Store Architecture Decision

Capture design decisions with trade-offs.

```
Trigger: Made significant architectural choice
Flow:
  1. Document: WHAT, WHY, ALTERNATIVES, TRADE-OFFS
  2. mcp__vector-memory__store_memory({
       content: "Decision: Use Redis for session storage.
                 WHY: Horizontal scaling, shared state across servers.
                 ALTERNATIVES: file (no scaling), database (slower).
                 TRADE-OFFS: Redis dependency, memory cost.
                 WHEN-TO-APPLY: Multi-server deployments.",
       category: "architecture",
       tags: ["session", "redis", "scaling", "decision"]
     })

Gotcha: Always include WHY and ALTERNATIVES - future you needs context.
```

---

### Store Bug Fix with Root Cause

Capture bug fixes with full diagnostic context.

```
Trigger: Fixed a bug
Flow:
  1. Document: SYMPTOM, ROOT CAUSE, FIX, PREVENTION
  2. mcp__vector-memory__store_memory({
       content: "Bug: Random 500 errors on login.
                 SYMPTOM: Intermittent failures, no logs.
                 ROOT CAUSE: Race condition in session initialization.
                 FIX: Add mutex lock around session start.
                 PREVENTION: Always check session state before operations.
                 PATTERN: Race conditions often hide in initialization code.",
       category: "bug-fix",
       tags: ["session", "race-condition", "debugging"]
     })
```

---

### Store with IDF Weight Awareness

Store with awareness of tag frequency impact.

```
Trigger: Adding tags to memory
Flow:
  1. Check tag frequencies if needed
  2. Prefer specific tags over generic ones
  3. Use structured tags for precision

Generic (weak signal):  tags: ["api", "auth", "backend"]
Specific (strong signal): tags: ["api v2", "jwt", "vendor:auth0", "module:billing"]

Gotcha: Common tags (api, auth) have low IDF weight. Rare tags (vendor:stripe, module:terminal) have high weight and boost search relevance.
```

---
---

## Search Scenarios
<!-- description: Multi-probe search, filtered search, pre-task mining. Core read operations. -->

### Simple Search

Basic semantic search for quick retrieval.

```
Trigger: Need to find related knowledge
Flow:
  mcp__vector-memory__search_memories({
    query: "authentication patterns",
    limit: 10
  })

Similarity interpretation:
  0.90+ → Highly relevant, almost exact
  0.80-0.90 → Good match, worth reading
  0.70-0.80 → Somewhat related
  <0.70 → Weak match, may not be useful
```

---

### Multi-Probe Search (CRITICAL)

Always use for complex queries. Single query = missed context.

```
Trigger: Complex topic, need comprehensive coverage
Flow:
  1. Decompose query into 2-3 semantic aspects
  2. Execute probes in parallel
  3. Merge unique insights, discard duplicates

Example - "How to implement JWT auth in Laravel":
  Probe 1: "JWT authentication Laravel"        → Implementation
  Probe 2: "user login security session"      → Security context
  Probe 3: "token refresh pattern expiration" → Edge cases

Example - "Why tests fail in CI":
  Probe 1: "test failure CI environment"      → Environment issues
  Probe 2: "similar bug fix testing"          → Known solutions
  Probe 3: "database migration test"          → Common pitfalls

Gotcha: Vector search has semantic radius. Different phrasings reveal different memories.
```

---

### Filtered Search

Combine semantic search with exact filters.

```
Trigger: Need precision within a category or domain
Flow:
  mcp__vector-memory__search_memories({
    query: "cache performance",
    category: "performance",    // Exact match
    tags: ["redis", "laravel"], // OR logic (any match)
    limit: 20
  })

Filter behavior:
  - category: EXACT match (only that category)
  - tags: OR logic (memories with ANY of the tags)
  - Combined: Semantic match AND filters pass
```

---

### Pre-Task Mining

Mine memory BEFORE starting significant work.

```
Trigger: Starting new task, especially complex or unfamiliar
Flow:
  1. Initial probe: mcp__vector-memory__search_memories({query: "{task_topic}", limit: 5})
  2. IF sparse results → 2 more probes with synonyms
  3. IF critical task → probe by category (architecture, bug-fix, code-solution)
  4. Extract: solutions tried, patterns used, mistakes avoided

Example - Starting "Add rate limiting":
  Probe 1: "rate limiting implementation"
  Probe 2: "api throttling middleware"
  Probe 3: "DDOS protection"
  
  Extract:
    - Pattern: Use existing middleware, don't reinvent
    - Gotcha: Throttle key should include user ID
    - Failure: Global rate limit broke multi-tenant
```

---

### Failure-Awareness Search

Find what DIDN'T work before trying approaches.

```
Trigger: Starting task with multiple possible approaches
Flow:
  1. mcp__vector-memory__search_memories({
       query: "{task} failure",
       category: "debugging",
       limit: 5
     })
  2. Extract BLOCKED_APPROACHES
  3. Pass to agents: "DO NOT USE these approaches"

Example:
  Task: "Fix slow API response"
  Search: "API slow failure"
  Found: "Tried Redis cache - broke serialization"
  Blocked: Redis for this use case
  Alternative: Use file cache or optimize query
```

---
---

## Statistics Scenarios
<!-- description: Memory stats, category analysis, tag frequencies, IDF weights. -->

### Overview Statistics

Get memory database health and usage.

```
Trigger: Check memory state, health, or capacity
Flow:
  mcp__vector-memory__get_memory_stats({})
  
Returns:
  {
    "total_memories": 247,
    "memory_limit": 100000,
    "usage_percentage": 0.25,
    "categories": {"code-solution": 89, "bug-fix": 67},
    "recent_week_count": 12,
    "database_size_mb": 15.7,
    "health_status": "Healthy"
  }

Use cases:
  - Before cleanup: Check total and usage
  - Health check: Verify healthy status
  - Capacity planning: Monitor usage_percentage
```

---

### Category-Specific Analysis

Deep dive into specific category.

```
Trigger: Need insights about specific category
Flow:
  1. mcp__vector-memory__search_memories({query: "*", category: "{category}", limit: 50})
  2. Calculate: count, avg access_count, date range
  
Example:
  Category: "bug-fix"
  Query: mcp__vector-memory__search_memories({query: "*", category: "bug-fix", limit: 50})
  Analysis:
    - Count: 23 bug-fix memories
    - Avg access: 3.2x
    - Date range: 2026-01-15 to 2026-02-19
    - Top accessed: Memory #42 "N+1 fix pattern"
```

---

### Tag Frequency Analysis

Understand tag distribution and IDF weights.

```
Trigger: Planning tag strategy or understanding search relevance
Flow:
  1. mcp__vector-memory__get_tag_frequencies({}) → raw frequencies
  2. mcp__vector-memory__get_tag_weights({}) → IDF weights
  
Returns (frequencies):
  {"api": 50, "laravel": 10, "module:terminal": 2, "vendor:stripe": 1}

Returns (weights):
  {"api": 0.26, "laravel": 0.43, "module:terminal": 0.91, "vendor:stripe": 1.44}

Interpretation:
  - High frequency (api: 50) → Low weight (0.26) → Weak signal
  - Low frequency (vendor:stripe: 1) → High weight (1.44) → Strong signal

Use for:
  - Tag strategy: Prefer specific, rare tags
  - Search reranking: Weight by IDF
  - Quality check: Identify over-generic tags
```

---

### Top Accessed Memories

Find most valuable stored knowledge.

```
Trigger: Identify frequently referenced memories
Flow:
  1. mcp__vector-memory__list_recent_memories({limit: 50})
  2. Sort by access_count descending
  3. Extract top N

Insights:
  - High access = high value = keep during cleanup
  - Low access + old = candidate for cleanup
  - Recent + high access = trending knowledge
```

---
---

## Task Management Scenarios
<!-- description: Memory integration with Vector Task MCP. Full task ops delegated to Task MCP. -->

> **NOTE:** Task creation, decomposition, and status management are handled by **Vector Task MCP**.
> 
> Use `mcp__vector-task__get_docs(include="cases")` to access:
> - Task Creation Scenarios
> - Task Execution Scenarios  
> - Search & Query Scenarios
> - Hierarchy & Decomposition Scenarios
> - Status & Time Tracking Scenarios
> - Parallel Execution Scenarios
> - Validation Scenarios
> - And more...
>
> This MCP (Vector Memory) focuses on **memory operations** and **memory-task integration**.
> See **Task Integration Scenarios** below for the intersection of both systems.

### Memory Integration with Tasks

When working with tasks, use memory for context:

```python
# Before task execution - mine memory for solutions
mcp__vector-memory__search_memories({
    "query": "authentication JWT Laravel",
    "limit": 5
})

# After task completion - store learnings
mcp__vector-memory__store_memory({
    "content": "Solution pattern from task #42...",
    "category": "code-solution",
    "tags": ["solution", "reusable"]
})

# Link in task comment
mcp__vector-task__task_update({
    "task_id": 42,
    "comment": "Done. Pattern stored in memory #ID.",
    "append_comment": True
})
```

See **Task Integration Scenarios** section for complete workflows.

---
---

## Brain Docs Scenarios
<!-- description: CLI docs indexing, keyword search, YAML front matter, docs-to-memory workflow. -->

### Quick Index

List all documentation files.

```
Trigger: Need overview of available docs
Flow:
  Bash('brain docs')
  
Returns:
  [{"path": ".docs/guide.md", "score": 0}, ...]

Output: JSON array with paths and scores
```

---

### Keyword Search

Search docs by keywords (OR logic).

```
Trigger: Find specific documentation
Flow:
  Bash('brain docs "api authentication"')
  
Returns:
  [{"path": ".docs/auth.md", "score": 2}, ...]

Options:
  --limit=N     Max results (default: 5)
  --headers=2   Include H1+H2 structure
  --stats       Include file stats
  --snippets    Include content previews
```

---

### Docs with Structure

Get document headers and line ranges.

```
Trigger: Need to understand doc structure before reading
Flow:
  Bash('brain docs "api" --headers=2')
  
Returns:
  [{
    "path": ".docs/api.md",
    "score": 1,
    "headers": [
      {"text": "API Reference", "start_line": 1, "end_line": 50},
      {"text": "Authentication", "start_line": 10, "end_line": 30}
    ]
  }]

Header levels:
  --headers=1  H1 only
  --headers=2  H1 + H2
  --headers=3  H1 + H2 + H3
```

---

### Docs with Stats

Get file statistics for planning.

```
Trigger: Assess doc size/complexity
Flow:
  Bash('brain docs "guide" --stats --snippets')
  
Returns:
  [{
    "path": ".docs/guide.md",
    "score": 1,
    "stats": {
      "lines": 150,
      "words": 1200,
      "size": 8500,
      "hash": "abc123",
      "modified": "2026-02-19T..."
    }
  }]

Use cases:
  - Large file (>500 lines) → Read in sections
  - Recent modification → May need refresh
```

---

### Docs → Memory Workflow

Index → Read → Store to memory.

```
Trigger: Found important documentation to preserve
Flow:
  1. Bash('brain docs "{topic}" --headers=2') → find doc
  2. Read('{path}') → read full content
  3. Extract key insights
  4. mcp__vector-memory__store_memory({
       content: "From {doc_name}: {key_insights}",
       category: "learning",
       tags: ["documentation", "{topic}"]
     })

Example:
  Bash('brain docs "architecture" --headers=2')
  → Found: .docs/architecture.md
  Read('.docs/architecture.md')
  → Extract: "Uses event sourcing pattern..."
  mcp__vector-memory__store_memory({
    content: "Architecture: Event sourcing for audit trail. WHY: Immutable history. PATTERN: Store events, not state.",
    category: "architecture",
    tags: ["event-sourcing", "documentation"]
  })
```

---

### Docs-First Policy

Always check docs before external sources.

```
Trigger: Need information about project/topic
Flow:
  1. Bash('brain docs "{keywords}"') → check internal docs
  2. IF found → Read and apply
  3. IF not found → mcp__vector-memory__search_memories() → check memory
  4. IF not found → external research (context7, web)

Priority: .docs > vector memory > external sources

Gotcha: .docs folder is CANONICAL source. Docs override assumptions.
```

---
---

## Agent Coordination Scenarios
<!-- description: Brain delegation, multi-agent parallel execution, agent-to-agent handoff via memory. -->

### Brain → Single Agent Delegation

Delegate task to specialized agent.

```
Trigger: Task requires specialist expertise
Flow:
  1. Identify agent: Bash('brain list:masters')
  2. Prepare delegation context (15 sections)
  3. Task(@agent-{name}, {
       task: "Clear description",
       FILES: ["file1.php", "file2.php"],
       DOCUMENTATION: "If docs exist: {...}",
       BLOCKED_APPROACHES: "Known failures: {...}",
       MEMORY_BEFORE: "Search memory for: {terms}",
       MEMORY_AFTER: "Store learnings: what worked, approach, insights",
       SECURITY: "No hardcoded secrets. Validate input.",
       VALIDATION: "Verify syntax. Run linter. Check logic.",
       GIT: "FORBIDDEN: git checkout/restore/stash/reset/clean",
       PATTERNS: "Search codebase for similar implementations",
       IMPACT: "Grep who imports/uses target file",
       HALLUCINATION: "Verify EVERY method exists",
       CLEANUP: "Remove unused imports, dead code",
       TESTS: "NO tests → WRITE them. Target >=80%",
       DOCS: "NEW feature → CREATE docs in .docs/"
     })
```

---

### Multi-Agent Parallel Execution

Execute multiple independent tasks simultaneously.

```
Trigger: Multiple independent tasks ready
Flow:
  1. Identify parallel batch (no file conflicts)
  2. Launch ALL agents CONCURRENTLY
  3. WAIT for ALL to complete
  4. Validate each result
  5. Store all learnings

Example - Parallel batch:
  Task(@agent-code-master, "Add validation to UserController.php")
  Task(@agent-code-master, "Add validation to ProductController.php")  
  Task(@agent-code-master, "Add validation to OrderController.php")
  
Result: 3 agents execute simultaneously, ~3x faster than sequential.

Gotcha: Verify NO file conflicts within batch before launching.
```

---

### Agent-to-Agent Handoff

Pass context between agents via memory.

```
Trigger: Agent completes, next agent needs context
Flow:
  Agent A (completing):
    mcp__vector-memory__store_memory({
      content: "Step 1: Created RateLimitMiddleware.
                Approach: used existing middleware pattern.
                Key insight: throttle key should include user ID.
                Files: app/Http/Middleware/RateLimitMiddleware.php",
      category: "code-solution",
      tags: ["middleware", "rate-limiting", "partial"]
    })

  Brain (delegating to Agent B):
    "Memory hints: rate limiting middleware, throttle key, user ID"
    
  Agent B (starting):
    mcp__vector-memory__search_memories({query: "rate limiting middleware", limit: 5})
    → Finds Agent A's stored knowledge
    → Continues with full context

Gotcha: Pass semantic hints as TEXT, not memory IDs. Vector search needs text.
```

---
---

## Integration Scenarios
<!-- description: Multi-source knowledge, memory initialization, error recovery, explore-execute-store. -->

### Multi-Source Knowledge

Combine brain docs + vector memory for complete context.

```
Trigger: Need comprehensive information about topic
Flow:
  1. Bash('brain docs "{keywords}"') → structured docs
  2. mcp__vector-memory__search_memories({query: "{keywords}", limit: 5}) → memory
  3. Merge: docs (primary) + memory (secondary)
  4. Fallback: if no docs, use memory + Explore agent

Example:
  Task: "Implement rate limiting"
  
  Step 1: brain docs "rate limiting api"
    → Found: .docs/api-rate-limits.md (structured spec)
  
  Step 2: mcp__vector-memory__search_memories("rate limiting middleware")
    → Found: Memory #42 "Used throttle middleware pattern"
    → Found: Memory #15 "Global rate limit broke multi-tenant"
  
  Merge:
    - Spec says: 100 req/min per user
    - Memory says: Use existing middleware, include user ID in key
    - Memory warns: Don't use global rate limit
```

---

### Docs → Memory → Task Workflow

Full knowledge pipeline.

```
Trigger: Starting complex task
Flow:
  1. DOCS: Bash('brain docs "{topic}" --headers=2') → find documentation
  2. READ: Read('{doc_path}') → understand specification
  3. MEMORY: mcp__vector-memory__search_memories("{topic}") → find related knowledge
  4. TASK: mcp__vector-task__task_get({task_id}) → understand task scope
  5. EXECUTE: Perform work
  6. STORE: mcp__vector-memory__store_memory({learnings}) → capture insights
  7. COMMENT: mcp__vector-task__task_update({comment: "See memory #ID"}) → link

Example:
  1. brain docs "authentication jwt" → .docs/auth.md
  2. Read('.docs/auth.md') → JWT flow spec
  3. mcp__vector-memory__search_memories("jwt token refresh") → Memory #10
  4. mcp__vector-task__task_get(42) → "Add JWT to API"
  5. Implement JWT authentication
  6. mcp__vector-memory__store_memory("JWT implementation: used firebase/php-jwt...")
  7. mcp__vector-task__task_update(42, comment: "Done. Pattern in memory #25")
```

---

### Memory Initialization

Scan project into memory for first-time context.

```
Trigger: New project or empty memory database
Flow:
  1. mcp__vector-memory__get_memory_stats({}) → check if fresh (total === 0)
  2. IF fresh → Full init | IF not empty → Augment existing
  
  Full init phases:
    Phase 1: Structure scan (identify areas)
    Phase 2: PARALLEL code exploration (src/, tests/)
    Phase 3: PARALLEL docs analysis (brain docs → DocumentationMaster)
    Phase 4: PARALLEL config exploration
    Phase 5: Synthesis (merge all findings)

Dense storage format:
  BAD: "The src/ directory contains 150 PHP files..."
  GOOD: "src|150php|MVC|App\\Models,App\\Http|Laravel11|eloquent"
  
  Format: path|files|pattern|namespaces|framework|features
```

---

### Memory Augmentation

Add to existing memory base.

```
Trigger: Memory already has content, need to add new areas
Flow:
  1. mcp__vector-memory__get_memory_stats({}) → check existing state
  2. mcp__vector-memory__search_memories("project structure") → see what's stored
  3. Identify gaps (missing areas, outdated info)
  4. Scan only missing areas
  5. store_memory with updates

Gotcha: Don't re-scan areas already covered. Check memory first.
```

---

### Error Recovery

Handle MCP failures gracefully.

```
Trigger: MCP tool call fails
Flow:
  1. Retry ONCE with same parameters (transient issue?)
  2. IF still fails:
     - Store failure to memory (category: "debugging", tags: ["failure"])
     - Log in task comment: "BLOCKED: {tool} failed. Error: {msg}"
     - Set status "pending" (NOT "stopped")
  3. Report to user with specific error

Error types:
  - MCP unavailable: Abort, report to user
  - Agent timeout: Skip area, continue with others
  - Empty results: Store minimal, proceed
  - Validation error: Check parameters, retry

Gotcha: NEVER set "stopped" on failure. "stopped" = permanently cancelled.
```

---

### Explore → Execute → Store Pattern

Standard task execution with knowledge capture.

```
Trigger: Starting any task
Flow:
  1. EXPLORE: mcp__vector-task__task_get({task_id})
     IF parent_id → mcp__vector-task__task_get({task_id: parent_id}) → context
     mcp__vector-task__task_list({parent_id: task_id}) → children
  2. MINE: mcp__vector-memory__search_memories({query: "{task_topic}", limit: 5})
  3. START: mcp__vector-task__task_update({task_id, status: "in_progress"})
  4. EXECUTE: Perform work, add comments for discoveries
  5. STORE: mcp__vector-memory__store_memory({content: "insights...", category: "..."})
  6. COMPLETE: mcp__vector-task__task_update({
       task_id,
       status: "completed",
       comment: "Done. Key findings in memory #ID.",
       append_comment: true
     })
```

---

### Memory-Driven Task Planning

Use memory to inform task approach.

```
Trigger: Planning complex task
Flow:
  1. mcp__vector-memory__search_memories({query: "{feature} implementation", limit: 5})
  2. mcp__vector-memory__search_memories({query: "{feature} failure", category: "debugging"})
  3. Extract:
     - Working patterns → Apply
     - Known failures → Avoid
     - Gotchas → Watch for
  4. Build task plan with extracted knowledge
  5. Include BLOCKED_APPROACHES in task content
```

---

### Task Comment with Memory Link

Preserve critical context in task comments.

```
Trigger: Important discovery during task execution
Flow:
  1. mcp__vector-memory__store_memory({content: "detailed finding..."})
  2. mcp__vector-task__task_update({
       task_id,
       comment: "Found: X causes Y. Details in memory #42.",
       append_comment: true
     })

Comment format examples:
  "Findings stored in memory #42, #43. See related #38."
  "Modified: src/Auth/Login.php:45-78. Created: tests/AuthTest.php"
  "BLOCKED: waiting for API spec. Resume when #15 completed."
  "Chose JWT over sessions. Rationale in memory #50."

Gotcha: Memory = PRIMARY storage. Comments = CRITICAL links only.
```

---
---

## Debugging Scenarios
<!-- description: Debug flow with memory capture, search for similar bugs, pattern extraction. -->

### Debug Flow with Memory Capture

Systematic debugging with knowledge preservation.

```
Trigger: Bug reported
Flow:
  1. mcp__vector-memory__store_memory({
       content: "Bug: {symptom}. Initial report details...",
       category: "debugging",
       tags: ["component", "bug-type"]
     })
  2. Investigate → store each discovery
  3. Find root cause → mcp__vector-memory__store_memory({
       content: "Root cause: {cause}. Why it happened...",
       category: "debugging"
     })
  4. Apply fix → mcp__vector-memory__store_memory({
       content: "Fix: {solution}. Pattern to prevent...",
       category: "bug-fix",
       tags: ["component", "prevention"]
     })
  5. Link with common tags for retrieval
```

---

### Search for Similar Bugs

Find previously solved similar issues.

```
Trigger: New bug with unclear cause
Flow:
  1. Multi-probe search:
     mcp__vector-memory__search_memories({query: "{symptom}"})
     mcp__vector-memory__search_memories({query: "{component} failure"})
     mcp__vector-memory__search_memories({query: "{error_message}"})
  2. Extract from bug-fix category results
  3. Apply similar fix or investigation approach

Example:
  Error: "SQLSTATE[HY000]: General error"
  Search: "SQLSTATE HY000", "database connection failure", "PDO error"
  Found: "Similar error caused by connection pool exhaustion"
  Apply: Check connection pool settings
```

---
---

## Cleanup Scenarios
<!-- description: Single/bulk delete, cleanup by age, preview before deletion. Brain hygiene strategies. -->

### Memory Staleness Detection

How to identify outdated or low-value memories.

```
Staleness signals:
  1. Code references to deleted/renamed files
  2. API patterns that no longer exist
  3. Deprecated library versions
  4. "TODO" or "temporary" in content
  5. Low access_count + old created_at
  6. Duplicate content (similarity >= 0.90)
  7. Contradicts newer memories

Detection workflow:
  1. mcp__vector-memory__search_memories({query: "TODO temporary deprecated", limit: 10})
  2. mcp__vector-memory__search_memories({query: "old approach legacy", limit: 10})
  3. For each: verify against current codebase
  4. Mark for deletion or update
```

---

### Retention Policies

What to keep vs what can be deleted.

```
ALWAYS KEEP (high retention):
  - Architecture decisions (category: architecture)
  - Security patterns (tags: security)
  - Failure patterns (category: debugging, tags: failure)
  - Project conventions (category: project-context)
  - Frequently accessed (access_count > 10)

CAN DELETE (low retention):
  - Temporary workarounds (tags: temporary, workaround)
  - Deprecated approaches (tags: deprecated)
  - Duplicate content (similarity >= 0.90)
  - Old unused (access_count = 0, days_old > 60)
  - Execution logs without insight

REVIEW BEFORE DELETE (medium retention):
  - Learning entries (category: learning) - may still be relevant
  - Bug fixes (category: bug-fix) - check if bug still exists
  - Tool usage (category: tool-usage) - tools may change
```

---

### Consolidation Patterns

Merge or update instead of delete when valuable.

```
DUPLICATE CONSOLIDATION:
  1. Search for duplicates: mcp__vector-memory__search_memories({query: "{topic}"})
  2. IF 2+ memories with similarity >= 0.90:
     - Keep the most complete one
     - Merge unique info from others
     - Delete duplicates

UPDATE IN PLACE:
  1. mcp__vector-memory__get_by_memory_id({memory_id})
  2. IF memory is partially outdated:
     - mcp__vector-memory__delete_by_memory_id({memory_id})
     - mcp__vector-memory__store_memory({content: "updated content", ...})
  3. Keep memory_id reference in task comments (may change)

DEPRECATED → SUPERSEDED:
  1. Store new approach: mcp__vector-memory__store_memory({content: "...", tags: ["supersedes-#15"]})
  2. Update old: mark as deprecated or delete
```

---

### Garbage Collection Triggers

When to initiate cleanup.

```
AUTOMATIC TRIGGERS:
  - Memory count > 80% of limit
  - mcp__vector-memory__get_memory_stats() shows high usage
  - After large import/restore operations

MANUAL TRIGGERS (agent should suggest):
  - After major refactoring (code patterns may be obsolete)
  - After library upgrades (API patterns may change)
  - After deleting files (file references may be stale)
  - Periodic: weekly/monthly brain hygiene session

TRIGGER DETECTION:
  IF memory_count > 8000 (for 10000 limit):
    - Report: "Memory usage at 80%. Consider cleanup."
    - Suggest: mcp__vector-memory__clear_old_memories({days_old: 30, max_to_keep: 5000})
```

---

### Brain Hygiene Workflow

Regular maintenance for healthy memory.

```
DAILY (automatic):
  - search-before-store prevents duplicates
  - quality content rules prevent noise

WEEKLY (agent-initiated):
  Trigger: User asks "what did we work on?" or session end
  Flow:
    1. mcp__vector-memory__list_recent_memories({limit: 20})
    2. Identify low-quality entries (short, vague, no context)
    3. Suggest consolidation or deletion

MONTHLY (explicit maintenance):
  Trigger: User requests "cleanup" or memory > 80% capacity
  Flow:
    1. mcp__vector-memory__get_memory_stats({})
    2. mcp__vector-memory__search_memories({query: "TODO temporary deprecated", limit: 20})
    3. mcp__vector-memory__search_memories({query: "old legacy workaround", limit: 20})
    4. Present cleanup candidates
    5. mcp__vector-memory__clear_old_memories({days_old: 30, max_to_keep: 1000})

QUARTERLY (deep clean):
  Trigger: Major project changes or manual request
  Flow:
    1. Full memory audit by category
    2. Validate code references still exist
    3. Consolidate similar memories
    4. Update outdated entries
    5. Archive rarely-used but valuable memories
```

---

### Memory Quality Gates

Prevent garbage from entering.

```
BEFORE STORE:
  1. Content length < 50 chars → REJECT or EXPAND
  2. No WHAT/WHY/WHEN → REJECT or EXPAND
  3. Similar exists (similarity >= 0.90) → SKIP or UPDATE
  4. Contains only error logs → EXTRACT pattern, not logs

QUALITY INDICATORS (good):
  - Problem → Solution → Pattern → Gotcha structure
  - Specific file/class references
  - "When to use" guidance
  - Reusable patterns

GARBAGE INDICATORS (bad):
  - Raw error logs without analysis
  - "Fixed bug" without explaining HOW
  - Code snippets without context
  - Temporary workarounds without "WHY temporary"
```

---

### Single Memory Delete

Remove specific memory.

```
Trigger: Memory is outdated or incorrect
Flow:
  1. mcp__vector-memory__get_by_memory_id({memory_id}) → confirm content
  2. User confirmation required
  3. mcp__vector-memory__delete_by_memory_id({memory_id})

Gotcha: Does NOT decrement tag frequencies. Tags remain in canonical_tags.
```

---

### Multi-Delete by IDs

Remove multiple specific memories at once.

```
Trigger: Several memories need removal
Flow:
  1. For each ID: mcp__vector-memory__get_by_memory_id({memory_id}) → preview
  2. Display all memories to be deleted
  3. User confirmation required for entire batch
  4. For each ID: mcp__vector-memory__delete_by_memory_id({memory_id})
  5. Report total deleted

Example:
  /mem:cleanup ids=15,16,17
  
  Preview:
    #15 [code-solution] "N+1 fix pattern..."
    #16 [debugging] "Connection timeout investigation..."
    #17 [learning] "Deprecated approach..."
  
  Confirm: "DELETE all 3 memories? (yes/no)"
  Execute: Delete each in sequence
  Report: "Deleted 3 memories successfully."

Gotcha: Preview ALL before confirming. No undo.
```

---

### Bulk Cleanup by Age

Remove old, unused memories.

```
Trigger: Database size concern or maintenance
Flow:
  1. mcp__vector-memory__get_memory_stats({}) → check current state
  2. Calculate: how many would be deleted
  3. Preview: show estimated impact
  4. User confirmation required
  5. mcp__vector-memory__clear_old_memories({
       days_old: 30,
       max_to_keep: 1000
     })
  6. Report: deleted count, remaining count

Preview format:
  Current total: 1500 memories
  Settings: days_old=30, max_to_keep=1000
  Would delete: ~500 memories
  Would keep: ~1000 memories
  Proceed? (yes/no)

Priority (keeps first):
  1. Frequently accessed (high access_count)
  2. Recent (created_at)
  3. Removes: old + low access_count
```

---
---

## Gates Rules Scenarios
<!-- description: CRITICAL and HIGH priority rules. MCP-only, multi-probe, search-before-store. -->

Critical rules and guardrails for memory operations. Violations break system integrity.

Use `query="critical"` or `query="high"` to filter by priority.

### Iron Rules

**[CRITICAL] MCP-Only Access**
```
RULE: ALL memory operations MUST use MCP tools. NEVER access ./memory/ directly.
WHY: MCP ensures embedding generation and data integrity.
VIOLATION: Use mcp__vector-memory__* tools exclusively.

BAD:  Read('./memory/vector_memory.db')
GOOD: mcp__vector-memory__search_memories({query: "..."})
```

**[CRITICAL] Multi-Probe Mandatory**
```
RULE: Complex tasks require 2-3 search probes minimum. Single query = missed context.
WHY: Vector search has semantic radius. Multiple probes cover more knowledge space.
VIOLATION: Decompose query into aspects. Execute multiple focused searches.

BAD:  mcp__vector-memory__search_memories({query: "fix auth problem"})
GOOD: mcp__vector-memory__search_memories({query: "JWT token invalid"})
      mcp__vector-memory__search_memories({query: "token refresh flow"})
      mcp__vector-memory__search_memories({query: "auth middleware"})
```

---

### Security Rules (CRITICAL)

**[CRITICAL] No Secrets in Storage**
```
RULE: NEVER store secrets, credentials, tokens, passwords, API keys, PII, or connection strings in vector memory.
WHY: Vector memory is persistent, searchable, and shared across agents and sessions. Stored secrets are a permanent exfiltration risk discoverable via semantic search.
VIOLATION: Review content before store_memory. Strip all literal secret values. Keep only key names and descriptions.

FORBIDDEN content:
  - .env values: DB_HOST=192.168.1.5, API_KEY=sk-abc123
  - Connection strings: mysql://user:pass@host/db
  - Tokens: Bearer eyJhbGciOiJIUzI1NiIs...
  - Passwords: password=Secret123
  - Private URLs: https://user:pass@internal.api/...
  - Certificates, private keys

ACCEPTABLE:
  - "Updated DB_HOST in .env for production"
  - "Rotated API_KEY for payment service"
  - "Added JWT_SECRET to .env (32 chars)"

BAD:  mcp__vector-memory__store_memory({content: "DB_HOST=192.168.1.5, API_KEY=sk-abc123"})
GOOD: mcp__vector-memory__store_memory({content: "Database config: updated DB_HOST for production environment"})
```

**[CRITICAL] No Secret Exfiltration**
```
RULE: NEVER output sensitive data to chat/response: .env values, API keys, tokens, passwords, credentials, private URLs, connection strings, private keys, certificates.
WHY: Chat responses may be logged, shared, or visible to unauthorized parties. Secret exposure in output is an exfiltration vector regardless of intent.
VIOLATION: Redact all secret values before displaying. Show key names only, mask values as "***".

When reading config/.env for CONTEXT:
  - Extract key NAMES and STRUCTURE only
  - Never raw values
  - If user asks to show .env: show key names, mask values as "***"

If error output contains secrets:
  - Redact before displaying
  - Strip before storing to memory

BAD:  get_by_memory_id({memory_id}) → displays "API_KEY=sk-abc123"
GOOD: get_by_memory_id({memory_id}) → displays "API_KEY=***"
```

---

### High Priority Rules

**[HIGH] Search Before Store**
```
RULE: ALWAYS search for similar content before storing. Duplicates waste space.
WHY: Prevents memory pollution. Keeps knowledge base clean and precise.
VIOLATION: mcp__vector-memory__search_memories({query: "{insight}", limit: 3}) → evaluate → store if unique

Flow:
  1. Search with content summary
  2. IF similarity >= 0.90 → SKIP or UPDATE via delete+store
  3. IF similarity < 0.90 → STORE as new
```

**[HIGH] Semantic Handoff**
```
RULE: When delegating, include memory search hints as text. Never assume next agent knows what to search.
WHY: Agents share memory but not session context. Text hints enable continuity.
VIOLATION: Add to delegation: "Memory hints: {relevant_terms}, {domain}, {patterns}"

Example delegation:
  "TASK: Implement user auth
   MEMORY HINTS: JWT authentication, token refresh, session management
   CONTEXT: Memory #45 has API design decision (use this approach)"
```

**[HIGH] Actionable Content**
```
RULE: Store memories with WHAT, WHY, WHEN-TO-USE. Raw facts are useless without context.
WHY: Future retrieval needs self-contained actionable knowledge.
VIOLATION: Rewrite: include problem context, solution rationale, reuse conditions.

BAD:  "Fixed N+1 in UserController"
GOOD: "UserController@store: N+1 on roles. Fix: ->with('roles'). 
       Pattern: eager load relationships. Gotcha: ->with() before ->get()"
```

---

### Efficiency Guards

**Probe Limits**
```
Max 3 search probes per task phase (pre/during/post)
Limit 3-5 results per probe (total ~10-15 memories max)
Extract only actionable lines, not full memory content
If memory unhelpful after 2 probes, proceed without - avoid rabbit holes
```

**Query Decomposition**
```
Complex: "How to implement user auth with JWT in Laravel"
  → Probe 1: "JWT authentication Laravel"
  → Probe 2: "user login security"
  → Probe 3: "token refresh pattern"

Debugging: "Why tests fail"
  → Probe 1: "test failure {module}"
  → Probe 2: "similar bug fix"
  → Probe 3: "{error_message}"

Architecture: "Best approach for X"
  → Probe 1: "X implementation"
  → Probe 2: "X trade-offs"
  → Probe 3: "X alternatives"
```

---

### Cross-Reference with Task MCP Rules

These rules coordinate with Vector Task MCP rules:

| Memory Rule | Task Rule | Coordination |
|-------------|-----------|--------------|
| `no-secrets-in-storage` | `no-secrets-in-comments` | NEVER store secrets - both systems |
| `no-secret-exfiltration` | `no-secret-exfiltration` | NEVER output secrets - both systems |
| `search-before-store` | `memory-primary-comments-critical` | Memory = PRIMARY, comments = LINKS |
| `semantic-handoff` | `explore-before-execute` | Pass memory hints when delegating |
| `actionable-content` | `comment-strategy` | Detailed → memory, reference → comment |
| `mcp-only-access` | `mcp-only-access` | Same rule, different MCP |

---

### Six Constitutional Gates

Six mandatory gates that protect system integrity. Each gate is a self-contained enforcement point.

---

#### Gate 1: MCP-JSON-ONLY

```
RULE: ALL MCP calls MUST use JSON-RPC via MCP tools. NEVER direct database/file access.
WHY: MCP ensures embedding generation, validation, and data integrity.
TRIGGER: Any memory operation.

ENFORCEMENT:
  BEFORE: Verify using mcp__vector-memory__*
  AFTER: If direct access detected → REJECT + escalate

BAD:  sqlite3.connect('./memory/vector_memory.db')
GOOD: mcp__vector-memory__search_memories({query: "..."})
```

---

#### Gate 2: Lightweight Lawyer Gate

```
RULE: ALL proposals MUST pass 5-check verification before storage.
WHY: Prevents low-quality proposals from polluting memory.
TRIGGER: Self-improvement proposals, instruction changes.

CHECKLIST:
  1. Iron Rules: Does NOT violate any → PASS
  2. Measurable: Has specific metric → PASS
  3. Reversible: Has rollback plan → PASS
  4. Scope: Does NOT expand task → PASS
  5. Security: Does NOT weaken (or improves) → PASS

ENFORCEMENT:
  IF 5/5 PASS → Store proposal
  IF security/iron_rules/scope FAIL → REJECT
  ELSE → CLARIFY

See: "Lightweight Lawyer Gate Scenarios" section for full details.
```

---

#### Gate 3: Constitutional Learn Protocol

```
RULE: ALL failures with trigger signals MUST store lessons to memory.
WHY: Captures failure patterns for future prevention.
TRIGGER: retries > 0, stuck tag, validation-fix, blocked, user correction.

STEPS:
  1. Detect trigger signal in task
  2. Search memory for duplicates
  3. IF unique → Store with format:
     FAILURE: {what}
     ROOT CAUSE: {why}
     FIX: {how}
     PREVENTION: {pattern}
     CONTEXT: Task #{id}
  4. Link memory ID in task comment

ENFORCEMENT:
  AFTER task completion: IF trigger detected AND no lesson stored → ESCALATE

See: "Constitutional Learn Protocol Scenarios" section for full details.
```

---

#### Gate 4: Category Discipline Contract

```
RULE: Categories are FIXED. NEVER create new categories dynamically.
WHY: Prevents category drift and search fragmentation.
TRIGGER: Any memory storage.

ALLOWED CATEGORIES (Memory MCP):
  code-solution, bug-fix, architecture, learning, debugging,
  performance, security, project-context, other

ENFORCEMENT:
  BEFORE storage: IF category not in allowed list → REJECT
  AFTER storage: IF category mismatch detected → DELETE + re-store

BAD:  store_memory({category: "new-feature", ...})
GOOD: store_memory({category: "code-solution", ...})
```

---

#### Gate 5: Cookbook-First Gate

```
RULE: When uncertain, CALL cookbook() BEFORE assuming or searching elsewhere.
WHY: Cookbook contains authoritative patterns, tools, and best practices.
TRIGGER: Uncertainty about tools, patterns, rules, or procedures.

STEPS:
  1. IF uncertain → mcp__vector-memory__cookbook()
  2. IF answer found → Apply pattern
  3. IF not found → THEN search memory/docs/web

ENFORCEMENT:
  IF question answered by cookbook BUT not called first → WARN
  IF repeated violations → ESCALATE

PRIORITY ORDER:
  1. cookbook() - authoritative
  2. vector memory - context-specific
  3. external docs - supplementary
```

---

#### Gate 6: Failure Escalation Gate

```
RULE: Failures MUST escalate according to severity. NEVER silently continue.
WHY: Prevents error cascades and ensures visibility.
TRIGGER: Any failure, error, or unexpected state.

ESCALATION LEVELS:

| Severity | Condition | Action |
|----------|-----------|--------|
| CRITICAL | Data loss risk, security breach | STOP + ALERT human immediately |
| HIGH | System integrity at risk | STOP + Log + Store lesson |
| MEDIUM | Task failure, retry possible | RETRY (max 3) + Store lesson |
| LOW | Minor issue, workaround exists | LOG + Continue |

ENFORCEMENT:
  IF CRITICAL failure AND continued → SEVERE VIOLATION
  IF retries > 3 AND no escalation → VIOLATION
  IF lesson stored but no retry/escalation → INCOMPLETE

ESCALATION PATH:
  Agent → Brain → Human (CRITICAL only)
```

---

### Memory-Task Workflow (Cross-MCP)

```
PRE-TASK (Task MCP triggers Memory MCP):
  1. mcp__vector-task__task_get({task_id}) → understand scope
  2. mcp__vector-memory__search_memories({query: "{task_topic}"}) → solutions
  3. mcp__vector-memory__search_memories({query: "{task_topic} failure", category: "debugging"}) → avoid

POST-TASK (Memory MCP stores, Task MCP links):
  1. mcp__vector-memory__search_memories({query: "{insight}"}) → check duplicates
  2. IF unique: mcp__vector-memory__store_memory({content, category, tags})
  3. mcp__vector-task__task_update({task_id, comment: "See memory #ID", append_comment: true})
```

---
---

## Task Integration Scenarios
<!-- description: Memory-Task workflow, cross-reference format, pre/post-task patterns with Task MCP. -->

Integration with Vector Task MCP (`mcp__vector-task__*` tools). Memory is PRIMARY storage for reusable knowledge. Task comments contain CRITICAL links only.

### Memory Categories for Tasks

| Category | When to Use | Task Type |
|----------|-------------|-----------|
| `code-solution` | Working implementations, patterns | feature, refactor, test |
| `bug-fix` | Root causes, fixes applied | bugfix, hotfix |
| `architecture` | Design decisions, trade-offs | spike, research |
| `learning` | Insights, discoveries | research, docs |
| `debugging` | Troubleshooting steps | bugfix (failed approaches) |
| `project-context` | Project-specific conventions | chore, docs |

---

### Memory Tags for Task Integration

| Tag Type | Examples | Usage |
|----------|----------|-------|
| CONTENT | `solution`, `failure`, `decision`, `insight`, `workaround`, `deprecated` | What kind of content |
| SCOPE | `reusable`, `project-wide`, `module-specific`, `temporary` | Breadth of applicability |

Formula: 1 CONTENT + 0-1 SCOPE. Example: `["solution", "reusable"]` or `["failure", "module-specific"]`

---

### Pre-Task Memory Mining

BEFORE starting work on task, mine memory aggressively.

```python
# Get task context
task = mcp__vector-task__task_get(task_id=42)

# Multi-probe search for solutions
solutions = mcp__vector-memory__search_memories(
    query=f"{task['title']} solution pattern",
    category="code-solution",
    limit=5
)

# Search for related failures (to AVOID these approaches)
failures = mcp__vector-memory__search_memories(
    query=f"{task['title']} failure error bug",
    category="debugging",
    limit=5
)

# Search for architecture decisions (constraints to follow)
decisions = mcp__vector-memory__search_memories(
    query=f"{task['title']} architecture decision",
    category="architecture",
    limit=3
)

# Extract blocked approaches from failures
blocked_approaches = []
for mem in failures["results"]:
    if "does not work" in mem["content"] or "failed" in mem["content"]:
        blocked_approaches.append(mem["content"][:100])

# Use context in task execution
print(f"Found {len(solutions['results'])} solutions")
print(f"AVOID these approaches: {blocked_approaches}")
```

---

### Post-Task Memory Storage

AFTER task completion, store UNIQUE insights.

```python
# ALWAYS search before store to prevent duplicates
existing = mcp__vector-memory__search_memories(
    query="JWT refresh token rotation pattern",
    limit=3
)

if not existing["results"]:
    # Store new insight with full context
    mcp__vector-memory__store_memory(
        content="JWT Refresh Token Rotation:\n"
                "Problem: Tokens don't rotate, security risk\n"
                "Solution: Return new refresh token with each access token refresh\n"
                "Pattern: Store previous token hash, invalidate on next refresh\n"
                "When to use: Any JWT-based auth system\n"
                "Gotchas: Handle concurrent requests with token family tracking\n"
                "Context: Discovered during task #42 for user auth system",
        category="code-solution",
        tags=["solution", "reusable"]
    )
else:
    # Similar exists - skip or update via delete+store
    print(f"Similar memory exists: #{existing['results'][0]['id']}")
```

---

### Task-Memory Workflow Cycle

Complete workflow: Task → Memory Mining → Execute → Store → Link.

```python
# 1. Get task
task = mcp__vector-task__task_get(task_id=42)

# 2. Mine memory for context
context = mcp__vector-memory__search_memories(
    query=f"{task['title']} {task['content'][:50]}",
    limit=5
)

# 3. Start task
mcp__vector-task__task_update(
    task_id=42,
    status="in_progress",
    comment=f"Memory context: {[f'#{m[\"id\"]}' for m in context['results']]}",
    append_comment=True
)

# 4. Execute task (do the work...)
# ... implementation ...

# 5. Store findings to memory
new_memory = mcp__vector-memory__store_memory(
    content="Key insight from task #42...",
    category="code-solution",
    tags=["solution", "reusable"]
)

# 6. Link memory in task comment
mcp__vector-task__task_update(
    task_id=42,
    status="completed",
    comment=f"Completed. Insights stored in memory #{new_memory['memory_id']}",
    append_comment=True
)
```

---

### Cross-Reference Format (Bidirectional)

Both systems use `#N` format for cross-references:

```
Memory → Task: "Discovered during task #42"
Task → Memory: "Insights stored in memory #15"
```

Consistent format enables:
- Parsing comment for linked IDs
- Fetching related context
- Building knowledge graph

---

### Inter-Agent Memory Handoff

When delegating to another agent, pass memory hints as TEXT.

```python
# Agents share memory but not session context
delegation_prompt = """
TASK: Implement user authentication

MEMORY HINTS (search these in mcp__vector-memory__search_memories):
- "JWT authentication Laravel flow"
- "token refresh pattern"
- "session management security"
- "auth error handling"

CONTEXT FROM PREVIOUS AGENT:
- Memory #45: API design decision (use this approach)
- Memory #46: Error handling pattern (follow this)
- Memory #47: Approach X failed (AVOID)

AFTER COMPLETION:
1. Store your findings to memory (category: code-solution)
2. Update task comment with new memory IDs
"""

# Delegate with context
Task(subagent_type="general", prompt=delegation_prompt)
```

---

### Link Memory in Task Comments

During execution, document memory discoveries in task comments.

```python
# Link discovered memories
mcp__vector-task__task_update(
    task_id=42,
    comment="Research phase:\n"
            "- Found pattern in memory #30 (caching strategy)\n"
            "- Memory #31 shows why approach X failed (AVOID)\n"
            "- Memory #32 has working solution for similar problem",
    append_comment=True
)

# After completion, store and link
new_mem = mcp__vector-memory__store_memory(
    content="New pattern: eager load with ->with() prevents N+1",
    category="code-solution",
    tags=["solution", "reusable"]
)

mcp__vector-task__task_update(
    task_id=42,
    comment=f"Completed. Pattern stored in memory #{new_mem['memory_id']}",
    append_comment=True,
    status="completed"
)
```

---

### Memory-Aware Task Decomposition

Before decomposing, check memory for patterns.

```python
task = mcp__vector-task__task_get(task_id=42)

# Search for decomposition patterns
patterns = mcp__vector-memory__search_memories(
    query="task decomposition pattern similar features",
    category="architecture",
    limit=3
)

# Use patterns to inform decomposition
if patterns["results"]:
    # Apply learned decomposition pattern
    subtasks = apply_decomposition_pattern(patterns["results"][0])
else:
    # Default decomposition
    subtasks = default_decompose(task)

# Create subtasks
mcp__vector-task__task_create_bulk(tasks=subtasks)
```

---

### Memory Categories by Task Type

Choose memory category based on task type.

```python
task_type_to_category = {
    "feature": "code-solution",     # New implementations
    "bugfix": "bug-fix",            # Bug fixes and root causes
    "refactor": "code-solution",    # Improved patterns
    "research": "learning",         # Discoveries
    "docs": "project-context",      # Documentation conventions
    "test": "code-solution",        # Test patterns
    "chore": "project-context",     # Project maintenance
    "spike": "learning",            # Experiments
    "hotfix": "bug-fix"             # Urgent fixes
}

# Store with appropriate category
task = mcp__vector-task__task_get(task_id=42)
category = task_type_to_category.get(extract_task_type(task), "code-solution")

mcp__vector-memory__store_memory(
    content="Insight content...",
    category=category,
    tags=["solution", "reusable"]
)
```

---

### Memory → Task Signal (When to Create Tasks)

Memory content can signal need for task creation.

```python
# When storing insight that needs implementation
mcp__vector-memory__store_memory(
    content="Pattern discovered that should be applied to payment module. "
            "See task #42 for original context. "
            "Recommendation: Create task to apply this to BillingService.",
    category="code-solution",
    tags=["solution", "needs-implementation"]  # Signal for task creation
)

# Later, when reviewing memories
results = mcp__vector-memory__search_memories(
    query="needs-implementation",
    tags=["needs-implementation"],
    limit= 10
)

for mem in results["results"]:
    if "needs-implementation" in mem["tags"]:
        # Extract recommendation and create task
        mcp__vector-task__task_create(
            title="Apply pattern from memory #" + str(mem["id"]),
            content=f"Implement pattern discovered in memory #{mem['id']}",
            estimate=2.0,
            tags=["feature", "from-memory"]
        )
```

---

### Comment Context Parsing

Parse task comment for accumulated context.

```python
import re

task = mcp__vector-task__task_get(task_id=42)
comment = task.get("comment", "")

# Extract memory IDs (#NNN patterns)
memory_ids = re.findall(r'#(\d+)', comment)

# Extract file paths
file_paths = re.findall(r'(src/[\w/\.]+|tests/[\w/\.]+)', comment)

# Extract blockers
if "BLOCKED" in comment:
    blockers = re.findall(r'BLOCKED:\s*(.+?)(?:\n|$)', comment)

# Extract decisions
if "DECISION:" in comment or "chose" in comment:
    decisions = re.findall(r'(?:DECISION|chose):\s*(.+?)(?:\n|$)', comment, re.IGNORECASE)

# Use extracted context
print(f"Memory IDs: {memory_ids}")
print(f"Files: {file_paths}")
print(f"Blockers: {blockers}")
```

---

### Parse Comment Context in Execution

Use comment context during task execution.

```python
task = mcp__vector-task__task_get(task_id=42)
comment = task.get("comment", "")

# Parse for memory IDs
memory_ids = re.findall(r'#(\d+)', comment)

# Fetch linked memories for context
for mem_id in memory_ids:
    memory = mcp__vector-memory__get_by_memory_id(memory_id=int(mem_id))
    # Use memory content for context

# Check for failures to avoid
if "failed" in comment.lower():
    failures = re.findall(r'(?:failed|error):?\s*(.+?)(?:\n|$)', comment, re.IGNORECASE)
    print(f"Previous failures: {failures}")

# Check for blockers
if "BLOCKED" in comment:
    blockers = re.findall(r'BLOCKED:\s*(.+?)(?:\n|$)', comment)
    # Check if blocker resolved before proceeding
```

---
## Technical Debt Integration Scenarios
<!-- description: Memory patterns for technical debt lifecycle - detection, tracking, knowledge storage. -->

How Memory MCP supports technical debt management across its lifecycle.

### Debt Knowledge Storage

Store patterns and solutions for debt-related work.

```python
# Store debt pattern for reuse
mcp__vector-memory__store_memory(
    content="""DEBT PATTERN: N+1 Query in Laravel Eloquent

Symptoms:
- Slow page load with related data
- Many duplicate queries in debugbar
- ->get() in loop context

Detection:
- Check debugbar query count
- Look for ->get() without ->with()
- Find lazy loading in serialization

Fix:
- Add ->with('relation') before ->get()
- Use lazy eager loading: ->load('relation')
- For API: Resource collection with relations

Prevention:
- Always eager load in controllers
- Add query count assertion in tests
- Static analysis rule for lazy loading""",
    category="code-solution",
    tags=["debt-pattern", "n-plus-1", "performance", "laravel"]
)

# Store incident caused by debt
mcp__vector-memory__store_memory(
    content="""INCIDENT: Payment timeout from debt

Debt: PaymentService N+1 query (known, deprioritized)
Impact: 15% transactions timeout under load
Cost: 4h emergency fix + 2h post-mortem
Root Cause: Debt scoring underestimated velocity impact

Learning: Debt affecting user-facing paths = higher priority
Action: Update debt scoring to include user impact""",
    category="bug-fix",
    tags=["incident", "debt:emergency", "payment", "learning"]
)

# Store refactoring strategy
mcp__vector-memory__store_memory(
    content="""REFACTOR STRATEGY: Quarantine Pattern

When to use: Large legacy module, high risk of breaking changes

Steps:
1. Quarantine: Move to Legacy/, add deprecation warnings
2. Block: Static analysis prevents new usage
3. Rewrite: Create new implementation with tests
4. Migrate: Update call sites with feature flags
5. Remove: Delete legacy after 2-week cooldown

Time: 2-4 weeks total depending on module size
Risk: Low (can rollback via feature flags)""",
    category="architecture",
    tags=["debt:repayment", "quarantine", "refactor", "strategy"]
)
```

### Debt Detection via Search

Multi-probe search for debt patterns.

```python
# Find similar debt patterns
patterns_1 = mcp__vector-memory__search_memories(query="N+1 query slow loading", limit=3)
patterns_2 = mcp__vector-memory__search_memories(query="eager loading performance", limit=3)
patterns_3 = mcp__vector-memory__search_memories(query="database optimization laravel", limit=3)

# Find debt incidents for learning
incidents = mcp__vector-memory__search_memories(
    query="incident debt root cause",
    category="bug-fix",
    limit=5
)

# Find refactoring strategies
strategies = mcp__vector-memory__search_memories(
    query="refactor strategy legacy module",
    category="architecture",
    limit=3
)
```

### Debt Velocity Tracking

Track debt trends over time.

```python
# Weekly debt velocity snapshot
mcp__vector-memory__store_memory(
    content="""DEBT VELOCITY: Week 8, 2026

Repaid: 3 tasks (8h total)
- PaymentService N+1 fix
- UserService test coverage
- Dependency update

Introduced: 1 task (2h)
- Quick fix with TODO in OrderService

Net: -6h (positive trend)
Trend: Improving vs Week 7 (+2h debt)

Velocity this quarter: +12h net reduction
On track for 50% reduction goal.""",
    category="project-context",
    tags=["debt:velocity", "metrics", "week-8", "quarterly"]
)

# Search velocity history
history = mcp__vector-memory__search_memories(
    query="debt velocity metrics",
    tags=["debt:velocity"],
    limit=10
)
```

### Prevention Knowledge

Store prevention measures and decisions.

```python
# Store decision why debt was introduced
mcp__vector-memory__store_memory(
    content="""DEBT DECISION: OrderService quick fix

What: Added TODO and workaround for payment timeout
Why: Critical feature deadline, no time for proper fix
Trade-off: 2h saved now, estimated 6h to fix properly later
Risk: Medium - affects payments but has fallback
Repayment: Link to task #156, scheduled Sprint 10

Decision maker: Team lead
Date: 2026-02-15""",
    category="architecture",
    tags=["debt:intentional", "decision", "order-service"]
)

# Store DoD checklist item
mcp__vector-memory__store_memory(
    content="""DOD: No untracked debt

Definition of Done checklist item:
- No TODO without linked task
- If debt introduced: tag debt:intentional
- Add estimated repayment time
- Get approval from tech lead

Rationale: Invisible debt accumulates silently
Enforcement: PR template checkbox""",
    category="project-context",
    tags=["debt:prevention", "dod", "process"]
)
```

### Task Integration for Debt

Link memory findings to task tracking.

```python
# Found debt pattern → create task
pattern = mcp__vector-memory__search_memories(query="god object large class", limit=1)
if pattern:
    mcp__vector-task__task_create(
        title="Audit for god objects",
        content=f"Based on pattern in memory #{pattern[0]['id']}. Scan for classes > 500 lines.",
        estimate=2.0,
        tags=["debt:detection", "refactor"]
    )

# After debt fix → store learning
mcp__vector-memory__store_memory(
    content="""DEBT FIX: UserService refactoring complete

Original: 1200 lines, 45 methods, circular deps
After: 3 services, 150-300 lines each, clean deps

Time: 16h (estimated 12h, +33% due to unexpected coupling)

Learnings:
- Circular deps harder to break than expected
- Add time buffer for legacy refactors
- Tests essential BEFORE refactor""",
    category="learning",
    tags=["debt:repayment", "refactor", "learning"]
)

# Link memory to task
mcp__vector-task__task_update(
    task_id=42,
    comment="Refactoring complete. Learnings stored in memory #78.",
    append_comment=True
)
```

### Debt Categories Reference

| Category | Usage | Tags |
|----------|-------|------|
| `code-solution` | Debt patterns, fixes | debt-pattern, smell:* |
| `bug-fix` | Debt incidents | incident, debt:emergency |
| `architecture` | Strategies, decisions | debt:intentional, strategy |
| `learning` | Lessons from debt work | debt:learning |
| `project-context` | Metrics, processes | debt:velocity, dod |

### Debt Tag Taxonomy (Memory)

```
debt-pattern       - Reusable debt detection/fix patterns
debt:intentional   - Decisions to introduce debt
debt:emergency     - Incidents caused by debt
debt:repayment     - Refactoring strategies
debt:prevention    - Process/measures to prevent debt
debt:velocity      - Trend tracking over time
debt:learning      - Lessons from debt work
debt:detection     - Active hunting patterns
debt:blocking      - Debt blocking other work
```

---
## Reference: Brain Ecosystem Tags
<!-- description: Tag reference tables - memory tags, task tags, cognitive/strict levels, CLI commands. -->

### Memory Tags (Vector Memory MCP)

```
CONTENT: pattern, solution, failure, decision, insight, workaround, deprecated
SCOPE: project-wide, module-specific, temporary, reusable
```

### Memory Categories (Vector Memory MCP)

```
code-solution   → Working implementations, patterns
bug-fix         → Bug fixes with root causes
architecture    → Design decisions, trade-offs
learning        → Insights, discoveries
debugging       → Troubleshooting steps
performance     → Optimizations, benchmarks
security        → Vulnerabilities, fixes
project-context → Project-specific conventions
```

### Task Tags (Vector Task MCP - for integration)

```
WORKFLOW: decomposed, validation-fix, blocked, stuck, needs-research, parallel-safe, atomic, manual-only, regression
TYPE: feature, bugfix, refactor, research, docs, test, chore, spike, hotfix
DOMAIN: backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration
STRICT: strict:relaxed, strict:standard, strict:strict, strict:paranoid
COGNITIVE: cognitive:minimal, cognitive:standard, cognitive:deep, cognitive:exhaustive
```

### Task MCP Tools Reference

| Tool | Purpose | Common Usage |
|------|---------|--------------|
| `mcp__vector-task__task_get` | Get task by ID | Read task context |
| `mcp__vector-task__task_create` | Create new task | New work items |
| `mcp__vector-task__task_create_bulk` | Create multiple tasks | Decomposition |
| `mcp__vector-task__task_update` | Update task | Status, comment, tags |
| `mcp__vector-task__task_list` | Search/list tasks | Find related work |
| `mcp__vector-task__task_next` | Get next task | Smart selection |
| `mcp__vector-task__task_stats` | Task statistics | Progress tracking |
| `mcp__vector-task__task_delete` | Delete task | Cleanup |

### Cognitive Level Selection (Reference)

Calibrates analysis/research depth for tasks.

| Level | Use For | Memory Probes | Research | Agents |
|-------|---------|---------------|----------|--------|
| `cognitive:minimal` | Trivial: typo, rename, config | 0-1 | None | 0 |
| `cognitive:standard` | Normal: feature, known fix | 2-3 | On error | 2-3 |
| `cognitive:deep` | Complex: new architecture, optimization | 3-5 | Required | 3-5 |
| `cognitive:exhaustive` | Critical: security audit, unknown territory | 5+ | Exhaustive | Max |

### Strict Mode Selection (Reference)

Calibrates validation intensity.

| Level | Use For | Tests | Coverage | Static Analysis |
|-------|---------|-------|----------|-----------------|
| `strict:relaxed` | Cosmetic: README, comments | Skip non-critical | - | - |
| `strict:standard` | Normal: most features | Full suite | 80% | Standard |
| `strict:strict` | Critical: auth, payments, migrations | Full + edge | 100% | Strict |
| `strict:paranoid` | Security: credentials, destructive ops | Everything | 100% | Paranoid + manual |

### Safety Escalation (Auto-Override)

File patterns automatically escalate strict level:

```
auth/, guards/, policies/        → strict:strict minimum
payments/, billing/, stripe/     → strict:strict minimum
.env, credentials, secrets       → strict:paranoid minimum
migrations/, schema              → strict:strict minimum
CI/, .github/, Dockerfile        → strict:standard minimum
```

### Structured Tags (Colon Tags)

Only allowed prefixes:
```
type, domain, strict, cognitive, batch, module, vendor, priority, scope, layer
```

Examples:
```
type:refactor     ✓
module:billing    ✓
vendor:stripe     ✓
random:stuff      ✗ (invalid prefix)
```

### Brain Docs CLI Reference

```
Quick:
  brain docs                    List all docs
  brain docs "api auth"         Search keywords (OR logic)
  brain docs --limit=10         Max results

With options:
  brain docs "api" --headers=2  Include H1+H2 structure
  brain docs "api" --stats      Include file stats
  brain docs "api" --snippets   Include content previews
  brain docs "api" --code       Extract code blocks
  brain docs "api" --matches    Show keyword locations

Verbose:
  brain docs -v                 Normal output
  brain docs -vv                More verbose
  brain docs -vvv               Debug/internals
```

### Cookbook Quick Reference

```
mcp__vector-memory__cookbook()                                        → init (FIRST READ)
mcp__vector-memory__cookbook(include="init")                          → init (explicit)
mcp__vector-memory__cookbook(include="categories")                    → list categories with keys
mcp__vector-memory__cookbook(include="cases", case_category="search") → by key (exact)
mcp__vector-memory__cookbook(include="cases", case_category="gates-rules") → by key
mcp__vector-memory__cookbook(include="cases", query="JWT")            → search in cases
mcp__vector-memory__cookbook(include="docs", level=2)                 → docs by level
mcp__vector-memory__cookbook(include="docs", query="tag", level=2)    → search in docs
mcp__vector-memory__cookbook(include="all", query="error")            → search everywhere
mcp__vector-memory__cookbook(include="cases", query="X", limit=5, offset=0) → paginated
mcp__vector-memory__cookbook(include="all", level=3)                  → everything (debug)
```

---

## Constitutional Learn Protocol Scenarios
<!-- description: Mandatory failure pattern storage. Trigger signals, format, category discipline. -->

Critical protocol for storing failure patterns. Memory is PRIMARY storage for lessons.

### [CRITICAL] Trigger Signals (from Task MCP)

Store lesson when Task MCP reports ANY:

| Signal | Detection | Category | Tags |
|--------|-----------|----------|------|
| `retries > 0` | Task comment "ATTEMPT [exec]: N" where N > 0 | `bug-fix` | `type:lesson`, `signal:retry` |
| `stuck` tag | Task has `stuck` tag | `debugging` | `type:lesson`, `signal:stuck` |
| `validation-fix` | Subtask for validation issues | `bug-fix` | `type:lesson`, `signal:validation-fix` |
| `blocked` | Comment contains "BLOCKED:" | `debugging` | `type:lesson`, `signal:blocked` |
| User correction | User corrected output | `learning` | `type:lesson`, `signal:correction` |

### [CRITICAL] Storage Format

```python
mcp__vector-memory__store_memory({
    "content": """FAILURE: {concise_what_failed}
ROOT CAUSE: {why_it_happened}
FIX: {how_resolved}
PREVENTION: {pattern_to_prevent}
CONTEXT: Task #{id}, {area}""",
    "category": "bug-fix",  # or "debugging" for investigation
    "tags": ["type:lesson", "signal:{trigger}", "{domain}"]
})
```

### [CRITICAL] Category Discipline

| Intent | Category | Required |
|--------|----------|----------|
| Execution failure (retry, validation-fix) | `bug-fix` | `type:lesson`, `signal:{X}` |
| Investigation (stuck, blocked) | `debugging` | `type:lesson`, `signal:{X}` |
| Misunderstanding (correction) | `learning` | `type:lesson`, `signal:correction` |

**NEVER:**
- Store lessons in `architecture` category
- Store without `type:lesson` tag
- Store without ROOT CAUSE
- Store proposals here (use Suggestion Mode)

### Example Scenarios

**Retry Signal:**

```python
# Task MCP reports retry
# Trigger: retries > 0

mcp__vector-memory__store_memory({
    "content": """FAILURE: Race condition in OrderService
ROOT CAUSE: Concurrent requests modifying same order without lock
FIX: Added mutex lock around order update block
PREVENTION: Always use locks for concurrent mutations in payment flows
CONTEXT: Task #42, OrderService::update""",
    "category": "bug-fix",
    "tags": ["type:lesson", "signal:retry", "race-condition", "payment"]
})
```

**Stuck Signal:**

```python
# Task MCP reports stuck
# Trigger: stuck tag

mcp__vector-memory__store_memory({
    "content": """FAILURE: External API timeout unreachable
ROOT CAUSE: Third-party dependency without fallback mechanism
FIX: Escalated to human, switched to alternative provider
PREVENTION: Always implement circuit breaker for external APIs
CONTEXT: Task #43, PaymentProvider integration""",
    "category": "debugging",
    "tags": ["type:lesson", "signal:stuck", "external-api", "circuit-breaker"]
})
```

---

## Self Improvement Scenarios
<!-- description: Trigger-based suggestion mode for proposing instruction improvements. -->

Store improvement proposals. NOT continuous—only when triggered.

### [HIGH] Activation Triggers

| Trigger | Condition |
|---------|-----------|
| Mode flags | Task has `strict:paranoid` OR `cognitive:exhaustive` |
| User request | "How to improve?" / "Can we optimize?" |
| High cost | `time_spent` > 200% of `estimate` |
| Pattern repeat | Same failure 3+ times |

### [HIGH] Budget Constraints

| Constraint | Value |
|------------|-------|
| Max proposals/session | 3 |
| Max content length | 500 characters |
| Required format | Problem + Solution + Benefit |
| Initial status | `status:pending` |

### Proposal Format

```python
mcp__vector-memory__store_memory({
    "content": """PROPOSAL: {what_to_change}
PROBLEM: {current_issue}
SOLUTION: {proposed_fix}
BENEFIT: {expected_improvement}
CONTEXT: {instruction}:{section}, Task #{id}""",
    "category": "architecture",
    "tags": ["type:proposal", "status:pending", "source:self", "{area}"]
})
```

### Lifecycle

```
TRIGGER → LAWYER GATE (5/5) → STORE (status:pending)
                                      ↓
                              Human review
                                      ↓
                              ACCEPT → status:accepted + implement
                              REJECT → status:rejected + reason
```

### Example

```python
# Trigger: high cost (6h actual vs 2h estimate)

mcp__vector-memory__store_memory({
    "content": """PROPOSAL: Split auth module into smaller submodules
PROBLEM: Auth tasks consistently 3x over estimate due to module complexity
SOLUTION: Decompose into login, register, password modules
BENEFIT: 50% reduction in auth task time, clearer scope
CONTEXT: Task #45, auth module""",
    "category": "architecture",
    "tags": ["type:proposal", "status:pending", "source:self", "auth"]
})
```

---

## Lightweight Lawyer Gate Scenarios
<!-- description: Self-verification before proposals. 5-check gate. -->

Filter proposals before storage. Text-based self-verify.

### [HIGH] Checklist

| # | Check | Pass Condition |
|---|-------|----------------|
| 1 | Iron Rules | Does NOT violate any |
| 2 | Measurable | Has specific metric |
| 3 | Reversible | Has rollback plan |
| 4 | Scope | Does NOT expand task |
| 5 | Security | Does NOT weaken (or improves) |

### Decision

```python
if all_5_checks_pass:
    store_proposal(tags=["type:proposal", "status:pending", "gate:passed"])
elif security_fail or iron_rules_fail or scope_fail:
    reject_proposal()
else:
    clarify_and_retry()
```

### Pass Example

```python
# Proposal: Add parallel safety checklist
# Check 1 (Iron Rules): ✓ No violation
# Check 2 (Measurable): ✓ "80% reduction"
# Check 3 (Reversible): ✓ "Remove checklist"
# Check 4 (Scope): ✓ Same command
# Check 5 (Security): ✓ N/A
# Result: 5/5 PASS
```

### Fail Example

```python
# Proposal: Auto-complete parent tasks
# Check 1 (Iron Rules): ✗ "Parent-readonly: NEVER update parent"
# Result: REJECT

# Store rejection:
mcp__vector-memory__store_memory({
    "content": """PROPOSAL: Auto-complete parent tasks
GATE FAILED: iron_rules
REASON: Violates Parent-readonly Iron Rule
LESSON: Proposals must not violate Iron Rules""",
    "category": "learning",
    "tags": ["type:proposal", "status:rejected", "gate:failed"]
})
```

---

## Standard Search Patterns Reference
<!-- description: Canonical category + query patterns. Use category as primary filter. -->

### Lesson Search

```python
# What to AVOID
mcp__vector-memory__search_memories({
    "category": "bug-fix",
    "query": "type:lesson {domain}",
    "limit": 10
})
```

### Proposal Search

```python
# Pending improvements
mcp__vector-memory__search_memories({
    "category": "architecture",
    "query": "type:proposal status:pending",
    "limit": 10
})
```

### Pattern Search

```python
# What to APPLY
mcp__vector-memory__search_memories({
    "category": "code-solution",
    "query": "type:pattern {feature}",
    "limit": 5
})
```

### Quick Reference

| Find | Category | Query |
|------|----------|-------|
| Failure lessons | `bug-fix` | `type:lesson {domain}` |
| Debug patterns | `debugging` | `type:lesson signal:{X}` |
| Pending proposals | `architecture` | `type:proposal status:pending` |
| Accepted proposals | `architecture` | `type:proposal status:accepted` |
| Working patterns | `code-solution` | `type:pattern {feature}` |
| Decisions | `architecture` | `type:decision {topic}` |
| Insights | `learning` | `type:insight {topic}` |

---

## Tag Taxonomy Reference
<!-- description: Standard prefixes for consistent tagging. -->

### Prefixes

| Prefix | Values |
|--------|--------|
| `type:` | `lesson`, `proposal`, `pattern`, `decision`, `insight`, `convention` |
| `status:` | `pending`, `accepted`, `rejected`, `deprecated` |
| `signal:` | `retry`, `stuck`, `validation-fix`, `blocked`, `correction` |
| `source:` | `self`, `user`, `agent`, `external` |
| `gate:` | `passed`, `failed` |

### Combinations

```python
# Lesson (minimum)
["type:lesson", "signal:retry", "{domain}"]

# Proposal (required status)
["type:proposal", "status:pending", "source:self", "{area}"]

# Pattern (optional status)
["type:pattern", "{feature}", "{domain}"]

# Decision (no status)
["type:decision", "{topic}", "{area}"]
```

### Rules

```
type:lesson + status:* → INVALID
type:proposal + status:* → REQUIRED
type:pattern + status:* → OPTIONAL
type:decision + status:* → INVALID

Max: 10 tags (hard limit)
Min: 2 tags (type + domain)
Recommended: type + status/signal + 1-2 domain
```

---

## Non-Goals Reference
<!-- description: What we explicitly do NOT do to prevent overengineering. -->

| Non-Goal | Why Not Now |
|----------|-------------|
| AND tag filters | Category + query achieves 99% precision |
| Structural parser | Regex works; AST is overkill |
| Auto-checklist tools | Text self-verify is sufficient |
| Dynamic hooks | Constitutional docs are simpler |
| New categories | Add only when real noise problem |
| Auto-accept proposals | Human gate is safety requirement |
| Continuous suggestions | Trigger-based prevents waste |

**Revisit when:**
- Search noise > 20%
- Category discipline fails 5+/month
- Review queue > 50 items
