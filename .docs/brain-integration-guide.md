# Brain Integration Guide

---
name: "Brain Integration Guide"
description: "Metacognitive constitution for Brain-Memory-Task orchestration"
part: 1
type: "guide"
date: "2026-02-19"
version: "1.0.0"
---

## Purpose

This document defines the **metacognitive contract** between:
- Brain (orchestrator)
- Memory MCP (knowledge storage)
- Task MCP (execution tracking)

It establishes **constitutional rules** that ensure consistency, prevent drift, and enable self-improvement without overengineering.

---

## Section A: Constitutional Learn Protocol [CRITICAL]

### Purpose

Memory is PRIMARY storage for failure patterns. This protocol defines WHAT to store, WHEN to store, and HOW to store lessons.

### Trigger Signals

Store lesson to memory when Task MCP reports ANY:

| Signal | Source | Memory Content Focus |
|--------|--------|---------------------|
| `retries > 0` | Task comment | Execution failure pattern |
| `stuck` tag | Task tags | Unresolvable blocker pattern |
| `validation-fix` tag | Subtask | Quality gate failure pattern |
| `blocked` in comment | Task comment | Dependency failure pattern |
| User correction | Session | Misunderstanding pattern |

### Non-Triggers

Do NOT store lesson when:

- Task completed cleanly (pollutes memory with noise)
- Task was `stopped` by user (cancelled ≠ failed)
- `strict:relaxed` + `cognitive:minimal` AND no signals

### Required Content Format

```python
mcp__vector-memory__store_memory({
    "content": """FAILURE: {concise_what_failed}
ROOT CAUSE: {why_it_happened}
FIX: {how_it_was_resolved}
PREVENTION: {pattern_to_prevent_recurrence}
CONTEXT: Task #{id}, {file_or_area}""",
    "category": "bug-fix",  # or "debugging" for investigation
    "tags": ["type:lesson", "signal:{trigger}", "{domain}"]
})
```

### Content Quality Rules

| Element | Required | Bad Example | Good Example |
|---------|----------|-------------|--------------|
| FAILURE | Yes | "Error happened" | "N+1 query in UserController@store" |
| ROOT CAUSE | Yes | "It broke" | "Missing ->with('roles') eager load" |
| FIX | Yes | "Fixed it" | "Add ->with('roles') before ->get()" |
| PREVENTION | Yes | "Be careful" | "Always eager load in store methods" |
| CONTEXT | Recommended | None | "Task #42, auth module" |

### Forbidden Storage

```
NEVER store:
- Raw error logs (extract pattern, not logs)
- Secrets/credentials (security violation)
- Proposals (use Suggestion Mode, different category)
- Temporary workarounds without "WHY temporary"
- Duplicate content (search before store)
```

---

## Section B: Category Discipline Contract

### Principle

Categories provide first-level filtering. Discipline ensures clean retrieval even with OR-only tag logic.

### Category Assignment Matrix

| Intent | Category | Required Tags | Cross-Reference |
|--------|----------|---------------|-----------------|
| **Lesson (failure pattern)** | `bug-fix` | `type:lesson`, `signal:{X}` | Task with failure |
| **Lesson (investigation)** | `debugging` | `type:lesson`, `signal:{X}` | Debugging session |
| **Proposal (improvement)** | `architecture` | `type:proposal`, `status:pending` | Trigger task |
| **Decision (choice made)** | `architecture` | `type:decision` | Related task |
| **Pattern (working solution)** | `code-solution` | `type:pattern` | Implementation task |
| **Insight (discovery)** | `learning` | `type:insight` | Research task |
| **Convention (project rule)** | `project-context` | `type:convention` | Setup task |

### Critical Boundaries

```
SEPARATION IS MANDATORY:

bug-fix category:
  → type:lesson ONLY (past failures)
  → NEVER type:proposal

architecture category:
  → type:proposal, type:decision ONLY
  → NEVER type:lesson

code-solution category:
  → type:pattern ONLY (working solutions)
  → NEVER type:proposal, NEVER type:lesson
```

### Why Separation Matters

Memory MCP uses **OR logic** for tags. Without category discipline:
- `search_memories(tags=["type:proposal"])` → returns lessons in bug-fix too
- `search_memories(category="bug-fix")` → clean lessons only

**Category is the primary filter. Tags are secondary.**

---

## Section C: Standard Search Patterns

### Canonical Search Format

