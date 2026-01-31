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
<guidelines>

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

</guidelines>
</command>