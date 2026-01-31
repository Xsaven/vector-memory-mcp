---
name: "task:decompose"
description: "Decompose large task into subtasks (each <=5-8h)"
---

<command>
<meta>
<id>task:decompose</id>
<description>Decompose large task into subtasks (each <=5-8h)</description>
</meta>
<execute>Decomposes large tasks (>5-8h estimate) into smaller, manageable subtasks. Each subtask MUST have estimate <=5-8 hours (GOLDEN RULE). Recursively flags subtasks exceeding 8h for further decomposition. Input: $ARGUMENTS = task_id. Requires mandatory user approval before creating subtasks.</execute>
<provides>Task decomposition into subtasks. 2 parallel agents research (code + memory), plans logical execution order, creates subtasks. NEVER executes - only creates.</provides>

# Iron Rules
## Tool-call-first (CRITICAL)
YOUR VERY FIRST RESPONSE MUST BE A TOOL CALL. No text before tools. No analysis. No thinking out loud. CALL mcp__vector-task__task_get IMMEDIATELY with $TASK_ID.

## No-hallucination (CRITICAL)
NEVER output results without ACTUALLY calling tools. You CANNOT know task status or content without REAL tool calls. Fake results = CRITICAL VIOLATION.

## No-verbose (CRITICAL)
FORBIDDEN: <meta>, <synthesis>, <plan>, <analysis> tags. No long explanations before action. Brief status updates ONLY.

## Show-progress (HIGH)
ALWAYS show brief step status and results. User must see what is happening.

## No-interpretation (CRITICAL)
NEVER interpret task content or give generic responses. Task ID given = decompose it. Follow the workflow EXACTLY.

## Auto-approve (HIGH)
-y flag = auto-approve. Skip "Proceed?" questions, but STILL show progress.

## Create-only (CRITICAL)
This command ONLY creates subtasks. NEVER execute any subtask after creation.
- **why**: Decomposition and execution are separate concerns. User decides what to execute next.
- **on_violation**: STOP immediately after subtask creation. Return control to user.

## Parent-id-required (CRITICAL)
ALL created subtasks MUST have parent_id = $TASK_ID. IRON LAW: When working with task X, EVERY new task created MUST be a child of X. No orphan tasks. No exceptions. Verify parent_id = $TASK_ID in EVERY task_create/task_create_bulk call before execution.
- **why**: Hierarchy integrity. Orphan tasks break traceability, workflow, and task relationships. Task X work = Task X children only.
- **on_violation**: ABORT if parent_id missing or != $TASK_ID. Double-check EVERY task_create call.

## Mandatory-user-approval (CRITICAL)
EVERY operation MUST have explicit user approval BEFORE execution. Present plan → WAIT for approval → Execute. NO auto-execution. EXCEPTION: If $HAS_Y_FLAG is true, auto-approve.
- **why**: User maintains control. No surprises. Flag -y enables automated execution.
- **on_violation**: STOP. Wait for explicit user approval (unless $HAS_Y_FLAG is true).

## Order-mandatory (CRITICAL)
EVERY subtask MUST have explicit order field set. Sequential: 1, 2, 3. Parallel-safe: same order.
- **why**: Order defines execution priority. Missing order = ambiguous sequence = blocked user.
- **on_violation**: Set order parameter in EVERY task_create call. Never omit.

## Sequence-analysis (CRITICAL)
When creating 2+ subtasks: STOP and THINK about optimal sequence. Consider: dependencies, data flow, setup requirements, parallel opportunities.
- **why**: Wrong sequence wastes time. User executes in order - if task 3 needs output from task 5, user is blocked.
- **on_violation**: Use SequentialThinking to analyze dependencies. Reorder before creation.

## Logical-order (HIGH)
Subtasks MUST be in logical execution order. Dependencies first, dependents after.
- **why**: Prevents blocked work. User can execute subtasks sequentially without dependency issues.
- **on_violation**: Reorder subtasks. Use SequentialThinking for complex dependencies.

## Exclude-brain-directory (HIGH)
NEVER analyze .brain/ when decomposing code tasks.
- **why**: Brain system internals are not project code.
- **on_violation**: Skip .brain/ in all exploration.


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($HAS_Y_FLAG = {true if $RAW_INPUT contains "-y" or "--yes"})
STORE-AS($CLEAN_ARGS = {$RAW_INPUT with flags removed})
STORE-AS($TASK_ID = {numeric ID extracted from $CLEAN_ARGS})

