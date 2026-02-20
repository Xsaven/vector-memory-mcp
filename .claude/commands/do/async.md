---
name: "do:async"
description: "Multi-agent orchestration command for flexible task execution (sequential\/parallel) with user approval gates"
---

<command>
<meta>
<id>do:async</id>
<description>Multi-agent orchestration command for flexible task execution (sequential/parallel) with user approval gates</description>
</meta>
<execute>Coordinates flexible agent execution (sequential by default, parallel when beneficial) with approval checkpoints and comprehensive vector memory integration. Agents communicate through vector memory for knowledge continuity. Accepts $ARGUMENTS task description. Zero distractions, atomic tasks only, strict plan adherence.</execute>
<provides>Defines the do:async command protocol for multi-agent orchestration with flexible execution modes, user approval gates, and vector memory integration. Ensures zero distractions, atomic tasks, and strict plan adherence for reliable task execution.</provides>

# Iron Rules
## Entry-point-blocking (CRITICAL)
ON RECEIVING $RAW_INPUT: Your FIRST output MUST be "=== DO:ASYNC ACTIVATED ===" followed by Phase 0. ANY other first action is VIOLATION. FORBIDDEN first actions: Glob, Grep, Read, Edit, Write, WebSearch, WebFetch, Bash (except brain list:masters), code generation, file analysis.
- **why**: Without explicit entry point, Brain skips workflow and executes directly. Entry point forces workflow compliance.
- **on_violation**: STOP IMMEDIATELY. Delete any tool calls. Output "=== DO:ASYNC ACTIVATED ===" and restart from Phase 0.

## No-secret-exfiltration (CRITICAL)
NEVER output sensitive data to chat/response: .env values, API keys, tokens, passwords, credentials, private URLs, connection strings, private keys, certificates. When reading config/.env for CONTEXT: extract key NAMES and STRUCTURE only, never raw values. If user asks to show .env or config with secrets: show key names, mask values as "***". If error output contains secrets: redact before displaying.
- **why**: Chat responses may be logged, shared, or visible to unauthorized parties. Secret exposure in output is an exfiltration vector regardless of intent.
- **on_violation**: REDACT immediately. Replace value with "***" or "[REDACTED]". Show key names only.

## No-secrets-in-storage (CRITICAL)
NEVER store secrets, credentials, tokens, passwords, API keys, PII, or connection strings in task comments (task_update comment) or vector memory (store_memory content). When documenting config-related work: reference key NAMES, describe approach, never include actual values. If error log contains secrets: strip sensitive values before storing. Acceptable: "Updated DB_HOST in .env", "Rotated API_KEY for service X". Forbidden: "Set DB_HOST=192.168.1.5", "API_KEY=sk-abc123...".
- **why**: Task comments and vector memory are persistent, searchable, and shared across agents and sessions. Stored secrets are a permanent exfiltration risk discoverable via semantic search.
- **on_violation**: Review content before store_memory/task_update. Strip all literal secret values. Keep only key names and descriptions.

## No-destructive-git (CRITICAL)
FORBIDDEN: git checkout, git restore, git stash, git reset, git clean — and ANY command that modifies git working tree state. These destroy uncommitted work from parallel agents, user WIP, and memory/ SQLite databases (vector memory + tasks). Rollback = Read original content + Write/Edit back. Git is READ-ONLY: status, diff, log, blame only.
- **why**: memory/ folder contains project SQLite databases tracked in git. git checkout/stash/reset reverts these databases, destroying ALL tasks and memories. Parallel agents have uncommitted changes — any working tree modification wipes their work. Unrecoverable data loss.
- **on_violation**: ABORT git command. Use Read to get original content, Write/Edit to restore specific files. Never touch git working tree state.

## No-destructive-git-in-agents (CRITICAL)
When delegating to agents: ALWAYS include in prompt: "FORBIDDEN: git checkout, git restore, git stash, git reset, git clean. Rollback = Read + Write. Git is READ-ONLY."
- **why**: Sub-agents do not inherit parent rules. Without explicit prohibition, agents will use git for rollback and destroy parallel work.
- **on_violation**: Add git prohibition to agent prompt before delegation.

## Memory-folder-sacred (CRITICAL)
memory/ folder contains SQLite databases (vector memory + tasks). SACRED — protect at ALL times. NEVER git checkout/restore/reset/clean memory/ — these DESTROY all project knowledge irreversibly. In PARALLEL CONTEXT: use "git add {specific_files}" (task-scope only) — memory/ excluded implicitly because it is not in task files. In NON-PARALLEL context: "git add -A" is safe and DESIRED — includes memory/ for full state checkpoint preserving knowledge base alongside code.
- **why**: memory/ is the project persistent brain. Destructive git commands on memory/ = total knowledge loss. In parallel mode, concurrent SQLite writes + git add -A = binary merge conflicts and staged half-done sibling work. In sequential mode, committing memory/ preserves full project state for safe revert.
- **on_violation**: NEVER destructive git on memory/. Parallel: git add specific files only (memory/ not in scope). Non-parallel: git add -A (full checkpoint with memory/).

