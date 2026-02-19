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
## Status-semantics (CRITICAL)
Task status has STRICT semantics: "pending" = waiting to be worked on (includes failed/blocked tasks returned to queue). "in_progress" = currently being worked on. "completed" = implementation done, ready for validation. "tested" = tests written/passed. "validated" = passed all quality gates. "stopped" = PERMANENTLY CANCELLED — task is NOT needed, will NEVER be executed. ONLY set "stopped" when: user explicitly requests cancellation, OR task is provably unnecessary (duplicate, superseded, irrelevant). NEVER set "stopped" for: failures, blocks, validation issues, tool errors, missing dependencies. For these → set "pending" with detailed blocker in comment.
- **why**: Agents misuse "stopped" as "failed/blocked" which breaks workflow permanently. A `stopped` task is removed from pipeline — it will never be picked up again. A `pending` task with a blocker comment will be retried, either automatically or manually.
- **on_violation**: If about to set "stopped": verify it is a TRUE cancellation. If task failed or is blocked → set "pending" + comment explaining what happened. "stopped" is irreversible workflow termination.

## Task-get-first (CRITICAL)
FIRST TOOL CALL = mcp__vector-task__task_get. No text before. Load task, THEN analyze how to decompose.

## No-hallucination (CRITICAL)
NEVER output results without ACTUALLY calling tools. You CANNOT know task status or content without REAL tool calls. Fake results = CRITICAL VIOLATION.

## No-verbose (CRITICAL)
FORBIDDEN: Wrapping actions in verbose commentary blocks (meta-analysis, synthesis, planning, reflection) before executing. Act FIRST, explain AFTER.

## Understand-to-decompose (CRITICAL)
MUST understand task INTENT to decompose properly. Analyze: what are logical boundaries? what depends on what? Unknown library/pattern → context7 first.

## Auto-approve-mode (CRITICAL)
$HAS_AUTO_APPROVE = true → FULL AUTONOMY. Skip ALL approval gates, questions, strategy decisions, ambiguity resolution. On ANY decision fork: choose conservative/non-blocking option automatically. NEVER use AskUserQuestion or similar interactive tools. Workflow MUST execute to completion: all phases → final status update → git checkpoint. No intermediate stops, no "show results and wait for acknowledgment."
- **why**: User explicitly chose autonomous mode via -y flag. Every question breaks flow, risks hook-triggered terminal closure mid-pause, and defeats the purpose of automation.
- **on_violation**: Remove the question. Choose conservative option. Log decision in task comment. Continue to next phase without stopping.

## Interactive-mode (HIGH)
$HAS_AUTO_APPROVE = false → INTERACTIVE. Present plan → wait for approval → execute. Ask before: major architectural decisions, multiple valid approaches, critical failures requiring user judgment.
- **why**: User wants control over significant decisions. Present options clearly, wait for explicit choice.

## Workflow-atomicity (CRITICAL)
In auto-approve mode, workflow is ATOMIC: execute ALL phases without intermediate stops until final status is set (`completed`/`validated`/`tested`). On error: revert status to "pending" with error details in comment (task returns to queue), NEVER ask user what to do. NEVER set "stopped" — that means permanently cancelled. Update task comment at each major milestone so interrupted workflow has recoverable state.
- **why**: Hook-triggered terminal closure during a pause leaves task in limbo with no recoverable state. Atomic execution minimizes pause windows. Milestone comments enable session recovery without re-running `completed` phases. Failed tasks return to `pending` — they are not cancelled, just need another attempt.
- **on_violation**: If paused in auto-approve mode: immediately resume. If error: set status=`pending`, add error to comment, abort gracefully.

## Docs-are-law (CRITICAL)
Documentation is the SINGLE SOURCE OF TRUTH. If docs exist for task - FOLLOW THEM EXACTLY. No deviations, no "alternatives", no "options" that docs don't mention.
- **why**: User wrote docs for a reason. Asking about non-existent alternatives wastes time and shows you didn't read the docs.
- **on_violation**: Re-read documentation. Execute ONLY what docs specify.

## No-phantom-options (CRITICAL)
FORBIDDEN: Asking "keep as is / rewrite / both?" when docs specify ONE approach. If docs say HOW to do it - do it. Don't invent alternatives.
- **why**: Docs are the holy grail. Phantom options confuse user and delay work.
- **on_violation**: Check docs again. If docs are clear - execute. If genuinely ambiguous - ask about THAT ambiguity, not made-up options.

