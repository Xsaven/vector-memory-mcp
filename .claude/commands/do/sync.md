---
name: "do:sync"
description: "Direct execution command - Brain executes tasks directly without agent delegation"
---

<command>
<meta>
<id>do:sync</id>
<description>Direct execution command - Brain executes tasks directly without agent delegation</description>
</meta>
<execute>Direct synchronous task execution by Brain without agent delegation. Uses Read/Edit/Write/Glob/Grep tools directly. Single approval gate. Best for: simple tasks, quick fixes, single-file changes, when agent overhead is unnecessary. Accepts $ARGUMENTS task description. Zero distractions, atomic execution, strict plan adherence.</execute>
<provides>Direct synchronous task execution by Brain without agent delegation. Uses Read/Edit/Write/Glob/Grep tools directly. Single approval gate. Best for: simple tasks, quick fixes, single-file changes, when agent overhead is unnecessary. Accepts task description as input. Zero distractions, atomic execution, strict plan adherence.</provides>

# Iron Rules
## Zero-distractions (CRITICAL)
ZERO distractions - implement ONLY specified task from $TASK_DESCRIPTION. NO creative additions, NO unapproved features, NO scope creep.
- **why**: Ensures focused execution and prevents feature drift
- **on_violation**: Abort immediately. Return to approved plan.

## No-delegation (CRITICAL)
Brain executes ALL steps directly. NO Task() delegation to agents. Use ONLY direct tools: Read, Edit, Write, Glob, Grep, Bash.
- **why**: Sync mode is for direct execution without agent overhead
- **on_violation**: Remove Task() calls. Execute directly.

## Single-approval-gate (CRITICAL)
User approval REQUIRED before execution. Present plan, WAIT for confirmation, then execute without interruption. EXCEPTION: If $HAS_Y_FLAG is true, auto-approve (skip waiting for user confirmation).
- **why**: Single checkpoint for simple tasks - approve once, execute fully. The -y flag enables unattended/scripted execution.
- **on_violation**: STOP. Wait for user approval before execution (unless $HAS_Y_FLAG is true).

## Atomic-execution (CRITICAL)
Execute ONLY approved plan steps. NO improvisation, NO "while we're here" additions. Atomic changes only.
- **why**: Maintains plan integrity and predictability
- **on_violation**: Revert to approved plan. Resume approved steps only.

## Read-before-edit (HIGH)
ALWAYS Read file BEFORE Edit/Write. Never modify files blindly.
- **why**: Ensures accurate edits based on current file state
- **on_violation**: Read file first, then proceed with edit.

## Vector-memory-integration (HIGH)
Search vector memory BEFORE planning. Store learnings AFTER completion.
- **why**: Leverages past solutions, builds knowledge base
- **on_violation**: Include memory search in analysis, store insights after.


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($TASK_DESCRIPTION = {task description extracted from $RAW_INPUT})

# Phase1 context analysis
GOAL(Analyze task and gather context from conversation + memory)
- `1`: STORE-AS($HAS_Y_FLAG = {true if $RAW_INPUT contains "-y" or "--yes"})
- `2`: STORE-AS($TASK = {$TASK_DESCRIPTION with flags removed, trimmed})
- `3`: Analyze conversation: requirements, constraints, preferences, prior decisions
- `4`: mcp__vector-memory__search_memories('{query: "similar: {$TASK}", limit: 5, category: "code-solution"}')
- `5`: STORE-AS($PRIOR_SOLUTIONS = Relevant past approaches)
- `6`: OUTPUT(=== CONTEXT === Task: {$TASK} Prior solutions: {summary or "none found"})

# Phase1.5 material gathering
GOAL(Collect materials per plan and store to vector memory. NOTE: command `brain docs` returns file index (Path, Name, Description, etc.), then Read relevant files)
- `1`: FOREACH(scan_target in $REQUIREMENTS_PLAN.scan_targets) →
  Context extraction from {scan_target}
  STORE-AS($GATHERED_MATERIALS[{TARGET}] = Extracted context)
→ END-FOREACH
- `2`: IF($DOCS_SCAN_NEEDED === true) →
  Bash(brain docs {keywords}) → [Find documentation index (returns: Path, Name, Description)] → END-Bash
  STORE-AS($DOCS_INDEX = Documentation file index)
  FOREACH(doc in $DOCS_INDEX) → Read('{doc.path}')
  STORE-AS($DOCS_SCAN_FINDINGS = Documentation content)
→ END-IF
- `3`: IF($WEB_RESEARCH_NEEDED === true) →
  WebSearch(Research best practices for {$TASK})
  STORE-AS($WEB_RESEARCH_FINDINGS = External knowledge)
→ END-IF
- `4`: mcp__vector-memory__store_memory('{content: "Context for {$TASK}\\\\n\\\\nMaterials: {summary}", category: "tool-usage", tags: ["do-command", "context-gathering"]}')
- `5`: OUTPUT(=== PHASE 1.5: MATERIALS GATHERED === Materials: {count} | Docs: {status} | Web: {status} Context stored to vector memory ✓)

