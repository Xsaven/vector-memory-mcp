---
description: "List tasks with optional filters (status, parent, tags, priority)"
---

<command>
<meta>
<id>task:list</id>
<description>List tasks with optional filters (status, parent, tags, priority)</description>
</meta>
<execute>Lists tasks from vector-task storage with optional filters. Parses $ARGUMENTS for filters (status, parent_id, tags, priority), queries vector-task MCP, and displays formatted hierarchical task list with status/priority indicators.</execute>
<provides>Task listing utility that queries vector storage and displays formatted task hierarchy with status and priority indicators. Supports filters: status, parent_id, tags, priority, limit.</provides>

# Iron Rules
## Task-tags-predefined-only (CRITICAL)
Task tags MUST use ONLY predefined values. FORBIDDEN: inventing new tags, synonyms, variations. Allowed: decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression, feature, bugfix, refactor, research, docs, test, chore, spike, hotfix, backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration.
- **why**: Ad-hoc tags cause explosion ("user-auth", "authentication", "auth" = same thing, search finds none). Predefined list = consistent search.
- **on_violation**: Replace with closest predefined match. No match = skip tag, put context in content.

## Memory-tags-predefined-only (CRITICAL)
Memory tags MUST use ONLY predefined values. Allowed: pattern, solution, `failure`, decision, insight, workaround, deprecated, project-wide, module-specific, temporary, reusable.
- **why**: Unknown tags = unsearchable memories. Predefined = discoverable.
- **on_violation**: Replace with closest predefined match.

## Memory-categories-predefined-only (CRITICAL)
Memory category MUST be one of: code-solution, bug-fix, architecture, learning, debugging, performance, security, project-context. FORBIDDEN: "other", "general", "misc", or unlisted.
- **why**: "other" is garbage nobody searches. Every memory needs meaningful category.
- **on_violation**: Choose most relevant from predefined list.


# Task tag selection
GOAL(Select 1-4 tags per task. Combine dimensions for precision.)
WORKFLOW (pipeline stage): decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression
TYPE (work kind): feature, bugfix, refactor, research, docs, test, chore, spike, hotfix
DOMAIN (area): backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration
Formula: 1 TYPE + 1 DOMAIN + 0-2 WORKFLOW. Example: ["feature", "api"] or ["bugfix", "auth", "validation-fix"]. Max 4 tags.

# Memory tag selection
GOAL(Select 1-3 tags per memory. Combine dimensions.)
CONTENT (kind): pattern, solution, `failure`, decision, insight, workaround, deprecated
SCOPE (breadth): project-wide, module-specific, temporary, reusable
Formula: 1 CONTENT + 0-1 SCOPE. Example: ["solution", "reusable"] or ["failure", "module-specific"]. Max 3 tags.

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($LIST_FILTERS = {filters extracted from $RAW_INPUT: status, parent_id, tags, query})

# Role
Task listing utility that queries vector storage and displays formatted task hierarchy with status and priority indicators.

# Workflow step1
STEP 1 - Parse Input for Filters
- `parse`: Extract filters from $RAW_INPUT: status=`pending`, parent_id=5, tags=backend, priority=high, limit=20
- `defaults`: Default: no filters (list all), limit=50
- `output`: STORE-AS($FILTERS = {status?, parent_id?, tags?, priority?, limit?, offset?})

# Workflow step2
STEP 2 - Query Vector Task Storage
- `query`: mcp__vector-task__task_list('STORE-GET($FILTERS)')
- `output`: STORE-AS($TASKS = task list from vector storage)

# Workflow step3
STEP 3 - Format and Display Task List
- `organize`: Group tasks: root tasks (parent_id=null) first, then children indented under parents
- `format`: FOREACH(task in STORE-GET($TASKS)) →
  Display: {status_icon} {priority_icon} #{id} {title} [{tags}] (est: {estimate})
→ END-FOREACH
- `summary`: Show: total count, by status breakdown, by priority breakdown

# Status icons
Status indicator mapping
- [`pending`]
- [`in_progress`]
- [`completed`]
- [`stopped`]

# Priority icons
Priority indicator mapping
- [critical]
- [high]
- [medium]
- [low]

# Output format
Task display format
- `root`: {status} {priority} #{id} {title} [{tags}]
- `child`:   └─ {status} {priority} #{id} {title} [{tags}]
- `nested`:     └─ {status} {priority} #{id} {title} [{tags}]

# Filter examples
Supported filter combinations
- /task:list → all tasks
- /task:list status=`pending` → `pending` tasks only
- /task:list parent_id=5 → children of task #5
- /task:list tags=backend,api → tasks with specific tags
- /task:list priority=high → high priority tasks
- /task:list status=`pending` priority=critical → combined filters

</command>