## Task-tags-predefined-only (CRITICAL)
Task tags MUST use ONLY predefined values. FORBIDDEN: inventing new tags, synonyms, variations. Allowed: decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression, feature, bugfix, refactor, research, docs, test, chore, spike, hotfix, backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration, strict:relaxed, strict:standard, strict:strict, strict:paranoid, cognitive:minimal, cognitive:standard, cognitive:deep, cognitive:exhaustive, batch:trivial.
SCENARIO(Project with 30 modules needs per-module filtering → use CUSTOM_TASK_TAGS in .env for project-specific tags, not 30 new constants in core.)
SCENARIO(Task about "user login flow" → tag: auth (NOT: login, authentication, user-auth). MCP normalizes at storage, but use canonical form at reasoning time.)
- **why**: Ad-hoc tags cause tag explosion ("user-auth", "authentication", "auth" = same concept, search finds none). Predefined list = consistent search. MCP normalizes aliases at storage layer, but reasoning-time canonical usage prevents drift.
- **on_violation**: Normalize via NOT-list (e.g. authentication→auth, db→database). No canonical match → skip tag, put context in task content. Silent fix, no memory storage.

## Memory-tags-predefined-only (CRITICAL)
Memory tags MUST use ONLY predefined values. Allowed: pattern, solution, `failure`, decision, insight, workaround, deprecated, project-wide, module-specific, temporary, reusable.
- **why**: Unknown tags = unsearchable memories. Predefined = discoverable. MCP normalizes at storage, but use canonical form at reasoning time.
- **on_violation**: Normalize to closest canonical tag. No match → skip tag.

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
SCENARIO(Task tagged strict:relaxed touches auth/guards/LoginController.php → escalate to strict:strict minimum regardless of tag.)
SCENARIO(Simple rename across 12 files → cognitive escalates to standard (>10 files rule), strict stays as tagged.)
- **why**: Safety patterns guarantee minimum protection for critical code areas. Agent cannot "cheat" by under-tagging a task touching auth/ or payments/.
- **on_violation**: Raise effective level to safety floor. Log escalation in task comment.

## Failure-policy-tool-error (CRITICAL)
TOOL ERROR / MCP FAILURE: 1) Retry ONCE with same parameters. 2) Still fails → STOP current step. 3) Store `failure` to memory (category: "debugging", tags: ["failure"]). 4) Update task comment: "BLOCKED: {tool} failed after retry. Error: {msg}", append_comment: true. 5) -y mode: set status "pending" (return to queue for retry), abort current workflow. Interactive: ask user "Tool failed. Retry/Skip/Abort?". NEVER set "stopped" on `failure` — "stopped" = permanently cancelled.
- **why**: Consistent tool `failure` handling across all commands. One retry catches transient issues. Failed task returns to `pending` queue — it is NOT cancelled, just needs another attempt or manual intervention.
- **on_violation**: Follow 5-step sequence. Max 1 retry for same tool call. Always store `failure` to memory. Status → `pending`, NEVER `stopped`.

## Failure-policy-missing-docs (HIGH)
MISSING DOCS: 1) Apply aggressive-docs-search (3+ keyword variations). 2) All variations exhausted → conclude "no docs". 3) Proceed using: task.content (primary spec) + vector memory context + parent task context. 4) Log in task comment: "No documentation found after {N} search attempts. Proceeding with task.content.", append_comment: true. NOT a blocker — absence of docs is information, not `failure`.
- **why**: Missing docs must not block execution. task.content is the minimum viable specification. Blocking on missing docs causes pipeline stalls for tasks that never had docs.
- **on_violation**: Never block on missing docs. Search aggressively, then proceed with available context.

## Failure-policy-ambiguous-spec (HIGH)
AMBIGUOUS SPEC: 1) Identify SPECIFIC ambiguity (not "task is unclear" but "field X: type A or B?"). 2) -y mode: choose conservative/safe interpretation, log decision in task comment: "DECISION: interpreted {X} as {Y} because {reason}", append_comment: true. 3) Interactive: ask ONE targeted question about the SPECIFIC gap. 4) After 1 clarification → proceed. NEVER ask open-ended "what did you mean?" or multiple follow-ups.
SCENARIO(Task says "add validation". Client-side, server-side, or both? → In -y mode: choose server-side (conservative, safer). In interactive: ask ONE question about this specific gap.)
- **why**: Ambiguity paralysis wastes more time than conservative interpretation. One precise question is enough — if user wanted detailed spec, they would have written docs.
- **on_violation**: Identify specific gap. One question or auto-decide. Proceed.

