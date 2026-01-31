---
name: "task:status"
description: "Show task statistics with optional custom query ($ARGUMENTS)"
---

<command>
<meta>
<id>task:status</id>
<description>Show task statistics with optional custom query ($ARGUMENTS)</description>
</meta>
<execute>Displays task statistics and progress. Supports custom queries via $ARGUMENTS: time filters (yesterday, today, this week, this month), status filters (completed, pending, in_progress), grouping (by priority, by tags), and specific parent queries (parent_id=N). Empty $ARGUMENTS shows default overview.</execute>
<provides>Provides detailed task status information based on user input. Supports time-based filters, status filters, grouping, and specific parent queries.</provides>

# Iron Rules
## Always-summary (MEDIUM)
Always show summary at the end of output
- **why**: Provides quick overview regardless of query type
- **on_violation**: Append: "Summary: {total} total, {`completed`} `completed` ({pct}%)"


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($STATUS_QUERY = {query extracted from $RAW_INPUT})

# Parse arguments
Parse captured input to detect query type
- `detect`: STORE-AS($QUERY_TYPE = {detect: time|status|group|specific|default from $RAW_INPUT})
- Time filters: yesterday, today, this week, this month, last 7 days, last N days
- Status filters: `completed`, `pending`, `in_progress`, `stopped`
- Grouping: by priority, by tags, by parent
- Specific: parent_id=N, task_id=N
- Empty: default overview statistics

# Route query
Route to handler based on detected query type
- `route`: IF(STORE-GET($RAW_INPUT) is empty) →
  Execute: default-stats workflow
→ ELSE →
  Execute: custom-query workflow based on STORE-GET($QUERY_TYPE)
→ END-IF

# Default stats
Default overview when STORE-GET($RAW_INPUT) is empty
- `fetch`: mcp__vector-task__task_stats('{}')
- `store`: STORE-AS($STATS = statistics response)
- `format`: Display: Total tasks: {total} → Display: Pending: {`pending`} | In Progress: {`in_progress`} | Completed: {`completed`} → Calculate: completion_pct = (`completed` / total) * 100 → Render: [####------] {completion_pct}%

# Time filter
Handle time-based filters when STORE-GET($QUERY_TYPE) = time
- `parse`: yesterday → tasks from previous day → today → tasks from current day → this week → tasks from current week (Mon-Sun) → this month → tasks from current month → last N days → tasks from past N days
- `fetch-completed`: IF(STORE-GET($RAW_INPUT) contains "completed") →
  mcp__vector-task__task_list('{status: "completed", limit: 50}')
→ ELSE →
  mcp__vector-task__task_list('{limit: 50}')
→ END-IF
- `filter`: Filter results by detected timeframe using task timestamps
- `output`: List matching tasks with: title, status, created_at/completed_at → Show count: "Found {N} tasks {timeframe}"

# Status filter
Handle status-based filters when STORE-GET($QUERY_TYPE) = status
- `detect`: Extract status: `completed`|`pending`|`in_progress`|`stopped`
- `fetch`: mcp__vector-task__task_list('{status: "{detected_status}", limit: 30}')
- `store`: STORE-AS($TASKS = filtered task list)
- `output`: Display: "{status}" tasks: {count} → List each: #{id} {title} (priority: {priority}, created: {date})

# Grouping
Handle grouping when STORE-GET($QUERY_TYPE) = group
- `by-priority`: IF(STORE-GET($RAW_INPUT) = "by priority") →
  mcp__vector-task__task_list('{limit: 100}') → Group by priority: critical, high, medium, low → Display: Priority | Count | % of Total → Display: Critical: {n} ({pct}%) → Display: High: {n} ({pct}%) → Display: Medium: {n} ({pct}%) → Display: Low: {n} ({pct}%)
→ END-IF
- `by-tags`: IF(STORE-GET($RAW_INPUT) = "by tags") →
  mcp__vector-task__task_list('{limit: 100}') → Extract and count unique tags → Display: Tag | Count → Sort by count descending
→ END-IF
- `by-parent`: IF(STORE-GET($RAW_INPUT) = "by parent") →
  mcp__vector-task__task_list('{limit: 100}') → Group by parent_id (null = root tasks) → Display: Root tasks: {n} → Display: Child tasks by parent with counts
→ END-IF

# Specific parent
Handle parent_id=N queries when STORE-GET($QUERY_TYPE) = specific
- `parse`: Extract N from "parent_id=N" in STORE-GET($RAW_INPUT)
- `fetch-parent`: mcp__vector-task__task_get('{task_id: N}')
- `fetch-children`: mcp__vector-task__task_list('{parent_id: N, limit: 50}')
- `output`: Display parent: #{id} {title} [{status}] → Display children count: {n} subtasks → List children: #{id} {title} [{status}] (priority: {priority}) → Show completion: {`completed`}/{total} subtasks done

# Output format
Standard output formatting
- --- Task Statistics ---
- --- Tasks: {query_description} ---
- Total: 25 | Pending: 15 | In Progress: 2 | Completed: 8
- [########----------] 32%
- #{id} {title} [{status}] - {date}
- Found 5 tasks `completed` yesterday
- Priority breakdown: Critical(2) High(5) Medium(12) Low(6)

</command>