```python
# Find lessons (what to AVOID)
mcp__vector-memory__search_memories({
    "category": "bug-fix",
    "query": "type:lesson {domain}",
    "limit": 10
})

# Find pending improvement proposals
mcp__vector-memory__search_memories({
    "category": "architecture",
    "query": "type:proposal status:pending",
    "limit": 10
})

# Find accepted patterns for reuse
mcp__vector-memory__search_memories({
    "category": "code-solution",
    "query": "type:pattern {feature}",
    "limit": 5
})

# Find architecture decisions
mcp__vector-memory__search_memories({
    "category": "architecture",
    "query": "type:decision {topic}",
    "limit": 5
})

# Find debugging patterns (investigation lessons)
mcp__vector-memory__search_memories({
    "category": "debugging",
    "query": "type:lesson signal:{signal}",
    "limit": 10
})
```

### Multi-Probe Pattern (MANDATORY for Complex Queries)

```python
# Complex topic: auth implementation
# Probe 1: Find patterns
search_memories(category="code-solution", query="type:pattern auth")

# Probe 2: Find lessons (what to avoid)
search_memories(category="bug-fix", query="type:lesson auth")

# Probe 3: Find decisions (constraints)
search_memories(category="architecture", query="type:decision auth")
```

### Search Quick Reference

| Find | Category | Query |
|------|----------|-------|
| Failure patterns | `bug-fix` | `type:lesson {domain}` |
| Debug patterns | `debugging` | `type:lesson signal:{X}` |
| Pending proposals | `architecture` | `type:proposal status:pending` |
| Accepted proposals | `architecture` | `type:proposal status:accepted` |
| Working patterns | `code-solution` | `type:pattern {feature}` |
| Decisions | `architecture` | `type:decision {topic}` |
| Insights | `learning` | `type:insight {topic}` |

---

## Section D: Trigger-based Suggestion Mode [HIGH]

### Purpose

Allow agents to propose instruction improvements. NOT continuous—only when triggered.

### Activation Triggers

| Trigger | Condition |
|---------|-----------|
| Mode flags | Task has `strict:paranoid` OR `cognitive:exhaustive` |
| User request | "How to improve?" / "Can we optimize?" |
| High cost | `time_spent` >> `estimate` (>200%) |
| Pattern repetition | Same failure 3+ times |

### Budget Constraints

| Constraint | Value |
|------------|-------|
| Max proposals/session | 3 |
| Max content length | 500 chars |
| Format | Problem + Solution + Benefit |
| Initial status | `status:pending` (NEVER auto-accept) |

### Proposal Format

```python
mcp__vector-memory__store_memory({
    "content": """PROPOSAL: {what_to_change}
PROBLEM: {current_issue}
SOLUTION: {proposed_fix}
BENEFIT: {expected_improvement}
CONTEXT: {instruction_file}:{section}, Task #{id}""",
    "category": "architecture",
    "tags": ["type:proposal", "status:pending", "source:self", "{domain}"]
})
```

### Proposal Lifecycle

```
CREATE (status:pending)
    ↓
REVIEW (human or Brain)
    ↓
    ├─→ ACCEPT: Update instruction, change to status:accepted
    │
    └─→ REJECT: Add reason, change to status:rejected
```

### Storage Rules

- ALWAYS in `architecture` category
- ALWAYS start with `status:pending`
- NEVER store without passing Lawyer Gate
- NEVER auto-change to `status:accepted`

---

## Section E: Lightweight Lawyer Gate [HIGH]

### Purpose

Filter proposals before storage to prevent harmful suggestions.

### Self-Verification Checklist

Before `store_memory` for ANY proposal:

| Check | Pass Condition |
|-------|----------------|
| **Iron Rules** | Does NOT violate any Iron Rule |
| **Measurable** | Has specific, measurable benefit |
| **Reversible** | Can be rolled back if needed |
| **Scope** | Does NOT expand beyond original task |
| **Security** | Does NOT weaken security (or improves it) |

### Decision Logic

```python
checks_passed = 0
if not violates_iron_rules: checks_passed += 1
if has_measurable_benefit: checks_passed += 1
if is_reversible: checks_passed += 1
if same_scope: checks_passed += 1
if security_ok: checks_passed += 1

if checks_passed == 5:
    store_proposal(tags=["gate:passed"])
elif security_fail:
    reject_or_escalate()
else:
    reject_with_explanation()
```

### Rejected Proposal Storage