## Docs-are-law (CRITICAL)
Documentation is the SINGLE SOURCE OF TRUTH. If docs exist for task - FOLLOW THEM EXACTLY. No deviations, no "alternatives", no "options" that docs don't mention.
SCENARIO(Docs say "use Repository pattern". Existing code uses Service pattern. → Follow docs (Repository), not existing code.)
SCENARIO(Docs describe feature but skip error handling details. → Follow docs for main flow, use conservative approach for undocumented edge cases.)
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

## Zero-distractions (CRITICAL)
ZERO distractions - implement ONLY specified task from $TASK_DESCRIPTION. NO creative additions, NO unapproved features, NO scope creep.
- **why**: Ensures focused execution and prevents feature drift
- **on_violation**: Abort immediately. Return to approved plan.

## Scope-escalation (CRITICAL)
If task analysis reveals: estimated effort >8h OR >5 files affected OR requires multi-session execution OR >4 distinct sub-steps → ESCALATE to Task workflow. Create vector task via VectorTaskMcp with tag "manual-only" (prevents auto-execution). Suggest user switch to /task:async or /task:sync with created task ID. ABORT do command — task is too large for single-shot execution.
- **why**: Do commands are lightweight single-shot executors. Complex tasks need vector task tracking for state persistence, parallel execution, validation pipeline, and circuit breaker protection. Escalation prevents half-done work in a single context window.
- **on_violation**: Create vector task with TAG_MANUAL_ONLY. Report task ID. Suggest /task:async or /task:sync. ABORT.

## Do-circuit-breaker (CRITICAL)
MAX 3 retry attempts per step within single do:async session. Track via $RETRY_COUNTS[step_id]. Step fails 3x → store `failure` to memory (category: "debugging", tags: ["failure"]), then: -y mode = skip step and continue, interactive mode = ask user "Skip / Abort?". NEVER retry same step more than 3 times.
- **why**: Without retry limit, failed steps create infinite loops especially in auto-approve mode. Do commands have no cross-session state (no vector task comments), so circuit breaker is session-scoped.
- **on_violation**: Check $RETRY_COUNTS before retry. 3x reached → store `failure`, skip or abort.

## Do-failure-awareness (CRITICAL)
BEFORE starting work: search memory category "debugging" for KNOWN FAILURES related to $TASK_DESCRIPTION. Found → extract failed approaches and BLOCK them. Pass blocked approaches to agents (async) or exclude from plan (sync). Do NOT attempt solutions that already failed.
- **why**: Repeating failed solutions wastes time and context. Memory contains "this does NOT work" knowledge from previous sessions.
- **on_violation**: Search debugging memories FIRST. Block known-failed approaches in plan/delegation.

## Do-machine-readable-progress (HIGH)
ALL progress output MUST follow structured format. DURING EXECUTION: emit "STATUS: [phase_name] description" at each major workflow phase. AT COMPLETION: emit "RESULT: SUCCESS|PARTIAL|FAILED|PASSED|NEEDS_WORK — key=value, key=value" followed by "NEXT: recommended_command". No free-form progress — only STATUS/RESULT/NEXT lines. Examples: "STATUS: [context] Analyzing task scope" | "STATUS: [execution] Step 3/5 complete" | "RESULT: SUCCESS — steps=5/5, files=3" | "NEXT: /do:validate {description}".
- **why**: Structured format enables UI rendering, orchestrator parsing, and consistent user experience. Matches Task command output contract for uniform tooling.
- **on_violation**: Reformat to STATUS/RESULT/NEXT structure. Replace free-form text with structured lines.

## Approval-gates-mandatory (CRITICAL)
User approval REQUIRED at Requirements Analysis gate and Execution Planning gate. NEVER proceed without explicit confirmation. EXCEPTION: If $HAS_AUTO_APPROVE is true, auto-approve all gates (skip waiting for user confirmation).
- **why**: Maintains user control and prevents unauthorized execution. The -y flag enables unattended/scripted execution.
- **on_violation**: STOP. Wait for user approval before continuing (unless $HAS_AUTO_APPROVE is true).

## Atomic-tasks-only (CRITICAL)
Each agent task MUST be small and focused: maximum 1-2 files per agent invocation. NO large multi-file changes.
- **why**: Prevents complexity, improves reliability, enables precise tracking
- **on_violation**: Break task into smaller pieces. Re-plan with atomic steps.

## No-improvisation (CRITICAL)
Execute ONLY approved plan steps. NO improvisation, NO "while we're here" additions, NO proactive suggestions during execution.
- **why**: Maintains plan integrity and predictability
- **on_violation**: Revert to last approved checkpoint. Resume approved steps only.

