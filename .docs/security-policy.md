# Security Policy

---
name: "Security Policy"
description: "Security rules for Vector Memory MCP - secrets protection and data handling"
part: 1
type: "policy"
date: "2026-02-19"
version: "1.0.0"
---

## CRITICAL Rules

### No Secrets in Storage

**RULE:** NEVER store secrets, credentials, tokens, passwords, API keys, PII, or connection strings in vector memory.

**WHY:** Vector memory is persistent, searchable, and shared across agents and sessions. Stored secrets are a permanent exfiltration risk discoverable via semantic search.

**FORBIDDEN:**

| Type | Example |
|------|---------|
| .env values | `DB_HOST=192.168.1.5` |
| API keys | `API_KEY=sk-abc123...` |
| Connection strings | `mysql://user:pass@host/db` |
| Tokens | `Bearer eyJhbGciOiJIUzI1NiIs...` |
| Passwords | `password=Secret123` |
| Private URLs | `https://user:pass@api.internal/` |
| Certificates/Keys | Private key content |

**ACCEPTABLE:**

```
"Updated DB_HOST in .env for production"
"Rotated API_KEY for payment service"
"Configured JWT_SECRET (32 chars)"
```

### No Secret Exfiltration

**RULE:** NEVER output sensitive data to chat/response.

**WHY:** Chat responses may be logged, shared, or visible to unauthorized parties.

**GUIDELINES:**

- Extract key NAMES and STRUCTURE only from config
- Never raw values
- Mask values as "***" when displaying config

**BAD:**
```
get_by_memory_id({memory_id}) → shows "API_KEY=sk-abc123"
```

**GOOD:**
```
get_by_memory_id({memory_id}) → shows "API_KEY=***"
```

## High Priority Rules

### Content Validation

Before storing, validate content does not contain:

1. Patterns matching API keys (`sk-`, `Bearer `, etc.)
2. Patterns matching connection strings
3. Patterns matching passwords/credentials
4. Private URLs with credentials

### Error Output Handling

If error output contains secrets:

1. Redact before displaying
2. Strip before storing to memory
3. Never log raw error with secrets

## Enforcement

### Before mcp__vector-memory__store_memory()

```python
# Check content for secret patterns
if contains_secrets(content):
    return {
        "success": False,
        "error": "Security violation",
        "message": "Content contains forbidden secret patterns. Remove sensitive values."
    }
```

### Before any output

```python
# Sanitize for display
content = redact_secrets(content)
```

## Cross-MCP Consistency

This policy is identical in Vector Task MCP.

Both MCPs enforce:
- `no-secrets-in-storage` (Memory) / `no-secrets-in-comments` (Task)
- `no-secret-exfiltration` (both)

## Violation Response

On violation:

1. REJECT the operation
2. Log the attempt (without secrets)
3. Return clear error message
4. Suggest acceptable alternative

## Regular Audit

Monthly:

1. Search for potential secret patterns
2. Review high-risk memories
3. Delete any accidental secret storage
4. Update patterns as needed