## Partial-work-continue (CRITICAL)
Partial implementation exists? Read DOCS first, understand FULL spec. Continue from where it `stopped` ACCORDING TO DOCS. Never ask "keep partial or rewrite" - docs define target state.
- **why**: Partial work means someone started following docs. Continue following docs, not inventing alternatives.
- **on_violation**: Read docs → understand target state → implement remaining parts per docs.

## Docs-over-existing-code (HIGH)
Conflict between docs and existing code? DOCS WIN. Existing code may be: WIP, placeholder, wrong, outdated. Docs define WHAT SHOULD BE.
- **why**: Code is implementation, docs are specification. Spec > current impl.

## Context-priority-chain (HIGH)
Conflict resolution priority: documentation > existing code > vector memory > assumptions. When sources disagree, higher-priority source wins. Documentation defines WHAT SHOULD BE. Code shows WHAT IS NOW. Memory shows WHAT WAS BEFORE. Assumptions are last resort when all sources are absent.
- **why**: Multiple context sources may contradict each other. Without explicit priority chain, agents pick whichever they loaded first. Clear hierarchy eliminates ambiguity in conflict resolution.

## Aggressive-docs-search (CRITICAL)
NEVER search docs with single exact query. Generate 3-5 keyword variations: 1) split CamelCase (FocusModeTest → "FocusMode", "Focus Mode", "Focus"), 2) remove technical suffixes (Test, Controller, Service, Repository, Command, Handler, Provider), 3) extract domain words, 4) try singular/plural. Search until found OR 3+ variations tried.
- **why**: Docs may be named differently than code. "FocusModeTest" code → "Focus Mode" doc. Single exact search = missed docs = wrong decisions.
- **on_violation**: Generate keyword variations. Search each. Only conclude "no docs" after 3+ failed searches.

## Codebase-pattern-reuse (CRITICAL)
BEFORE implementing: search codebase for similar/analogous implementations. Grep for: similar class names, method signatures, trait usage, helper utilities. Found → REUSE approach, follow same patterns, extend existing code. Not found → proceed independently. NEVER reinvent what already exists in the project.
- **why**: Codebase consistency > personal style. Duplicate implementations create maintenance burden, inconsistency, and confusion. Existing patterns are battle-`tested`.
- **on_violation**: STOP. Search codebase for analogous code. Found → study and follow the pattern. Only then proceed.

## Comment-context-mandatory (CRITICAL)
AFTER loading task: parse task.comment for accumulated context. Extract: memory IDs (#NNN), file paths, previous execution results, `failure` reasons, blockers, decisions made. Store as $COMMENT_CONTEXT. Pass to ALL agents alongside task.content.
- **why**: Comments accumulate critical inter-session context: what was tried, what failed, what files were touched, what decisions were made. Ignoring comments = blind re-execution without history.
- **on_violation**: Parse task.comment IMMEDIATELY after task_get. Extract actionable context. Include in agent prompts and planning.

## Docs-define-structure (CRITICAL)
Documentation defines STRUCTURE for decomposition. If docs describe modules/components/phases → decompose ACCORDING TO DOCS. Code exploration is SECONDARY.
- **why**: Docs contain planned architecture. Code may be incomplete WIP. Decomposing by code misses planned structure.
- **on_violation**: Read docs FIRST. Decompose per documented structure. Code exploration fills gaps.

## Smart-decompose (CRITICAL)
Analyze task for decomposability based on SCOPE, not estimate hours. TWO outcomes: 1) DECOMPOSABLE — task has 2+ DISTINCT CONCERNS where each subtask has its OWN file scope (different files/modules) and involves code logic changes → create subtasks normally. 2) ATOMIC — task is single concern (one module, 1-2 files), OR splitting would produce subtasks without distinct file scope, OR is doc-only/config-only → add tag "atomic", set comment "Atomic: {reason}", return status "pending", STOP. If task ALREADY has "atomic" tag → STOP immediately (already evaluated). NEVER force-split atomic tasks into artificial micro-tasks (PHPDoc-only, rename-only, formatting-only).
- **why**: Decomposition decision = SCOPE (how many distinct concerns/files/modules), NOT time. A 6h single-file algorithm is atomic. A 3h task touching 4 modules is decomposable. Forced decomposition of atomic tasks creates micro-tasks that waste full lifecycle tokens.
- **on_violation**: Re-evaluate: does each subtask have its OWN distinct file scope and concern? No → tag atomic + STOP.