## Execution-mode-flexible (HIGH)
Execute agents sequentially BY DEFAULT. Allow parallel execution when: 1) tasks are independent (no file/context conflicts), 2) user explicitly requests parallel mode, 3) optimization benefits outweigh tracking complexity.
- **why**: Balances safety with performance optimization
- **on_violation**: Validate task independence before parallel execution. Fallback to sequential if conflicts detected.

## Vector-memory-mandatory (HIGH)
ALL agents MUST search vector memory BEFORE task execution AND store learnings AFTER completion. Vector memory is the primary communication channel between sequential agents.
- **why**: Enables knowledge sharing between agents, prevents duplicate work, maintains execution continuity across steps
- **on_violation**: Include explicit vector memory instructions in agent Task() delegation.

## Conversation-context-awareness (HIGH)
ALWAYS analyze conversation context BEFORE planning. User may have discussed requirements, constraints, preferences, or decisions in previous messages.
- **why**: Prevents ignoring critical information already provided by user in conversation
- **on_violation**: Review conversation history before proceeding with task analysis.

## Full-workflow-mandatory (CRITICAL)
ALL requests MUST follow complete workflow: Phase 0 (Context) → Phase 1 (Discovery) → Phase 2 (Requirements + APPROVAL) → Phase 3 (Gathering) → Phase 4 (Planning + APPROVAL) → Phase 5 (Execution via agents) → Phase 6 (Completion). NEVER skip phases. NEVER execute directly without agent delegation.
- **why**: Workflow ensures quality, user control, and proper orchestration. Skipping phases leads to poor results, missed context, and violated user trust.
- **on_violation**: STOP. Return to Phase 0. Follow workflow sequentially. Present approval gates. Delegate via Task().

## Never-execute-directly (CRITICAL)
Brain NEVER executes implementation tasks directly. For ANY $TASK_DESCRIPTION: MUST delegate to agents via Task(). Brain only: analyzes, plans, presents approvals, delegates, validates results.
- **why**: Direct execution violates orchestration model, bypasses agent expertise, wastes Brain tokens on execution instead of coordination.
- **on_violation**: STOP. Identify required agent from brain list:masters. Delegate via Task(@agent-name, task).

## No-direct-file-tools (CRITICAL)
FORBIDDEN: Brain NEVER calls Glob, Grep, Read, Edit, Write directly. ALL file operations MUST be delegated to agents via Task().
- **why**: Direct tool calls are expensive, bypass agent expertise, and violate orchestration model. Each file operation costs tokens that agents handle more efficiently.
- **on_violation**: STOP. Remove direct tool call. Delegate to appropriate agent: ExploreMaster (search/read), code agents (edit/write).

## Orchestration-only (CRITICAL)
Brain role is ORCHESTRATION ONLY. Permitted: Task(), vector MCP, brain CLI (docs, list:masters). Everything else → delegate.
- **why**: Brain is conductor, not musician. Agents execute, Brain coordinates.
- **on_violation**: Identify task type → Select agent → Delegate via Task().

## One-agent-one-file (CRITICAL)
Each programming subtask = separate agent invocation. One agent, one file change. NO multi-file edits in single delegation.
- **why**: Atomic changes enable precise tracking, easier rollback, clear accountability.
- **on_violation**: Split into multiple Task() calls. One agent per file modification.


# Task tag selection
GOAL(Select tags per task. Combine dimensions for precision.)
WORKFLOW (pipeline stage): decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression
TYPE (work kind): feature (NOT: feat, enhancement), bugfix (NOT: fix, bug), refactor (NOT: refactoring, cleanup), research, docs (NOT: documentation), test (NOT: testing, tests), chore (NOT: maintenance), spike, hotfix
DOMAIN (area): backend, frontend, database (NOT: db, mysql, postgres, sqlite), api (NOT: rest, graphql, endpoint), auth (NOT: authentication, authorization, login, authn, authz), ui, config, infra (NOT: docker, deploy, server), ci-cd (NOT: github-actions, pipeline), migration (NOT: schema, migrate)
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
GOAL(Cognitive level: exhaustive — calibrate analysis depth accordingly)
Memory probes per phase: 5+ cross-referenced
Failure history: full + pattern analysis
Research (context7/web): always + cross-reference
Agent scaling: maximum (4+)
Comment parsing: parse + validate

# Aggressive docs search

GOAL(Find documentation even if named differently than task/code)
GOAL(Find documentation even if named differently than task/code)

- `1`: Generate 3-5 keyword variations: split CamelCase, strip suffixes (Test, Controller, Service, Repository, Handler), extract domain words, try parent context keywords
- `2`: Search ORDER: most specific → most general. Minimum 3 attempts before concluding "no docs"
- `3`: WRONG: brain docs "UserAuthServiceTest" → not found → done
- `4`: RIGHT: brain docs "UserAuthServiceTest" → brain docs "UserAuth" → brain docs "Authentication" → FOUND!
- `5`: STILL not found after 3+ attempts? → brain docs --undocumented → check if class exists but lacks documentation

