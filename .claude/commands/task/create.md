---
name: "task:create"
description: "Create task from description with analysis and estimation"
---

<command>
<meta>
<id>task:create</id>
<description>Create task from description with analysis and estimation</description>
</meta>
<execute>Creates task(s) from user description provided via $ARGUMENTS. Analyzes relevant materials, searches vector memory for similar past work, estimates time, gets mandatory user approval, creates task(s), and recommends decomposition if estimate >5-8 hours. Golden rule: each task 5-8 hours max.</execute>
<provides>Task creation: analyzes description, researches context (memory, codebase, docs), estimates effort, creates well-structured task after approval. NEVER executes.</provides>

# Iron Rules
## Status-semantics (CRITICAL)
Task status has STRICT semantics: "pending" = waiting to be worked on (includes failed/blocked tasks returned to queue). "in_progress" = currently being worked on. "completed" = implementation done, ready for validation. "tested" = tests written/passed. "validated" = passed all quality gates. "stopped" = PERMANENTLY CANCELLED — task is NOT needed, will NEVER be executed. ONLY set "stopped" when: user explicitly requests cancellation, OR task is provably unnecessary (duplicate, superseded, irrelevant). NEVER set "stopped" for: failures, blocks, validation issues, tool errors, missing dependencies. For these → set "pending" with detailed blocker in comment.
- **why**: Agents misuse "stopped" as "failed/blocked" which breaks workflow permanently. A `stopped` task is removed from pipeline — it will never be picked up again. A `pending` task with a blocker comment will be retried, either automatically or manually.
- **on_violation**: If about to set "stopped": verify it is a TRUE cancellation. If task failed or is blocked → set "pending" + comment explaining what happened. "stopped" is irreversible workflow termination.

## Analyze-first (CRITICAL)
MUST analyze input thoroughly before creating. Extract: objective, scope, requirements, type (feature/bugfix/refactor/research/docs).

## Research-before-create (CRITICAL)
MUST research context: 1) existing tasks (duplicates?), 2) vector memory (prior work), 3) PROJECT DOCUMENTATION (.docs/), 4) codebase (if code-related), 5) context7 (if unknown lib/pattern).

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

## Docs-define-task-scope (CRITICAL)
If documentation exists for task domain → task.content MUST reference docs. Estimate based on FULL spec from docs, not brief description.
- **why**: Documentation contains complete requirements. Task without doc reference = incomplete context for executor.
- **on_violation**: Search docs first. If found → include doc reference in task.content and comment.

## Estimate-required (CRITICAL)
MUST provide time estimate for human planning reference. Decomposition is NOT driven by estimate — it is driven by SCOPE (distinct concerns, files, modules). Estimate is informational. Doc-only/comment-only/formatting-only tasks are NEVER standalone — they belong as part of the implementation task that touches same module.

## Create-only (CRITICAL)
This command ONLY creates tasks. NEVER execute after creation. User decides via /task:next or /do.

## Comment-with-context (HIGH)
Initial comment MUST contain: memory IDs, relevant file paths, related task IDs. Preserves research for executor.

## Mandatory-user-approval (CRITICAL)
EVERY operation MUST have explicit user approval BEFORE execution. Present plan → WAIT for approval → Execute. NO auto-execution. EXCEPTION: If $HAS_AUTO_APPROVE is true, auto-approve.
- **why**: User maintains control. No surprises. Flag -y enables automated execution.
- **on_violation**: STOP. Wait for explicit user approval (unless $HAS_AUTO_APPROVE is true).

## No-test-quality-tasks (HIGH)
Do NOT create standalone tasks for "Write tests", "Add test coverage", "Run quality gates" if they relate to work covered by another task. Tests and quality gates are handled automatically by executors and validators. EXCEPTION: user EXPLICITLY requests a dedicated test task.
- **why**: Executors write tests during implementation (>=80% coverage). Validators run quality gates. Standalone test tasks duplicate this work.
- **on_violation**: If user describes test/quality task for existing work → explain that executors/validators handle this. Create only if user insists.

## Fast-path (HIGH)
Simple task (<140 chars, no "architecture/integration/multi-module"): skip heavy research, check duplicates + memory only.

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

## File-scope-in-content (HIGH)
Task content SHOULD include expected file scope: "FILES: [file1.php, file2.php, ...]" if codebase exploration identified relevant files. For parallel: true tasks this becomes CRITICAL — executors need explicit scope for parallel conflict detection. Files unknown (new feature) → "FILES: [to be determined during planning]".
- **why**: Parallel execution awareness reads file scopes from task content. Create is the first place where scope can be captured. Missing files = executor guesses = parallel safety chain weakened.
- **on_violation**: If codebase exploration found files → include as FILES in content. If parallel: true → MUST include whatever file scope is known.


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
STORE-AS($TASK_DESCRIPTION = {task description from $CLEAN_ARGS})