## Minimum-subtask-complexity (CRITICAL)
Each leaf subtask MUST have: 1) distinct file scope (different files than siblings), 2) distinct concern (not a sub-step of same operation), 3) code logic changes (not doc-only, test-only, formatting-only). Doc/PHPDoc/README changes → merge into the implementation subtask that touches same module. If after merging only 1 subtask remains → task is atomic.
- **why**: Subtasks without distinct scope create overhead that exceeds the work. 17 tool calls + validation cycle for a PHPDoc block = pure token waste. Scope separation is the REAL decomposition criterion.
- **on_violation**: Merge trivial work into implementation subtask. Single subtask remaining = atomic.

## Create-only (CRITICAL)
This command ONLY creates subtasks. NEVER execute any subtask after creation.
- **why**: Decomposition and execution are separate concerns. User decides what to execute next.
- **on_violation**: STOP immediately after subtask creation. Return control to user.

## Parent-id-mandatory (CRITICAL)
ALL new tasks/subtasks created MUST have parent_id = $TASK_ID. No orphan tasks. No exceptions.
- **why**: Task hierarchy integrity. Orphan tasks break traceability and workflow.
- **on_violation**: ABORT task_create if parent_id missing or != $TASK_ID.

## Mandatory-user-approval (CRITICAL)
EVERY operation MUST have explicit user approval BEFORE execution. Present plan → WAIT for approval → Execute. NO auto-execution. EXCEPTION: If $HAS_AUTO_APPROVE is true, auto-approve.
- **why**: User maintains control. No surprises. Flag -y enables automated execution.
- **on_violation**: STOP. Wait for explicit user approval (unless $HAS_AUTO_APPROVE is true).

## Order-mandatory (CRITICAL)
EVERY subtask MUST have unique order (1,2,3,4) AND explicit parallel flag. Independent subtasks that CAN run concurrently = parallel: true. Dependent subtasks = parallel: false.
- **why**: Order defines strict sequence. Parallel flag enables executor to run independent tasks concurrently without re-analyzing dependencies.
- **on_violation**: Set order (unique) + parallel (bool) in EVERY task_create call. Never omit either.

## Sequence-analysis (CRITICAL)
When creating 2+ subtasks: STOP and THINK about optimal sequence. Use SequentialThinking to analyze dependencies before setting order and parallel flags.
- **why**: Wrong sequence wastes time. Wrong parallel marking causes race conditions.
- **on_violation**: Use SequentialThinking to analyze dependencies. Set order + parallel before creation.

## Parallel-isolation-mandatory (CRITICAL)
Before setting parallel: true, ALL isolation conditions MUST be verified: 1) ZERO file overlap — tasks touch completely different files, 2) ZERO import chain — file A does NOT import/use/require anything from file B scope, 3) ZERO shared model/table — tasks do NOT modify same DB table/migration/model, 4) ZERO shared config — tasks do NOT modify same config key/.env variable, 5) ZERO output→input — task B does NOT need result/output of task A. ALL five MUST be TRUE.
- **why**: Parallel tasks with shared files or dependencies cause race conditions, lost changes, and merge conflicts. LLM agents cannot lock files.
- **on_violation**: Set parallel: false. When in doubt, sequential is always safe.

## Parallel-file-manifest (CRITICAL)
Before marking ANY task parallel: true, EXPLICITLY list ALL files each task will read/write/create. Cross-reference lists. If ANY file appears in 2+ tasks → parallel: false for ALL overlapping tasks. No exceptions.
- **why**: Implicit file overlap is the #1 cause of parallel task conflicts. Explicit manifest prevents it.
- **on_violation**: Create file manifest per task. Cross-reference. Overlap found = parallel: false.

## Parallel-conservative-default (HIGH)
Default is parallel: false. Only set parallel: true when ALL isolation conditions are PROVEN. Uncertain about independence = sequential. Cost of wrong parallel (lost work, conflicts) far exceeds cost of wrong sequential (slower execution).
- **why**: False negative (missing parallelism) = slower. False positive (wrong parallelism) = data loss. Asymmetric risk demands conservative default.
- **on_violation**: Revert to parallel: false.