# Scope escalation
GOAL(Detect oversized tasks and escalate to Task workflow with vector task tracking)
- `1`: Escalation triggers (ANY = escalate):
- `2`:   1. Estimated effort >8 hours
- `3`:   2. >5 files need modification
- `4`:   3. Task requires multiple sessions (cannot complete in one context window)
- `5`:   4. >4 distinct sub-steps that each require their own analysis
- `6`: IF(any trigger matched) →
  mcp__vector-task__task_create('{title: "$TASK_DESCRIPTION", content: "Escalated from /do:async. Original task too large for single-shot execution. Triggers: {matched_triggers}.", priority: "medium", estimate: {estimated_hours}, tags: ["manual-only"]}')
  STORE-AS($ESCALATED_TASK_ID = {created task ID})
  OUTPUT( === SCOPE ESCALATION === Task exceeds do:async capacity: {matched_triggers}. Created vector task #{$ESCALATED_TASK_ID} (tagged manual-only). NEXT: /task:async #{$ESCALATED_TASK_ID} [-y] or /task:sync #{$ESCALATED_TASK_ID} [-y])
  ABORT do command
→ END-IF

# Do circuit breaker
GOAL(Break retry loops within do:async session)
- `1`: 1. STORE-AS($RETRY_COUNTS = {} (empty map, keyed by step_id))
- `2`: 2. On step `failure`: increment $RETRY_COUNTS[step_id]
- `3`: 3. IF($RETRY_COUNTS[step_id] >= 3) →
  mcp__vector-memory__store_memory('{content: "FAILED: Step {step_id} in do:async failed 3x. Task: {$TASK_DESCRIPTION}. Error: {last_error}. Context: {step_context}.", category: "debugging", tags: ["failure"]}')
  IF($HAS_AUTO_APPROVE === true) → SKIP step, continue to next
  IF($HAS_AUTO_APPROVE === false) →
  Ask user: "Step failed 3x. Skip / Abort?"
→ END-IF
→ END-IF
- `4`: 4. IF($RETRY_COUNTS[step_id] < 3) → Retry step with adjusted approach

# Do failure awareness
GOAL(Mine failure history before execution to avoid repeating mistakes)
- `1`: mcp__vector-memory__search_memories('{query: "$TASK_DESCRIPTION failure", limit: 5, category: "debugging"}')
- `2`: STORE-AS($KNOWN_FAILURES = {failed approaches, errors, blocked patterns})
- `3`: IF($KNOWN_FAILURES not empty) →
  STORE-AS($BLOCKED_APPROACHES = {extracted approaches that MUST NOT be attempted})
  OUTPUT(Known failures found: {$KNOWN_FAILURES.count}. Blocked approaches: {$BLOCKED_APPROACHES})
→ END-IF
- `4`: IF($KNOWN_FAILURES empty) →
  STORE-AS($BLOCKED_APPROACHES = [])
  No known failures — proceed freely.
→ END-IF

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($HAS_AUTO_APPROVE = {true if $RAW_INPUT contains "-y" or "--yes"})
STORE-AS($CLEAN_ARGS = {$RAW_INPUT with -y/--yes flags removed})
STORE-AS($TASK_DESCRIPTION = {task description from $CLEAN_ARGS})

# Phase0 context analysis
GOAL(Extract task insights from conversation history before planning)
- `1`: OUTPUT(=== DO:ASYNC ACTIVATED ===  === PHASE 0: CONTEXT ANALYSIS === Task: {$TASK_DESCRIPTION} Analyzing conversation context...)
- `2`: Analyze conversation context: requirements mentioned, constraints discussed, user preferences, prior decisions, related code/files referenced
- `3`: STORE-AS($CONVERSATION_CONTEXT = {requirements, constraints, preferences, decisions, references})
- `4`: IF(conversation has relevant context) →
  Integrate context into task understanding
  Note: Use conversation insights throughout all phases
→ END-IF
- `5`: OUTPUT(Context: {summary of relevant conversation info})

# Phase1 agent discovery
GOAL(Discover agents leveraging conversation context + vector memory)
- `1`: mcp__vector-memory__search_memories('{query: "similar: {$TASK_DESCRIPTION}", limit: 5, category: "code-solution,architecture"}')
- `2`: STORE-AS($PAST_SOLUTIONS = Past approaches)
- `3`: Bash(brain list:masters) → [brain list:masters] → END-Bash
- `4`: STORE-AS($AVAILABLE_AGENTS = Agents list)
- `5`: Match task to agents: $TASK_DESCRIPTION + $CONVERSATION_CONTEXT + $PAST_SOLUTIONS
- `6`: STORE-AS($RELEVANT_AGENTS = [{agent, capability, rationale}, ...])
- `7`: OUTPUT(=== PHASE 1: AGENT DISCOVERY === Agents: {selected} | Context: {conversation insights applied})

# Phase2 requirements analysis approval
GOAL(Create requirements plan leveraging conversation + memory + GET USER APPROVAL + START TASK)
- `1`: mcp__vector-memory__search_memories('{query: "patterns: {task_domain}", limit: 5, category: "learning,architecture"}')
- `2`: STORE-AS($IMPLEMENTATION_PATTERNS = Past patterns)
- `3`: Analyze: $TASK_DESCRIPTION + $CONVERSATION_CONTEXT + $PAST_SOLUTIONS + $IMPLEMENTATION_PATTERNS
- `4`: Determine needs: scan targets, web research (if non-trivial), docs scan (if architecture-related)
- `5`: STORE-AS($REQUIREMENTS_PLAN = {scan_targets, web_research, docs_scan, conversation_insights, memory_learnings})
- `6`: OUTPUT( === PHASE 2: REQUIREMENTS ANALYSIS === Context: {conversation insights} | Memory: {key learnings} Scanning: {targets} | Research: {status} | Docs: {status}  ⚠️  APPROVAL CHECKPOINT #1 ✅ approved/yes | ❌ no/modifications)
- `7`: IF($HAS_AUTO_APPROVE === true) →
  AUTO-APPROVED (unattended mode)
  OUTPUT(🤖 Auto-approved via -y flag)
→ END-IF
- `8`: IF($HAS_AUTO_APPROVE === false) →
  WAIT for user approval
  VERIFY-SUCCESS(User approved)
  IF(rejected) → Modify plan → Re-present → WAIT
→ END-IF

# Phase3 material gathering
GOAL(Collect materials via agents. Brain permitted: brain docs (index only, few tokens). ALL file reading → delegate to agents.)
- `1`: FOREACH(scan_target in $REQUIREMENTS_PLAN.scan_targets) →
  [DELEGATE] @agent-explore: 'Extract context from {scan_target}. Store findings to vector memory.'
  STORE-AS($GATHERED_MATERIALS[{TARGET}] = Agent-extracted context)
→ END-FOREACH
- `2`: IF($DOCS_SCAN_NEEDED === true) →
  Bash(brain docs {keywords}) → [Get documentation INDEX only (Path, Name, Description)] → END-Bash
  STORE-AS($DOCS_INDEX = Documentation file paths)
  [DELEGATE] @agent-explore: 'Read and summarize documentation files: {$DOCS_INDEX paths}. Store to vector memory.'
  STORE-AS($DOCS_SCAN_FINDINGS = Agent-summarized documentation)
→ END-IF
- `3`: IF($WEB_RESEARCH_NEEDED === true) →
  [DELEGATE] @agent-web-research-master: 'Research best practices for {$TASK_DESCRIPTION}. Store findings to vector memory.'
  STORE-AS($WEB_RESEARCH_FINDINGS = External knowledge)
→ END-IF
- `4`: STORE-AS($CONTEXT_PACKAGES = {agent_name: {context, materials, task_domain}, ...})
- `5`: mcp__vector-memory__store_memory('{content: "Context for {$TASK_DESCRIPTION}\\\\n\\\\nMaterials: {summary}", category: "code-solution", tags: ["solution", "reusable"]}')
- `6`: OUTPUT(=== PHASE 3: MATERIALS GATHERED === Materials: {count} | Docs: {status} | Web: {status} Context stored to vector memory ✓)

# Phase4 execution planning approval
GOAL(Create atomic plan leveraging past execution patterns, analyze dependencies, and GET USER APPROVAL)
- `1`: mcp__vector-memory__search_memories('{query: "execution approach for {task_type}", limit: 5, category: "code-solution"}')
- `2`: STORE-AS($EXECUTION_PATTERNS = Past successful execution approaches)
- `3`: mcp__sequential-thinking__sequentialthinking({
                thought: "Planning agent delegation. Analyzing: task decomposition, agent selection, step dependencies, parallelization opportunities, file scope per step.",
                thoughtNumber: 1,
                totalThoughts: 3,
                nextThoughtNeeded: true
            })
- `4`: Create plan: atomic steps (≤2 files each), logical order, informed by $EXECUTION_PATTERNS
- `5`: Analyze step dependencies: file conflicts, context dependencies, data flow
- `6`: Determine execution mode: sequential (default/safe) OR parallel (independent tasks/user request/optimization)
- `7`: IF(parallel possible AND beneficial) →
  Group independent steps into parallel batches
  Validate NO conflicts: 1) File: same file in multiple steps, 2) Context: step B needs output of step A, 3) Resource: same API/DB/external
  STORE-AS($EXECUTION_MODE = parallel)
  STORE-AS($PARALLEL_GROUPS = [[step1, step2], [step3], ...])