# Phase2 exploration planning
GOAL(Explore codebase, identify targets, create execution plan)
- `1`: Identify files to examine based on task description
- `2`: Glob(Find relevant files: patterns based on task)
- `3`: Grep(Search for relevant code patterns)
- `4`: Read(Read identified files for context)
- `5`: STORE-AS($CONTEXT = {files_found, code_patterns, current_state})
- `6`: mcp__sequential-thinking__sequentialthinking('{'."\\n"
    .'                thought: "Planning direct execution. Analyzing: file dependencies, edit sequence, atomic steps, potential conflicts, rollback strategy.",'."\\n"
    .'                thoughtNumber: 1,'."\\n"
    .'                totalThoughts: 2,'."\\n"
    .'                nextThoughtNeeded: true'."\\n"
    .'            }')
- `7`: Create atomic execution plan: specific edits with exact changes
- `8`: STORE-AS($PLAN = [{step_N, file, action: read|edit|write, description, exact_changes}, ...])
- `9`: OUTPUT( === EXECUTION PLAN === Files: {list} Steps: {numbered_steps_with_descriptions}  ⚠️ APPROVAL REQUIRED ✅ approved/yes | ❌ no/modifications)
- `10`: IF($HAS_Y_FLAG === true) →
  AUTO-APPROVED (unattended mode)
  OUTPUT(🤖 Auto-approved via -y flag)
→ END-IF
- `11`: IF($HAS_Y_FLAG === false) →
  WAIT for user approval
  VERIFY-SUCCESS(User approved)
  IF(rejected) → Modify plan → Re-present → WAIT
→ END-IF

# Phase3 direct execution
GOAL(Execute plan directly using Brain tools - no delegation)
- `1`: FOREACH(step in $PLAN) →
  OUTPUT(▶️ Step {N}: {step.description})
  IF(step.action === "read") →
  Read('{step.file}')
  STORE-AS($FILE_CONTENT[{N}] = File content)
→ END-IF
  IF(step.action === "edit") →
  Read('{step.file}')
  Edit('{step.file}', '{old_string}', '{new_string}')
→ END-IF
  IF(step.action === "write") → Write('{step.file}', '{content}')
  STORE-AS($STEP_RESULTS[{N}] = Result)
  OUTPUT(✅ Step {N} complete)
→ END-FOREACH
- `2`: IF(step fails) →
  Log error
  Offer: Retry / Skip / Abort
  WAIT for user decision
→ END-IF

# Phase4 completion
GOAL(Report results and store learnings to vector memory)
- `1`: STORE-AS($SUMMARY = {completed_steps, files_modified, outcome})
- `2`: mcp__vector-memory__store_memory('{content: "Completed: {$TASK}\\\\n\\\\nApproach: {steps}\\\\n\\\\nFiles: {list}\\\\n\\\\nLearnings: {insights}", category: "code-solution", tags: ["do:sync", "completed"]}')
- `3`: OUTPUT( === COMPLETE === Task: {$TASK} | Status: {SUCCESS/PARTIAL/FAILED} ✓ Steps: {`completed`}/{total} | 📁 Files: {count} {outcomes})

# Error handling
Direct error handling without agent fallback
- `1`: IF(file not found) →
  Report: "File not found: {path}"
  Offer: Create new file / Specify correct path / Abort
→ END-IF
- `2`: IF(edit conflict) →
  Report: "old_string not found in file"
  Re-read file, adjust edit, retry
→ END-IF
- `3`: IF(user rejects plan) →
  Accept modifications
  Rebuild plan
  Re-present for approval
→ END-IF

# Example simple fix
SCENARIO(Simple bug fix)
- `input`: "Fix typo in UserController.php line 42"
- `plan`: 1 step: Edit UserController.php
- `execution`: Read → Edit → Done
- `result`: 1/1 ✓

# Example add method
SCENARIO(Add method to existing class)
- `input`: "Add getFullName() method to User model"
- `plan`: 2 steps: Read User.php → Edit to add method
- `execution`: Read → Edit → Done
- `result`: 2/2 ✓

# Example config change
SCENARIO(Configuration update)
- `input`: "Change cache driver to redis in config"
- `plan`: 2 steps: Read config/cache.php → Edit driver value
- `execution`: Read → Edit → Done
- `result`: 2/2 ✓

# Sync vs async
When to use /do:sync vs /do:async
- `USE /do:sync`: Simple tasks, single-file changes, quick fixes, config updates, typo fixes, adding small methods
- `USE /do:async`: Complex multi-file tasks, tasks requiring research, architecture changes, tasks benefiting from specialized agents

# Response format
=== headers | single approval | progress | files | Direct execution, no filler

</command>