# Workflow
GOAL(Decompose task into subtasks: load → research → plan → approve → create)
- `1`: OUTPUT(=== TASK:DECOMPOSE === Loading task #$TASK_ID...)
- `2`: mcp__vector-task__task_get('{task_id: $TASK_ID}') → STORE-AS($TASK)
- `3`: IF(not found) → ABORT "Task #$TASK_ID not found"
- `4`: mcp__vector-task__task_list('{parent_id: $TASK_ID, limit: 50}') → STORE-AS($EXISTING_SUBTASKS)
- `5`: IF(EXISTING_SUBTASKS.count > 0 AND NOT $HAS_Y_FLAG) →
  Task has {count} existing subtasks.
  Ask: "(1) Add more, (2) Replace all, (3) Abort"
  WAIT for user choice
→ END-IF
- `6`: OUTPUT(Task: #{$TASK.id} - {$TASK.title} Status: {$TASK.status} | Priority: {$TASK.priority} Existing subtasks: {count})
- `7`: OUTPUT( ## RESEARCH (2 agents parallel))
- `8`: Launch 2 agents in PARALLEL (single message with multiple Task calls):
- `9`: [DELEGATE] @agent-explore: 'DECOMPOSITION RESEARCH for task #{$TASK.id}: "{$TASK.title}". Find: files, components, dependencies, natural split boundaries. EXCLUDE: .brain/. OUTPUT: {files:[], components:[], boundaries:[]}' Task(@agent-vector-master, 'TASK →'."\\n"
    .'  Memory search for: task decomposition patterns, similar implementations, past estimates'."\\n"
    .'→ END-TASK', 'STORE-AS($MEMORY_INSIGHTS)')
- `10`: STORE-AS($CODE_INSIGHTS = {from explore agent})
- `11`: OUTPUT( ## PLANNING)
- `12`: mcp__sequential-thinking__sequentialthinking('{'."\\n"
    .'                thought: "Synthesizing research: CODE_INSIGHTS + MEMORY_INSIGHTS. Identifying: logical boundaries, component coupling, data dependencies, effort distribution.",'."\\n"
    .'                thoughtNumber: 1,'."\\n"
    .'                totalThoughts: 3,'."\\n"
    .'                nextThoughtNeeded: true'."\\n"
    .'            }')
- `13`: Create subtask plan: group by component, order by dependency, estimate each
- `14`: IF(2+ subtasks) →
  STOP: Analyze optimal execution sequence
  Consider: What depends on what? What can run parallel? What needs setup first?
  Assign order: 1=first, 2=second, same order=parallel-safe
→ END-IF
- `15`: STORE-AS($SUBTASK_PLAN = [{title, content, estimate, priority, order}])
- `16`: IF(3+ subtasks) →
  mcp__sequential-thinking__sequentialthinking('{thought: "Analyze dependencies and optimal order for subtasks", thoughtNumber: 1, totalThoughts: 3, nextThoughtNeeded: true}')
→ END-IF
- `17`: OUTPUT( ## PLAN)
- `18`: Show table: | Order | Subtask | Est | Priority | Depends |
- `19`: IF($HAS_Y_FLAG) → OUTPUT(Auto-approved (-y flag))
- `20`: IF(NOT $HAS_Y_FLAG) →
  Ask: "Create {count} subtasks? (yes/no/modify)"
  WAIT for approval
→ END-IF
- `21`: OUTPUT( ## CREATING)
- `22`: mcp__vector-task__task_create_bulk('{tasks: [{title, content, parent_id: $TASK_ID, priority, estimate, order, tags: [...$TASK.tags, "decomposed"]}]}')
- `23`: mcp__vector-task__task_list('{parent_id: $TASK_ID}') → verify created
- `24`: mcp__vector-memory__store_memory('{content: "DECOMPOSED|#{$TASK.id}|subtasks:{count}", category: "tool-usage", tags: ["task-decomposition"]}')
- `25`: OUTPUT( === DECOMPOSITION COMPLETE === Created: {count} subtasks Next: /task:list --parent={$TASK_ID})
- `26`: STOP: Do NOT execute subtasks. Return control to user.

# Error handling
Graceful error recovery
- `1`: IF(task not found) →
  Report error
  Suggest task_list
  ABORT
→ END-IF
- `2`: IF(agent fails) →
  Log error
  Continue with available data
  Report partial results
→ END-IF
- `3`: IF(user rejects) →
  Accept modifications
  Rebuild plan
  Re-submit for approval
→ END-IF

</command>