# Cookbook API Reference

---
name: "Cookbook API Reference"
description: "Complete cookbook() tool API documentation for Vector Memory MCP"
part: 1
type: "api"
date: "2026-02-19"
version: "2.0.0"
---

## Parameters

```python
mcp__vector-memory__cookbook(
    include: str = "init",       # What to return
    level: int = 0,              # Docs verbosity (0-3)
    case_category: str = None,   # Filter by category key(s), CSV for OR
    query: str = None,           # Text search
    priority: str = None,        # Filter by priority (critical, high, or both)
    cognitive: str = None,       # Filter by cognitive level tags
    strict: str = None,          # Filter by strict level tags
    limit: int = 10,             # Max results (1-50)
    offset: int = 0              # Pagination offset
)
```

## Parameter Details

### include

| Value | Description |
|-------|-------------|
| `init` | FIRST READ - quick start + available resources |
| `docs` | Documentation by level |
| `cases` | Use case scenarios (filtered) |
| `categories` | List categories with keys |
| `all` | Everything combined (large!) |

### level

| Value | Content |
|-------|---------|
| 0 | Identity & Quick Start |
| 1 | Practical Usage |
| 2 | Advanced Patterns |
| 3 | Architecture & Internals |

### case_category

Single key or comma-separated list (OR logic):

```python
# Single
case_category="gates-rules"

# Multiple (OR)
case_category="store,search,gates-rules"
```

### query

Text search across all content:

```python
query="JWT token"
query="multi-probe search"
query="CRITICAL"
```

### priority

Filter by priority markers in content:

| Value | Description |
|-------|-------------|
| `critical` | Only [CRITICAL] content |
| `high` | Only [HIGH] content |
| `critical,high` | Both CRITICAL and HIGH |

### cognitive

Filter by `cognitive:*` tags in content (OR logic):

| Value | Description |
|-------|-------------|
| `minimal` | cognitive:minimal patterns |
| `standard` | cognitive:standard patterns |
| `deep` | cognitive:deep patterns |
| `exhaustive` | cognitive:exhaustive patterns |
| `deep,exhaustive` | Both deep and exhaustive |

### strict

Filter by `strict:*` tags in content (OR logic):

| Value | Description |
|-------|-------------|
| `relaxed` | strict:relaxed patterns |
| `standard` | strict:standard patterns |
| `strict` | strict:strict patterns |
| `paranoid` | strict:paranoid patterns |
| `strict,paranoid` | Both strict and paranoid |

### limit / offset

Pagination for large results:

```python
limit=10, offset=0   # First page
limit=10, offset=10  # Second page
```

## Examples

### Initialization

```python
mcp__vector-memory__cookbook()
mcp__vector-memory__cookbook(include="init")
```

### Categories

```python
mcp__vector-memory__cookbook(include="categories")
```

### Single Category

```python
mcp__vector-memory__cookbook(include="cases", case_category="gates-rules")
```

### Multiple Categories (OR)

```python
mcp__vector-memory__cookbook(include="cases", case_category="store,search")
```

### Priority Filter

```python
# Only CRITICAL
mcp__vector-memory__cookbook(include="cases", priority="critical")

# CRITICAL or HIGH
mcp__vector-memory__cookbook(include="cases", priority="critical,high")
```

### Cognitive Filter

```python
# Only deep
mcp__vector-memory__cookbook(include="cases", cognitive="deep")

# Deep or exhaustive
mcp__vector-memory__cookbook(include="cases", cognitive="deep,exhaustive")
```

### Strict Filter

```python
# Only strict
mcp__vector-memory__cookbook(include="cases", strict="strict")

# Strict or paranoid
mcp__vector-memory__cookbook(include="cases", strict="strict,paranoid")
```

### Search

```python
mcp__vector-memory__cookbook(include="cases", query="JWT")
mcp__vector-memory__cookbook(include="docs", query="tag normalization", level=2)
```

### Combined Filters

```python
mcp__vector-memory__cookbook(
    include="cases",
    case_category="gates-rules",
    query="secret",
    priority="critical",
    limit=5
)

# Brain with cognitive:deep, strict:standard
mcp__vector-memory__cookbook(
    include="cases",
    cognitive="deep,exhaustive",
    strict="standard,strict,paranoid",
    limit=20
)
```

### Pagination

```python
mcp__vector-memory__cookbook(include="cases", priority="critical", limit=5, offset=0)
mcp__vector-memory__cookbook(include="cases", priority="critical", limit=5, offset=5)
```

## Consistency with Task MCP

Both MCPs use IDENTICAL cookbook() parameters:

| Parameter | Memory MCP | Task MCP |
|-----------|------------|----------|
| include | ✓ same | ✓ same |
| level | ✓ same | ✓ same |
| case_category | ✓ same | ✓ same |
| query | ✓ same | ✓ same |
| priority | ✓ same | ✓ same |
| cognitive | ✓ same | ✓ same |
| strict | ✓ same | ✓ same |
| limit | ✓ same | ✓ same |
| offset | ✓ same | ✓ same |

This ensures agents can use cookbook() consistently across both MCPs.

## Response Structure

### init

```json
{
  "success": true,
  "critical": "READ THIS FIRST",
  "warning": "Without this cookbook...",
  "quick_start": {...},
  "available_resources": {
    "cookbook_docs": {...},
    "cookbook_cases": {...},
    "cookbook_priority": {...},
    "cookbook_cognitive": {...},
    "cookbook_strict": {...},
    "cookbook_search": {...},
    "cookbook_categories": {...}
  },
  "message": "Retrieved init level 0"
}
```

### cases

```json
{
  "success": true,
  "include": "cases",
  "level": 0,
  "case_category": "gates-rules",
  "query": null,
  "priority": "critical",
  "cognitive": null,
  "strict": null,
  "limit": 10,
  "offset": 0,
  "cases": {
    "categories": ["Gates & Rules Scenarios"],
    "count": 1,
    "content": "..."
  },
  "message": "Retrieved cases level 0 priority='critical'"
}
```