# Workflow
GOAL(Create task: parse → research → analyze → formulate → approve → create)
- `1`: Parse STORE-GET($TASK_DESCRIPTION) → STORE-AS($TASK_SCOPE = {objective, domain, type, requirements})
- `2`: STORE-AS($IS_SIMPLE = description <140 chars AND no architecture/integration/multi-module keywords)
- `3`: Bash('brain docs {domain} {objective}') → STORE-AS($DOCS_INDEX)
- `4`: IF(STORE-GET($DOCS_INDEX) found) →
  Read('{doc_paths}') → STORE-AS($DOCUMENTATION)
  DOCUMENTATION = COMPLETE specification. Use for: requirements, estimate, acceptance criteria.
→ END-IF
- `5`: IF(STORE-GET($IS_SIMPLE)) →
  mcp__vector-task__task_list('{query: "{objective}", limit: 5}') → check duplicates
  mcp__vector-memory__search_memories('{query: "{domain}", limit: 3}')
→ ELSE →
  [DELEGATE] @agent-explore: 'Search existing tasks for duplicates/related. Objective: {STORE-GET($TASK_SCOPE)}. Return: duplicates, potential parent, dependencies.' → STORE-AS($EXISTING_TASKS)
  mcp__vector-memory__search_memories('{query: "{domain} {objective}", limit: 5, category: "code-solution"}') → STORE-AS($PRIOR_WORK)
  IF(code-related task) →
  [DELEGATE] @agent-explore: 'Scan codebase for {domain}. Find: files, patterns, dependencies, SIMILAR existing implementations. Return: paths, architecture notes, analogous code to reference.' → STORE-AS($CODEBASE_CONTEXT)
→ END-IF
  IF(unknown library/pattern) →
  mcp__context7__query-docs('{query: "{library}"}') → understand before formulating
→ END-IF
→ END-IF
- `6`: IF(duplicate found) → STOP. Ask: update existing or create new?
- `7`: mcp__sequential-thinking__sequentialthinking({
                thought: "Analyzing: IF DOCS exist → extract full requirements from DOCS. Complexity, estimate (based on DOCS), priority, dependencies, acceptance criteria (from DOCS).",
                thoughtNumber: 1,
                totalThoughts: 2,
                nextThoughtNeeded: true
            })
- `8`: STORE-AS($ANALYSIS = {complexity, estimate, priority, dependencies, criteria, doc_requirements})
- `9`: STORE-AS($TASK_SPEC = {
                title: "concise, max 10 words",
                content: "objective, context, acceptance criteria, hints. FILES: [files from codebase exploration, or \\"to be determined\\" if new feature]. IF DOCS exist: See documentation: {doc_paths}. IF SIMILAR code: Reference: {similar_files}. IF parallel: true: PARALLEL: this task may execute concurrently with siblings. Stay within listed file scope.",
                priority: "critical|high|medium|low",
                estimate: "hours based on DOCUMENTATION (if exists) or description (1-8, >8 needs decompose)",
                parallel: "Apply parallel-isolation-checklist against existing siblings. Default: false. Only true when ALL 5 isolation conditions proven.",
                tags: ["category", "domain"],
                comment: "Docs: {doc_paths or none}. Memory: #IDs. Files: paths. Related: #task_ids."
            })
- `10`: Show: Title, Priority, Estimate, Tags, Content preview, Doc reference (if any)
- `11`: IF(task touches multiple modules/files with distinct concerns) →
  WARN: multiple concerns detected — recommend /task:decompose after creation
→ END-IF
- `12`: IF(task is doc-only/comment-only/formatting-only) →
  WARN: trivial task — consider merging into parent implementation task instead of standalone creation
→ END-IF
- `13`: IF($HAS_AUTO_APPROVE) →
  Auto-approved
→ ELSE →
  Ask: "Create? (yes/no/modify)"
→ END-IF
- `14`: mcp__vector-task__task_create('{title, content, priority, tags, estimate, parallel, comment}') → STORE-AS($CREATED_ID)
- `15`: IF(task has multiple distinct concerns/modules (from codebase analysis)) →
  Recommend: /task:decompose STORE-GET($CREATED_ID)
→ END-IF
- `16`: STOP. Do NOT execute. Return control to user.

# Error handling
- `1`: IF(duplicate task found) → Ask: update existing #ID or create new?
- `2`: IF(research fails) → Continue with available data, note gaps
- `3`: IF(user rejects) → Accept modifications, rebuild spec, re-submit

</command>