→ END-IF
- `8`: IF(NOT parallel OR dependencies detected) →
  STORE-AS($EXECUTION_MODE = sequential)
→ END-IF
- `9`: STORE-AS($EXECUTION_PLAN = {steps: [{step_number, agent_name, task_description, file_scope: [≤2 files], memory_search_query, expected_outcome}, ...], total_steps: N, execution_mode: "sequential|parallel", parallel_groups: [...]})
- `10`: VERIFY-SUCCESS(Each step has ≤ 2 files)
- `11`: VERIFY-SUCCESS(Parallel groups have NO conflicts)
- `12`: OUTPUT( === PHASE 4: EXECUTION PLAN === Task: {$TASK_DESCRIPTION} | Steps: {N} | Mode: {execution_mode} Learned from: {$EXECUTION_PATTERNS summary}  {Step-by-step breakdown with files and memory search queries} {If parallel: show grouped batches}  ⚠️  APPROVAL CHECKPOINT #2 ✅ Type "approved" or "yes" to begin. ❌ Type "no" or provide modifications.)
- `13`: IF($HAS_AUTO_APPROVE === true) →
  AUTO-APPROVED (unattended mode)
  OUTPUT(🤖 Auto-approved via -y flag)
→ END-IF
- `14`: IF($HAS_AUTO_APPROVE === false) →
  WAIT for user approval
  VERIFY-SUCCESS(User confirmed approval)
  IF(user rejected) →
  Accept modifications → Update plan → Verify atomic + dependencies → Re-present → WAIT
→ END-IF
→ END-IF

# Phase5 flexible execution
GOAL(Execute plan with optimal mode (sequential OR parallel))
- `1`: Initialize: current_step = 1
- `2`: IF($EXECUTION_PLAN.execution_mode === "sequential") →
  SEQUENTIAL MODE: Execute steps one-by-one
  FOREACH(step in $EXECUTION_PLAN.steps) →
  OUTPUT(▶️ Step {N}/{total}: @agent-{step.agent_name} | 📁 {step.file_scope})
  Delegate via Task() with agent-memory-pattern (BEFORE→DURING→AFTER)
  Task(Task(@agent-{name}, {task + memory_search_query + context}))
  STORE-AS($STEP_RESULTS[{N}] = Result)
  OUTPUT(✅ Step {N} complete)
→ END-FOREACH
→ END-IF
- `3`: IF($EXECUTION_PLAN.execution_mode === "parallel") →
  PARALLEL MODE: Execute independent steps concurrently
  FOREACH(group in $EXECUTION_PLAN.parallel_groups) →
  OUTPUT(🚀 Batch {N}: {count} steps)
  Launch ALL steps CONCURRENTLY via multiple Task() calls
  Each task follows agent-memory-pattern
  WAIT for ALL tasks in batch to complete
  STORE-AS($BATCH_RESULTS[{N}] = Batch results)
  OUTPUT(✅ Batch {N} complete)
→ END-FOREACH
→ END-IF
- `4`: IF(step fails) →
  Store `failure` to memory
  Offer: Retry / Skip / Abort
→ END-IF

# Phase6 completion report
GOAL(Report results and store comprehensive learnings to vector memory)
- `1`: STORE-AS($COMPLETION_SUMMARY = {completed_steps, files_modified, outcomes, learnings})
- `2`: mcp__vector-memory__store_memory('{content: "Completed: {$TASK_DESCRIPTION}\\\\n\\\\nApproach: {summary}\\\\n\\\\nSteps: {outcomes}\\\\n\\\\nLearnings: {insights}\\\\n\\\\nFiles: {list}", category: "code-solution", tags: ["solution", "reusable"]}')
- `3`: OUTPUT( === EXECUTION COMPLETE === Task: {$TASK_DESCRIPTION} | Status: {SUCCESS/PARTIAL/FAILED} ✓ Steps: {`completed`}/{total} | 📁 Files: {count} | 💾 Learnings stored to memory {step_outcomes}  RESULT: {SUCCESS|PARTIAL|FAILED} — steps={`completed`}/{total}, files={count} NEXT: /do:validate {$TASK_DESCRIPTION})
- `4`: IF(partial) →
  Store partial state → List remaining → Suggest resumption
→ END-IF

# Agent instruction template
Every Task() delegation MUST include these sections:
1. TASK: Clear description of what to do
2. FILES: Specific file scope (1-2 files, max 3-5 for feature)
3. DOCUMENTATION: "If docs exist: {$DOCS_SCAN_FINDINGS}. Documentation = COMPLETE spec. Follow DOCS."
4. BLOCKED APPROACHES: "KNOWN FAILURES (DO NOT USE): {$BLOCKED_APPROACHES}. If your solution matches — find alternative."
5. MEMORY BEFORE: "Search memory for: {terms}. Check debugging category for failures."
6. MEMORY AFTER: "Store learnings: what worked, approach used, key insights. Category: code-solution, tags: [solution, reusable]."
7. SECURITY: "No hardcoded secrets. Validate input. Escape output. Parameterized queries."
8. VALIDATION: "Verify syntax. Run linter if configured. Check logic: null, empty, boundary, off-by-one, error paths. Check performance: N+1, nested loops, unbounded data. Run ONLY related tests (scoped, never full suite). Fix before completion."
9. GIT: "FORBIDDEN: git checkout, git restore, git stash, git reset, git clean. These destroy parallel agents work and memory/ databases. Rollback = Read original content + Write back. Git is READ-ONLY (status, diff, log)."
10. PATTERNS: "BEFORE coding: search codebase for similar implementations. Grep analogous class names, method patterns. Found → follow same approach, reuse helpers. NEVER reinvent existing patterns."
11. IMPACT: "BEFORE editing: Grep who imports/uses/extends target file. Dependents found → ensure changes are compatible. Changing public API → update all callers."
12. HALLUCINATION: "Verify EVERY method/class/function call exists with correct signature. Read source to confirm. NEVER assume API from naming convention."
13. CLEANUP: "After edits: remove unused imports, dead code, orphaned helpers, commented-out blocks."
14. TESTS: "After implementation: check if changed code has tests. NO tests → WRITE them. Insufficient coverage → ADD tests. Target: >=80% coverage, critical paths 100%, meaningful assertions, edge cases (null, empty, boundary). Detect test framework, follow existing test patterns. Run written tests to verify passing."
15. DOCS: "After implementation: IF task adds NEW feature/module/API → run brain docs \\"{keywords}\\" to check existing docs. NOT found → CREATE .docs/{feature}.md with YAML front matter + markdown body. Documentation = description for humans, text-first, minimize code. IF task CHANGES existing behavior and docs exist → UPDATE relevant docs. Bugfix/refactor → SKIP docs."

# Error recovery
Graceful error handling with recovery options
- `1`: IF(user rejects plan) →
  Accept modifications
  Rebuild plan
  Re-submit for approval
→ END-IF
- `2`: IF(no agents available) →
  Report: "No agents found via brain list:masters"
  Suggest: Run /init-agents first
  Abort command
→ END-IF
- `3`: IF(agent execution fails) →
  Log: "Step/Agent {N} failed: {error}"
  Offer options:
    1. Retry current step
    2. Skip and continue
    3. Abort remaining steps
  WAIT for user decision
→ END-IF
- `4`: IF(web research timeout) →
  Log: "Web research timed out - continuing without external knowledge"
  Proceed with local context only
→ END-IF
- `5`: IF(context gathering fails) →
  Log: "Failed to gather {context_type}"
  Proceed with available context
  Warn: "Limited context may affect quality"
→ END-IF
- `6`: IF(documentation scan fails) →
  Log: "brain docs command failed or no documentation found"
  Proceed without documentation context
→ END-IF
- `7`: IF(memory storage fails) →
  Log: "Failed to store to memory: {error}"
  Report findings in output instead
  Continue with report
→ END-IF

# Constraints validation
Enforcement of critical constraints throughout execution
- `1`: Before Requirements Analysis: Verify $TASK_DESCRIPTION is not empty
- `2`: Before Phase 2 → Phase 3 transition: Verify user approval received
- `3`: Before Phase 4 → Phase 5 transition: Verify user approval received
- `4`: During Execution Planning: Verify each step has ≤ 2 files in scope
- `5`: During Execution: Verify dependencies respected (sequential: step order, parallel: no conflicts)
- `6`: Throughout: NO unapproved steps allowed
- `7`: VERIFY-SUCCESS(approval_checkpoints_passed = 2 all_tasks_atomic = true (≤ 2 files each) execution_mode = sequential OR parallel (`validated`) improvisation_count = 0)

# Example simple
SCENARIO(Simple single-agent task)
- `input`: "Fix authentication bug in LoginController.php"
- `flow`: Context → Discovery → Requirements ✓ → Gather → Plan ✓ → Execute (1 step) → Complete

# Example sequential
SCENARIO(Complex multi-agent sequential task)
- `input`: "Add Laravel rate limiting to API endpoints"
- `agents`: @web-research-master, @code-master, @documentation-master
- `plan`: 4 steps: Middleware → Kernel → Routes → Docs
- `execution`: Sequential: 1→2→3→4 (dependencies between steps)
- `result`: 4/4 ✓

# Example parallel
SCENARIO(Parallel execution for independent tasks)
- `input`: "Add validation to UserController, ProductController, OrderController"
- `analysis`: 3 independent files, no conflicts
- `plan`: Mode: PARALLEL, Batch 1: [Step1, Step2, Step3]
- `execution`: Concurrent: 3 agents simultaneously
- `result`: 3/3 ✓ (faster than sequential)

# Response format
=== headers | approval gates | progress | file scope | No filler

</command>