## Parallel-transitive-deps (HIGH)
Check transitive dependencies: if task A modifies file X, and file X is imported by file Y, and task B modifies file Y — tasks A and B are NOT independent. Follow import/use/require chains one level deep minimum.
- **why**: Indirect dependencies through shared modules cause subtle race conditions and inconsistent state.
- **on_violation**: Trace import chain one level. Any indirect overlap = parallel: false.

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

## Batch-trivial-grouping (HIGH)
When ALL items are: identical operation (rename, format, move) + trivial (<5 min each, no logic change) + independent (no cross-file dependencies) → create 1 task with checklist, tags: [batch:trivial, strict:relaxed, cognitive:minimal]. Do NOT decompose into separate subtasks.
- **why**: Trivial batch operations gain nothing from parallelism. 5 identical tasks waste 5x planning overhead.
- **on_violation**: Evaluate if items are truly independent and trivial. If yes → single task with checklist.

## File-scope-in-content (CRITICAL)
When creating subtasks: task content MUST include explicit file scope: "FILES: [file1.php, file2.php, ...]" from file_manifest. For parallel: true subtasks ALSO include: "PARALLEL: this task may execute concurrently with siblings. Stay within listed file scope." Without explicit files, executors guess scope and parallel conflict detection fails.
- **why**: Parallel execution awareness reads file scopes from task content and comments. Decompose is the ONLY place where planned file scope is known before execution. If not included in content, the entire parallel safety chain starts blind.
- **on_violation**: Add "FILES: [...]" to content of EVERY subtask. Add "PARALLEL: ..." note for every parallel: true subtask.

## Logical-order (HIGH)
Subtasks MUST be in logical execution order. Dependencies first, dependents after.
- **why**: Prevents blocked work. User can execute subtasks sequentially without dependency issues.
- **on_violation**: Reorder subtasks. Use SequentialThinking for complex dependencies.

## No-test-quality-subtasks (CRITICAL)
FORBIDDEN: Creating subtasks for "Write tests", "Add test coverage", "Run quality gates", "Code quality checks", "Verify implementation", or similar. These are ALREADY handled automatically: 1) Executors (sync/async) write tests during implementation (>=80% coverage, edge cases). 2) Validators run ALL quality gates and check coverage. Decompose ONLY into functional work units.
- **why**: Each executor writes tests inline. Each validator runs quality gates. Separate test/quality subtasks are always redundant — executor sees them and says "already done", wasting tokens and time.
- **on_violation**: Remove test/quality/verification subtasks from plan. Tests are part of EACH implementation subtask, not a separate subtask.

## Exclude-brain-directory (HIGH)
NEVER analyze .brain/ when decomposing code tasks.
- **why**: Brain system internals are not project code.
- **on_violation**: Skip .brain/ in all exploration.


# Aggressive docs search
GOAL(Find documentation even if named differently than task/code)
- `1`: Generate keyword variations from task title/content:
- `2`:   1. Original: "FocusModeTest" → search "FocusModeTest"
- `3`:   2. Split CamelCase: "FocusModeTest" → search "FocusMode", "Focus Mode"
- `4`:   3. Remove suffix: "FocusModeTest" → search "Focus" (remove Mode, Test)
- `5`:   4. Domain words: extract meaningful nouns → search each
- `6`:   5. Parent context: if task has parent → include parent title keywords
- `7`: Common suffixes to STRIP: Test, Tests, Controller, Service, Repository, Command, Handler, Provider, Factory, Manager, Helper, Validator, Processor
- `8`: Search ORDER: most specific → most general. STOP when found.
- `9`: Minimum 3 search attempts before concluding "no documentation".
- `10`: WRONG: brain docs "UserAuthenticationServiceTest" → not found → done
- `11`: RIGHT: brain docs "UserAuthenticationServiceTest" → not found → brain docs "UserAuthentication" → not found → brain docs "Authentication" → FOUND!

