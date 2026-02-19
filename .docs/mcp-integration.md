# MCP Integration Guide

---
name: "MCP Integration Guide"
description: "Vector Memory MCP integration with Vector Task MCP and Brain ecosystem"
part: 1
type: "guide"
date: "2026-02-19"
version: "1.0.0"
---

## Overview

Vector Memory MCP integrates with Vector Task MCP to provide seamless knowledge management for AI agents in the Brain ecosystem.

## Integration Points

### Cross-Reference Format

Both MCPs use `#N` format for bidirectional linking:

```
Memory → Task: "Discovered during task #42"
Task → Memory: "Insights stored in memory #15"
```

### Shared Rules

| Memory MCP Rule | Task MCP Rule | Coordination |
|-----------------|---------------|--------------|
| `no-secrets-in-storage` | `no-secrets-in-comments` | NEVER store secrets |
| `no-secret-exfiltration` | `no-secret-exfiltration` | NEVER output secrets |
| `search-before-store` | `memory-primary-comments-critical` | Memory = PRIMARY |
| `mcp-only-access` | `mcp-only-access` | Same rule |

### Workflow Integration

**Pre-Task (Task MCP triggers Memory MCP):**
```python
mcp__vector-task__task_get({task_id}) → understand scope
mcp__vector-memory__search_memories({query: "{task_topic}"}) → solutions
mcp__vector-memory__search_memories({query: "{task_topic} failure", category: "debugging"}) → avoid
```

**Post-Task (Memory MCP stores, Task MCP links):**
```python
mcp__vector-memory__search_memories({query: "{insight}"}) → check duplicates
mcp__vector-memory__store_memory({content, category, tags})
mcp__vector-task__task_update({task_id, comment: "See memory #ID"})
```

## Cookbook API Consistency

Both MCPs use identical cookbook() parameters:

```python
cookbook(
    include: str,       # init, docs, cases, categories, all
    level: int,         # 0-3 (docs verbosity)
    case_category: str, # single or comma-separated (OR logic)
    query: str,         # text search
    priority: str,      # critical, high, or critical,high (OR logic)
    cognitive: str,     # minimal, standard, deep, exhaustive (OR logic)
    strict: str,        # relaxed, standard, strict, paranoid (OR logic)
    limit: int,         # pagination
    offset: int         # pagination
)
```

### Filter Combinations

All filters can be combined for precise content retrieval:

```python
# Brain with cognitive:deep, strict:standard wants relevant cases
cookbook(
    include="cases",
    cognitive="deep,exhaustive",
    strict="standard,strict,paranoid",
    limit=30
)

# Get critical rules for memory storage
cookbook(
    include="cases",
    case_category="store,gates-rules",
    priority="critical"
)
```

## Category Keys

### Memory MCP Keys

| Key | Description |
|-----|-------------|
| `cookbook-usage` | How to use cookbook() |
| `store` | Store operations |
| `search` | Search operations |
| `statistics` | Memory stats |
| `task-management` | Task integration |
| `brain-docs` | CLI docs |
| `agent-coordination` | Multi-agent |
| `integration` | Error recovery |
| `debugging` | Debug patterns |
| `cleanup` | Garbage collection |
| `gates-rules` | CRITICAL/HIGH rules |
| `task-integration` | Memory-Task workflow |

### Task MCP Keys (for reference)

| Key | Description |
|-----|-------------|
| `task-decision` | When to create task |
| `task-creation` | Create operations |
| `task-execution` | Execution patterns |
| `search-query` | Search operations |
| `tag-normalization` | Tag management |
| `hierarchy-decomposition` | Task breakdown |
| `status-time` | Status management |
| `error-handling` | Error recovery |
| `parallel-execution` | Concurrent execution |
| `validation` | Quality gates |
| `memory-integration` | Memory workflow |
| `gates-rules` | CRITICAL/HIGH rules |
| `cookbook-usage` | Cookbook usage |

### Shared Keys (consistent across MCPs)

- `gates-rules` - Gates & Rules Scenarios (both have this)
- `cookbook-usage` - Cookbook documentation (both have this)

## Tool Naming Convention

**CRITICAL:** Always use FULL MCP tool names with prefix. NEVER abbreviate.

### Memory MCP Tools

```python
mcp__vector-memory__cookbook(...)
mcp__vector-memory__store_memory(...)
mcp__vector-memory__search_memories(...)
mcp__vector-memory__get_by_memory_id(...)
mcp__vector-memory__delete_by_memory_id(...)
mcp__vector-memory__list_recent_memories(...)
mcp__vector-memory__get_memory_stats(...)
mcp__vector-memory__clear_old_memories(...)
mcp__vector-memory__get_unique_tags(...)
mcp__vector-memory__get_canonical_tags(...)
mcp__vector-memory__get_tag_frequencies(...)
mcp__vector-memory__get_tag_weights(...)
```

### Task MCP Tools

```python
mcp__vector-task__cookbook(...)
mcp__vector-task__task_get(...)
mcp__vector-task__task_create(...)
mcp__vector-task__task_create_bulk(...)
mcp__vector-task__task_update(...)
mcp__vector-task__task_list(...)
mcp__vector-task__task_next(...)
mcp__vector-task__task_stats(...)
mcp__vector-task__task_delete(...)
mcp__vector-task__task_delete_bulk(...)
```

### Why Full Prefixes?

1. **No confusion** - Agent knows exactly which MCP to call
2. **No cross-contamination** - Tags/params go to correct MCP
3. **Clear documentation** - Copy-paste works directly
4. **Cross-MCP safety** - `mcp__vector-memory__*` never confused with `mcp__vector-task__*`

## Standards for Both MCPs

### Security

- NEVER store secrets in memory or task comments
- NEVER output secrets to chat/response
- Use key names only, mask values as "***"

### Quality

- Memory = PRIMARY storage for reusable knowledge
- Comments = CRITICAL links only (memory IDs, file paths)
- Content must include: WHAT + WHY + WHEN-TO-USE + GOTCHAS

### Documentation

- Category descriptions in HTML comments: `<!-- description: ... -->`
- Priority markers: `[CRITICAL]` and `[HIGH]` in subsection titles
- Keys: kebab-case (e.g., `gates-rules`, `task-integration`)

## Future Changes

When updating integration standards:

1. Update this file in BOTH MCP `.docs/` directories
2. Ensure cookbook() parameters remain consistent
3. Update Cross-Reference tables in both CASES files
4. Test integration workflows after changes
