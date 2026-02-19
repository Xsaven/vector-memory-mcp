---
name: "task:list"
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
Task tags MUST use ONLY predefined values. FORBIDDEN: inventing new tags, synonyms, variations. Allowed: decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression, feature, bugfix, refactor, research, docs, test, chore, spike, hotfix, backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration, strict:relaxed, strict:standard, strict:strict, strict:paranoid, cognitive:minimal, cognitive:standard, cognitive:deep, cognitive:exhaustive, batch:trivial.
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

## Mandatory-level-tags (CRITICAL)
EVERY task MUST have exactly ONE strict:* tag AND ONE cognitive:* tag. Allowed strict: strict:relaxed, strict:standard, strict:strict, strict:paranoid. Allowed cognitive: cognitive:minimal, cognitive:standard, cognitive:deep, cognitive:exhaustive. Missing level tags = assign based on task scope analysis.
- **why**: Level tags enable per-task compilation and cognitive load calibration. Without them, system defaults apply blindly regardless of task complexity.
- **on_violation**: Analyze task scope and assign: strict:{level} + cognitive:{level}. Simple rename = strict:relaxed + cognitive:minimal. Production auth = strict:strict + cognitive:deep.

## Safety-escalation-non-overridable (CRITICAL)
After loading task, check file paths in task.content/comment. If files match safety patterns → effective level MUST be >= pattern minimum, regardless of task tags or .env default. Agent tags are suggestions UPWARD only — can raise above safety floor, never lower below it.
- **why**: Safety patterns guarantee minimum protection for critical code areas. Agent cannot "cheat" by under-tagging a task touching auth/ or payments/.
- **on_violation**: Raise effective level to safety floor. Log escalation in task comment.


# Task tag selection
GOAL(Select tags per task. Combine dimensions for precision.)
WORKFLOW (pipeline stage): decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression
TYPE (work kind): feature, bugfix, refactor, research, docs, test, chore, spike, hotfix
DOMAIN (area): backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration
STRICT LEVEL: strict:relaxed, strict:standard, strict:strict, strict:paranoid
COGNITIVE LEVEL: cognitive:minimal, cognitive:standard, cognitive:deep, cognitive:exhaustive
BATCH: batch:trivial
Formula: 1 TYPE + 1 DOMAIN + 0-2 WORKFLOW + 1 STRICT + 1 COGNITIVE. Example: ["feature", "api", "strict:standard", "cognitive:standard"] or ["bugfix", "auth", "validation-fix", "strict:strict", "cognitive:deep"].

# Memory tag selection
GOAL(Select 1-3 tags per memory. Combine dimensions.)
CONTENT (kind): pattern, solution, `failure`, decision, insight, workaround, deprecated
SCOPE (breadth): project-wide, module-specific, temporary, reusable
Formula: 1 CONTENT + 0-1 SCOPE. Example: ["solution", "reusable"] or ["failure", "module-specific"]. Max 3 tags.

# Safety escalation patterns
GOAL(Automatic level escalation based on file patterns and context)
File patterns → strict minimum: auth/, guards/, policies/, permissions/ → strict. payments/, billing/, stripe/, subscription/ → strict. .env, credentials, secrets, config/auth → paranoid. migrations/, schema → strict. composer.json, package.json, *.lock → standard. CI/, .github/, Dockerfile, docker-compose → strict. routes/, middleware/ → standard.
Context patterns → level minimum: priority=critical → strict+deep. tag hotfix or production → strict+standard. touches >10 files → standard+standard. tag breaking-change → strict+deep. Keywords security/encryption/auth/permission → strict. Keywords migration/schema/database/drop → strict.

# Cognitive level
GOAL(Cognitive level: standard — calibrate analysis depth accordingly)
Memory probes per phase: 2-3 targeted
Failure history: recent only
Research (context7/web): on error/ambiguity
Agent scaling: auto (2-3)
Comment parsing: basic parse

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