# Comment context extraction
GOAL(Extract actionable context from task.comment before any execution or delegation)
- `1`: Parse $TASK.comment (may be multi-line with \\n\\n separators):
- `2`:   1. MEMORY IDs: extract #NNN or memory #NNN patterns → previous knowledge links
- `3`:   2. FILE PATHS: extract file paths (src/*, tests/*, app/*, etc.) → files already touched/identified
- `4`:   3. EXECUTION HISTORY: entries with "completed", "passed", "started", "Done" → what was already done
- `5`:   4. FAILURES: entries with "failed", "error", "stopped", "rolled back" → what went wrong and why
- `6`:   5. BLOCKERS: entries with "BLOCKED", "waiting for", "needs" → current impediments
- `7`:   6. DECISIONS: entries with "chose", "decided", "approach", "using" → decisions already locked in
- `8`:   7. MODE FLAGS: "TDD MODE", "light validation", special execution modes
- `9`: STORE-AS($COMMENT_CONTEXT = {memory_ids: [], file_paths: [], execution_history: [], failures: [], blockers: [], decisions: [], mode_flags: []})
- `10`: If comment is empty/null → $COMMENT_CONTEXT = {} (proceed without, no error)

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
STORE-AS($HAS_AUTO_APPROVE = {true if $RAW_INPUT contains "-y" or "--yes"})
STORE-AS($CLEAN_ARGS = {$RAW_INPUT with -y/--yes flags removed})
STORE-AS($VECTOR_TASK_ID = {numeric ID extracted from $CLEAN_ARGS})

# Workflow
GOAL(Decompose task into subtasks: load → research → plan → approve → create)
- `1`: mcp__vector-task__task_get('{task_id: $VECTOR_TASK_ID}') → STORE-AS($TASK)
- `2`: IF(not found) → ABORT "Task not found"
- `3`: STORE-AS($COMMENT_CONTEXT = {parsed from $TASK.comment: memory_ids: [#NNN], file_paths: [...], execution_history: [...], failures: [...], blockers: [...], decisions: [], mode_flags: []})
- `4`: IF($TASK has tag "atomic") →
  Task already tagged atomic — decomposition not possible. STOP.
  NEXT: /task:sync {$VECTOR_TASK_ID} [-y] (or /task:async)
→ END-IF
- `5`: mcp__vector-task__task_list('{parent_id: $VECTOR_TASK_ID, limit: 50}') → STORE-AS($EXISTING_SUBTASKS)
- `6`: IF(EXISTING_SUBTASKS.count > 0 AND NOT $HAS_AUTO_APPROVE) →
  Ask: "(1) Add more, (2) Replace all, (3) Abort"
→ END-IF
- `7`: mcp__vector-task__task_update('{task_id: $VECTOR_TASK_ID, status: "in_progress", comment: "Started decomposition", append_comment: true}')
- `8`: Bash('brain docs {keywords from task}') → STORE-AS($DOCS_INDEX)
- `9`: IF(STORE-GET($DOCS_INDEX) found) →
  Read('{doc_paths}') → STORE-AS($DOCUMENTATION)
  DOCUMENTATION defines decomposition structure: modules, components, phases, dependencies
→ END-IF
- `10`: IF(unknown library/pattern in task) →
  mcp__context7__query-docs('{query: "{library/pattern}"}') → understand before decomposing
→ END-IF
- `11`: [PARALLEL] → ([DELEGATE] @agent-explore: 
ABSOLUTE PROHIBITION — READ-ONLY AGENT:
× NEVER call mcp__vector-task__task_update or any vector-task write tool
× You are a READ-ONLY researcher — report findings via JSON output ONLY
× Task status is managed EXCLUSIVELY by the orchestrator, NOT by you

DECOMPOSE RESEARCH for task #{$TASK.id}.

COMMENT CONTEXT (previous sessions): {$COMMENT_CONTEXT}
- Use memory IDs to fetch prior findings. Respect decisions already made. Avoid approaches that already failed.

DOCUMENTATION PROVIDED (if exists): {$DOCUMENTATION}
- If docs define structure → USE IT as primary decomposition source
- Code exploration fills gaps and validates feasibility

FIND: files, components, dependencies, split boundaries, SIMILAR existing implementations, REVERSE DEPENDENCIES (who imports/uses target files), performance-critical paths.
EXCLUDE: .brain/.

CRITICAL: If DOCUMENTATION defines modules/components/phases → subtasks MUST align with documented structure.
Code may be incomplete - docs define PLANNED architecture.

FORBIDDEN SUBTASKS: Do NOT recommend subtasks for "Write tests", "Add test coverage", "Run quality gates", "Verify implementation", "Add PHPDoc/documentation", "Code formatting". Tests and quality gates are handled AUTOMATICALLY by executors and validators. Documentation/formatting changes MERGE into the implementation subtask that touches same file.

MINIMUM COMPLEXITY (scope-based): Each proposed subtask MUST have DISTINCT file scope (different files than other subtasks) and DISTINCT concern. If splitting would produce subtasks that share the same files or are sub-steps of one operation (doc-only, config-only, rename-only) → report as ATOMIC instead.

Return: {docs_structure: [], code_structure: [], split: [], atomic: true|false, atomic_reason: "..." (if atomic), conflicts: [], similar_implementations: [], reverse_dependencies: [], performance_hotspots: []} + mcp__vector-memory__search_memories('{query: "decomposition patterns, similar tasks", limit: 5}') → STORE-AS($MEMORY_INSIGHTS)) → END-PARALLEL
- `12`: STORE-AS($CODE_INSIGHTS = {from explore agent})
- `13`: mcp__sequential-thinking__sequentialthinking({
                thought: "Synthesizing: DOCUMENTATION (primary) + CODE_INSIGHTS (secondary) + MEMORY_INSIGHTS. If docs define structure → USE IT. Code fills gaps. Identify: boundaries, dependencies, parallel opportunities, order. For EACH subtask pair: do they share files? Does B need output of A? Same DB tables? If NO to all → both can be parallel: true.",
                thoughtNumber: 1,
                totalThoughts: 2,
                nextThoughtNeeded: true
            })
- `14`: If DOCUMENTATION exists: subtasks MUST align with documented modules/components/phases
- `15`: Group by component (per docs), order by dependency, estimate each
- `16`: PARALLEL ISOLATION: Apply parallel-isolation-checklist for each subtask pair. Setup/foundation tasks → always parallel: false.
- `17`: STORE-AS($SUBTASK_PLAN = [{title, content, estimate, priority, order, parallel, file_manifest: [files], doc_reference}])
- `18`: CONTENT ENRICHMENT: For each subtask in PLAN:
- `19`:   - Include "FILES: [file_manifest]" in content — executor needs explicit file scope
- `20`: IF(subtask.parallel === true) →
    - Append to content: "PARALLEL: this task may execute concurrently with sibling tasks. Stay within listed file scope. Other siblings will read your scope from task comment."
→ END-IF
- `21`: IF(CODE_INSIGHTS.atomic === true OR SUBTASK_PLAN has only 1 subtask OR subtasks share same file scope OR subtasks are sub-steps of single concern) →
  mcp__vector-task__task_update('{task_id: $VECTOR_TASK_ID, status: "pending", comment: "Atomic: single concern / single file scope — cannot decompose into distinct subtasks. Reason: {atomic_reason}. Ready for direct execution.", append_comment: true, add_tag: "atomic"}')
  RESULT: ATOMIC — task tagged, returned to `pending`.
  NEXT: /task:sync {$VECTOR_TASK_ID} [-y] (or /task:async). Task is atomic — execute directly.
  STOP.
→ END-IF
- `22`: Show: | Order | Parallel | Subtask | Est | Priority | Doc Ref |
- `23`: Visualize parallel groups: sequential tasks = "→", parallel tasks = "⇉"
- `24`: IF($HAS_AUTO_APPROVE) →
  Auto-approved
→ ELSE →
  Ask: "Create {count} subtasks? (yes/no/modify)"
→ END-IF
- `25`: mcp__vector-task__task_create_bulk('{tasks: [{title, content (with FILES + PARALLEL note), parent_id: $VECTOR_TASK_ID, priority, estimate, order, parallel, tags: ["decomposed"]}]}')
- `26`: mcp__vector-task__task_list('{parent_id: $VECTOR_TASK_ID}') → verify
- `27`: mcp__vector-task__task_update('{task_id: $VECTOR_TASK_ID, status: "pending", comment: "Decomposed into {count} subtasks. Ready for execution.", append_comment: true}')
- `28`: STOP: Do NOT execute. Return control to user.

# Error handling
- `1`: IF(task not found) → ABORT "suggest task_list"
- `2`: IF(agent fails) → Continue with available data
- `3`: IF(user rejects plan) → Accept modifications, rebuild, re-submit

</command>