```python
# Store rejection for learning
mcp__vector-memory__store_memory({
    "content": """PROPOSAL: {what}
GATE FAILED: {check_name}
REASON: {why_failed}
LESSON: {what_we_learned_about_proposal_quality}""",
    "category": "learning",
    "tags": ["type:proposal", "status:rejected", "gate:failed"]
})
```

---

## Section F: Non-Goals [CRITICAL]

### Explicitly Out of Scope

| Non-Goal | Rationale |
|----------|-----------|
| **AND tag filters** | Category + query achieves 99% precision |
| **AST case parser** | Regex works; complexity not justified |
| **Auto-checklist tools** | Text self-verify is sufficient |
| **Dynamic hooks** | Constitutional docs are simpler |
| **New categories** | Add only when real noise problem |
| **Auto-accept proposals** | Human gate is safety requirement |
| **Continuous suggestions** | Trigger-based prevents waste |
| **Proposal voting** | Human authority is final |

### Revisit Thresholds

Reconsider non-goals ONLY when:

| Non-Goal | Trigger to Reconsider |
|----------|----------------------|
| AND filters | Search noise > 20% in production |
| New categories | Discipline fails 5+ times/month |
| Auto-accept | Review queue > 50 items |
| AST parser | Case maintenance > 4 hours/week |

---

## Section G: Tag Taxonomy

### Prefix Standards

| Prefix | Purpose | Values |
|--------|---------|--------|
| `type:` | Content kind | `lesson`, `proposal`, `pattern`, `decision`, `insight`, `convention` |
| `status:` | Lifecycle | `pending`, `accepted`, `rejected`, `deprecated` |
| `signal:` | Trigger type | `retry`, `stuck`, `validation-fix`, `blocked` |
| `source:` | Origin | `self`, `user`, `agent`, `external` |
| `gate:` | Verification | `passed`, `failed` |

### Tag Rules

```python
# Minimum tags: type + domain
["type:lesson", "auth"]

# Recommended: type + status/signal + domain
["type:lesson", "signal:retry", "auth"]

# Maximum: 10 tags (hard limit)
["type:proposal", "status:pending", "source:self", "gate:passed", 
 "parallel", "execution", "safety", "performance", "workflow", "mcp"]
```

### Tag Interactions

```
type:lesson + status:* → Invalid (lessons don't have status)
type:proposal + status:* → Required (proposals need status)
type:pattern + status:* → Optional (patterns can be deprecated)
type:decision + status:* → Invalid (decisions are final)
```

---

## Section H: Memory-Task Coordination

### Pre-Task Flow

```
1. Task MCP: task_get({task_id}) → understand scope
2. Memory MCP: search_memories({category: "bug-fix", query: "type:lesson {topic}"})
3. Memory MCP: search_memories({category: "code-solution", query: "type:pattern {topic}"})
4. Extract: AVOID (lessons) + APPLY (patterns)
5. Task MCP: task_update({comment: "Memory context: #{ids}"})
```

### Post-Task Flow

```
1. Task MCP: task completed with signals (retries, stuck, etc.)
2. Memory MCP: search_memories({query: "{failure_summary}"}) → check duplicate
3. IF unique: Memory MCP: store_memory({lesson format})
4. Task MCP: task_update({comment: "Lesson stored in memory #ID"})
```

### Cross-Reference Format

```
Memory → Task: "Discovered during task #42"
Task → Memory: "Lesson stored in memory #15"
```

---

## Appendix: Quick Reference Card

### Store Lesson

```
IF task_has_failure_signal:
    search_memories(query="{failure}") → check duplicate
    IF unique:
        store_memory(
            category = "bug-fix"
            content = "FAILURE: ... ROOT CAUSE: ... FIX: ... PREVENTION: ..."
            tags = ["type:lesson", "signal:{signal}", "{domain}"]
        )
```

### Store Proposal

```
IF suggestion_mode_active AND gate_checks_pass:
    store_memory(
        category = "architecture"
        content = "PROPOSAL: ... PROBLEM: ... SOLUTION: ... BENEFIT: ..."
        tags = ["type:proposal", "status:pending", "source:self"]
    )
```

### Search by Intent

```
Avoid failures:  category="bug-fix", query="type:lesson {domain}"
Find patterns:   category="code-solution", query="type:pattern {feature}"
Review proposals: category="architecture", query="type:proposal status:pending"
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-02-19 | Initial constitutional framework |
