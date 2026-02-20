---
name: "init-task"
description: "Initialize project tasks from documentation and codebase analysis"
---

<command>
<meta>
<id>init-task</id>
<description>Initialize project tasks from documentation and codebase analysis</description>
</meta>
<execute>Initializes project task hierarchy by scanning documentation (.docs/, README), analyzing codebase structure via Explore agent, decomposing work into root-level tasks with estimates, and creating tasks in vector storage after user approval. Ensures comprehensive project understanding before task creation.</execute>
<provides>Aggressive project task initializer with MAXIMUM parallel agent orchestration. Scans every project corner via specialized agents, creates comprehensive epic-level tasks. NEVER executes - only creates.</provides>

# Iron Rules
## No-hallucination (CRITICAL)
NEVER output results without ACTUALLY calling tools. You CANNOT know task status or content without REAL tool calls. Fake results = CRITICAL VIOLATION.

## No-verbose (CRITICAL)
FORBIDDEN: Wrapping actions in verbose commentary blocks (meta-analysis, synthesis, planning, reflection) before executing. Act FIRST, explain AFTER.

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

## Parallel-agent-execution (CRITICAL)
Launch INDEPENDENT research agents in PARALLEL (multiple Task calls in single response)
- **why**: Maximizes coverage, reduces total research time, comprehensive analysis
- **on_violation**: Group independent areas, launch ALL simultaneously

## Every-corner-coverage (CRITICAL)
MUST explore EVERY project area: code, tests, config, docs, build, migrations, routes, schemas
- **why**: First layer tasks define entire project. Missing areas = missing epics = incomplete planning
- **on_violation**: Add missing areas to parallel exploration batch

## Multi-agent-research (CRITICAL)
Use SPECIALIZED agents for each domain: ExploreMaster(code), DocumentationMaster(docs), VectorMaster(memory), WebResearchMaster(external)
- **why**: Each agent has domain expertise. Single agent cannot comprehensively analyze all areas.
- **on_violation**: Delegate to appropriate specialized agent

## Create-only-no-execution (CRITICAL)
This command ONLY creates root tasks. NEVER execute any task after creation.
- **why**: Init-task creates strategic foundation. Execution via /task:next or /do
- **on_violation**: STOP immediately after task creation

## Auto-approve-default (CRITICAL)
When $HAS_AUTO_APPROVE = false: MUST get explicit user YES/APPROVE/CONFIRM before creating ANY tasks. When $HAS_AUTO_APPROVE = true: auto-approve after presenting summary. Proceed autonomously through creation.
- **why**: User must validate task breakdown before committing. -y flag enables pipeline usage.
- **on_violation**: If interactive: present task list and wait for explicit confirmation. If auto-approve: show summary and proceed.

## Estimate-required (CRITICAL)
MUST provide time estimate (8-40h) for EACH epic
- **why**: Estimates enable planning and identify tasks needing decomposition
- **on_violation**: Add estimate before presenting epic

## Exclude-brain-directory (CRITICAL)
NEVER analyze .brain/ - Brain system internals, not project code
- **why**: Brain config pollutes task list with irrelevant system tasks
- **on_violation**: Skip .brain/ in all exploration phases


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
- `1`: Generate 3-5 keyword variations: split CamelCase, strip suffixes (Test, Controller, Service, Repository, Handler), extract domain words, try parent context keywords
- `2`: Search ORDER: most specific → most general. Minimum 3 attempts before concluding "no docs"
- `3`: WRONG: brain docs "UserAuthServiceTest" → not found → done
- `4`: RIGHT: brain docs "UserAuthServiceTest" → brain docs "UserAuth" → brain docs "Authentication" → FOUND!
- `5`: STILL not found after 3+ attempts? → brain docs --undocumented → check if class exists but lacks documentation

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($HAS_AUTO_APPROVE = {true if $RAW_INPUT contains "-y" or "--yes"})
STORE-AS($CLEAN_ARGS = {$RAW_INPUT with -y/--yes flags removed})
STORE-AS($INIT_SCOPE = {optional scope filter from $CLEAN_ARGS: specific area/focus for task generation, or empty for full project})

# Phase0 preflight
GOAL(Check existing state, determine mode)
- `1`: OUTPUT(=== INIT:TASK ACTIVATED ===  === PHASE 0: PRE-FLIGHT CHECKS === Checking task state...)
- `2`: 
STEP 1 - Check task state:
mcp__vector-task__task_stats('{}') STORE-AS($TASK_STATE = {total, `pending`, `in_progress`})

- `3`: 
STEP 2 - Decision:
IF($TASK_STATE.total === 0) → Fresh init → proceed IF($TASK_STATE.total > 0) →
  Ask: "Tasks exist. (1) Add more, (2) Clear & restart, (3) Abort"
→ END-IF


# Phase1 structure
GOAL(Quick structure scan to identify ALL areas for parallel exploration)
- `1`: Task(@agent-explore, TASK →
  QUICK STRUCTURE SCAN - identify directories only
  Glob("*") → list root directories and key files
  EXCLUDE: .brain/, vendor/, node_modules/, .git/
  IDENTIFY: code(src/app), tests, config, docs(.docs), migrations, routes, build, public
  Output JSON: {areas: [{path, type, estimated_files, priority}]}
→ END-TASK, 'OUTPUT({areas: [{path, type, estimated_files, priority: critical|high|medium|low}]})', 'STORE-AS($PROJECT_AREAS)')

# Phase2 parallel code
GOAL(PARALLEL: Launch code exploration agents simultaneously)
- `1`: 
BATCH 1 - Core Code Areas (LAUNCH IN PARALLEL):
Task(@agent-explore, TASK →
  Area: src/ or app/ (MAIN CODE)
  Thoroughness: very thorough
  ANALYZE: directory structure, namespaces, classes, design patterns
  IDENTIFY: entry points, core modules, service layers, models
  EXTRACT: {path|files_count|classes|namespaces|patterns|complexity}
  FOCUS ON: what needs to be built/refactored/improved
→ END-TASK, 'OUTPUT({path:"src",files:N,modules:[],patterns:[],tech_debt:[]})', 'STORE-AS($CODE_ANALYSIS)') Task(@agent-explore, TASK →
  Area: tests/ (TEST COVERAGE)
  Thoroughness: medium
  ANALYZE: test structure, frameworks, coverage areas
  IDENTIFY: `tested` modules, missing coverage, test patterns
  EXTRACT: {path|test_files|framework|covered_modules|gaps}
→ END-TASK, 'OUTPUT({path:"tests",files:N,framework:str,coverage_gaps:[]})', 'STORE-AS($TEST_ANALYSIS)') Task(@agent-explore, TASK →
  Area: database/ + migrations/ (DATA LAYER)
  Thoroughness: thorough
  ANALYZE: migrations, seeders, factories, schema
  IDENTIFY: tables, relationships, indexes, `pending` migrations
  EXTRACT: {migrations_count|tables|relationships|pending_changes}
→ END-TASK, 'OUTPUT({migrations:N,tables:[],relationships:[],`pending`:[]})', 'STORE-AS($DATABASE_ANALYSIS)')

- `2`: NOTE: All 3 ExploreMaster agents run SIMULTANEOUSLY

# Phase3 parallel config
GOAL(PARALLEL: Config, routes, and infrastructure analysis)
- `1`: 
BATCH 2 - Config & Infrastructure (LAUNCH IN PARALLEL):
Task(@agent-explore, TASK →
  Area: config/ (CONFIGURATION)
  Thoroughness: quick
  ANALYZE: config files, env vars, service bindings
  IDENTIFY: services configured, missing configs, security settings
  EXTRACT: {configs:[names],services:[],env_vars_needed:[]}
→ END-TASK, 'OUTPUT({configs:[],services:[],security_gaps:[]})', 'STORE-AS($CONFIG_ANALYSIS)') Task(@agent-explore, TASK →
  Area: routes/ (API SURFACE)
  Thoroughness: thorough
  ANALYZE: route definitions, middleware, controllers
  IDENTIFY: endpoints, auth requirements, API versioning
  EXTRACT: {routes_count|endpoints:[method,path,controller]|middleware:[]}
→ END-TASK, 'OUTPUT({routes:N,api_endpoints:[],web_routes:[],middleware:[]})', 'STORE-AS($ROUTES_ANALYSIS)') Task(@agent-explore, TASK →
  Area: build/CI (.github/, docker*, Makefile)
  Thoroughness: quick
  ANALYZE: CI/CD pipelines, Docker setup, build scripts
  IDENTIFY: deployment process, missing CI steps, containerization
  EXTRACT: {ci:bool,docker:bool,pipelines:[],missing:[]}
→ END-TASK, 'OUTPUT({ci:bool,docker:bool,deployment_ready:bool,gaps:[]})', 'STORE-AS($BUILD_ANALYSIS)')

- `2`: NOTE: All 3 agents run SIMULTANEOUSLY with Batch 1

# Phase4 documentation
GOAL(Index docs via brain docs, then PARALLEL DocumentationMaster analysis)
- `1`: 
STEP 1 - Get documentation index:
Bash('brain docs') STORE-AS($DOCS_INDEX = [{path, name, description, type}])

- `2`: 
STEP 2 - Adaptive batching based on doc count:
IF(docs_count <= 3) → Single DocumentationMaster for all IF(docs_count 4-8) → 2 DocumentationMaster agents in parallel IF(docs_count > 8) → 3+ DocumentationMaster agents in parallel

- `3`: 
STEP 3 - DocumentationMaster for comprehensive doc analysis:
Task(@agent-documentation-master, TASK →
  Analyze ALL project documentation from DOCS_INDEX
  Read: README*, CONTRIBUTING*, ARCHITECTURE*, API docs, .docs/*.md
  EXTRACT: {name|purpose|requirements|constraints|decisions|endpoints|integrations}
  FOCUS ON: project goals, requirements, API contracts, integrations
→ END-TASK, 'OUTPUT({docs_analyzed:N,requirements:[],constraints:[],api_specs:[],integrations:[]})', 'STORE-AS($DOCS_ANALYSIS)')


# Phase5 vector research

GOAL(Search vector memory for prior knowledge via direct MCP calls)
NOTE(Brain uses vector memory MCP tools directly - NO agent delegation needed Simple tool calls do not require agent orchestration overhead Multi-probe search covers all relevant categories)

- `1`: 
PARALLEL: Multi-probe memory searches:
mcp__vector-memory__search_memories('{query: "project architecture implementation patterns", limit: 5, category: "architecture"}') mcp__vector-memory__search_memories('{query: "project requirements features roadmap", limit: 5, category: "learning"}') mcp__vector-memory__search_memories('{query: "bugs issues problems technical debt", limit: 5, category: "bug-fix"}') mcp__vector-memory__search_memories('{query: "decisions trade-offs alternatives", limit: 5, category: "code-solution"}') mcp__vector-memory__search_memories('{query: "project context conventions standards", limit: 5, category: "project-context"}')

- `2`: STORE-AS($PRIOR_KNOWLEDGE = {memories from all probes, filtered for actionable insights})

# Phase6 external
GOAL(WebResearchMaster for external dependencies and APIs)
- `1`: 
CONDITIONAL: If project uses external services/APIs:
IF(external services detected in config/routes analysis) →
  Task(@agent-web-research-master, TASK →
  Research external dependencies: {detected_services}
  Find: API documentation, rate limits, best practices
  Find: known issues, integration patterns, gotchas
  OUTPUT: integration requirements, constraints, risks
→ END-TASK, 'OUTPUT({services_researched:N,requirements:[],risks:[]})', 'STORE-AS($EXTERNAL_CONTEXT)')
→ ELSE →
  SKIP(No external dependencies detected)
→ END-IF


# Phase7 synthesis
GOAL(Synthesize ALL research into comprehensive project context)
- `1`: 
COMBINE all stored research:
STORE-GET($CODE_ANALYSIS) STORE-GET($TEST_ANALYSIS) STORE-GET($DATABASE_ANALYSIS) STORE-GET($CONFIG_ANALYSIS) STORE-GET($ROUTES_ANALYSIS) STORE-GET($BUILD_ANALYSIS) STORE-GET($DOCS_ANALYSIS) STORE-GET($PRIOR_KNOWLEDGE) STORE-GET($EXTERNAL_CONTEXT)

- `2`: 
SEQUENTIAL THINKING for strategic decomposition:
mcp__sequential-thinking__sequentialthinking({
                    thought: "Analyzing comprehensive research from 8+ parallel agents. Synthesizing into strategic epics.",
                    thoughtNumber: 1,
                    totalThoughts: 8,
                    nextThoughtNeeded: true
                })

- `3`: 
SYNTHESIS STEPS:
Step 1: Extract project scope, primary objectives, `success` criteria Step 2: Map functional requirements from docs + code analysis Step 3: Map non-functional requirements (performance, security, scalability) Step 4: Identify current state: greenfield / existing / refactor Step 5: Calculate completion percentage per area Step 6: Identify major work streams (future epics) Step 7: Map dependencies between work streams Step 8: Prioritize: blockers first, then core, then features

- `4`: STORE-AS($PROJECT_SYNTHESIS = comprehensive project understanding)

# Phase8 epic generation
GOAL(Generate 5-15 strategic epics from synthesis)
- `1`: 
EPIC GENERATION RULES:
Target: 5-15 root epics (not too few, not too many) Each epic: major work stream, 8-40 hours estimate Epic boundaries: clear scope, deliverables, acceptance criteria Dependencies: identify inter-epic dependencies Tags: [epic, {domain}, {stack}, {phase}]

- `2`: 
EPIC CATEGORIES to consider:
FOUNDATION: setup, infrastructure, CI/CD, database schema CORE: main features, business logic, models, services API: endpoints, authentication, authorization, contracts FRONTEND: UI components, views, assets, interactions TESTING: unit tests, integration tests, E2E, coverage SECURITY: auth, validation, encryption, audit PERFORMANCE: optimization, caching, scaling, monitoring DOCUMENTATION: API docs, guides, deployment docs TECH_DEBT: refactoring, upgrades, cleanup, migrations

- `3`: STORE-AS($EPIC_LIST = [{title, content, priority, estimate, tags, dependencies}])

# Phase9 approval
GOAL(Present epics for user approval (conditional on $HAS_AUTO_APPROVE))
- `1`: 
FORMAT epic list as table:
# | Epic Title | Priority | Estimate | Dependencies | Tags ---|------------|----------|----------|--------------|----- 1 | Foundation Setup | critical | 16h | - | [epic,infra,setup] 2 | Core Models | high | 24h | #1 | [epic,backend,models] ... (all epics)

- `2`: 
SUMMARY:
Total epics: {count} Total estimated hours: {sum} Critical path: {epics with dependencies} Research agents used: {count} (Explore, Doc, Vector, Web) Areas analyzed: code, tests, database, config, routes, build, docs, memory

- `3`: IF(NOT $HAS_AUTO_APPROVE) →
  Ask: "Approve epic creation? (yes/no/modify)"
  VALIDATE(User response is YES, APPROVE, or CONFIRM) → FAILS → Wait for explicit approval
→ END-IF
- `4`: IF($HAS_AUTO_APPROVE) → Auto-approved. Proceeding to task creation.

# Phase10 create
GOAL(Create epics in vector task system after approval)
- `1`: 
CREATE epics:
mcp__vector-task__task_create_bulk('{tasks: STORE-GET($EPIC_LIST)}') STORE-AS($CREATED_EPICS = [task_ids])

- `2`: 
VERIFY creation:
mcp__vector-task__task_stats('{}') Confirm: {count} epics created


# Phase11 complete
GOAL(Report completion, store insight, STOP)
- `1`: 
STORE initialization insight:
mcp__vector-memory__store_memory({
                    content: "INIT-TASK|epics:{count}|hours:{total}|areas:{list}|stack:{tech}|critical_path:{deps}",
                    category: "architecture",
                    tags: ["insight", "project-wide"]
                })

- `2`: 
REPORT:
═══ INIT-TASK COMPLETE ═══ Epics created: {count} Total estimate: {hours}h Agents used: {agent_count} (parallel execution) Areas covered: code, tests, db, config, routes, build, docs, memory, external ═══════════════════════════  NEXT STEPS:   1. /task:decompose {epic_id} - Break down each epic   2. /task:list - View all tasks   3. /task:next - Start first task

- `3`: STOP: Do NOT execute any task. Return control to user.

# Epic format
Required epic structure
- title: Concise name (max 10 words)
- content: Scope, objectives, deliverables, acceptance criteria
- priority: critical | high | medium | low
- estimate: 8-40 hours (will be decomposed)
- tags: [epic, {domain}, {stack}, {phase}]

# Estimation guide
Epic estimation guidelines
- 8-16h: Focused, single domain
- 16-24h: Cross-component, moderate
- 24-32h: Architectural, integrations
- 32-40h: Foundational, high complexity
- >40h: Split into multiple epics

# Error recovery
Command-specific error handling (trait provides baseline tool error / MCP `failure` policy)
- no .docs/ → codebase analysis only, continue
- agent timeout → skip area, continue, report in summary
- task_create_bulk fails → retry individually, report failures
- vector memory unavailable → skip prior knowledge, continue
- user rejects epics → revise based on feedback, re-propose
- external research fails → local analysis only, -0.2 confidence

# Quality gates
Validation checkpoints
- Gate 1: pre-flight task state checked
- Gate 2: structure discovery `completed` (PROJECT_AREAS populated)
- Gate 3: all parallel exploration batches returned
- Gate 4: documentation analysis `completed`
- Gate 5: vector memory research `completed`
- Gate 6: synthesis produced actionable epics
- Gate 7: each epic has title, content, priority, estimate, tags
- Gate 8: user approval obtained (or auto-approved)
- Gate 9: task_create_bulk succeeded
- Gate 10: completion insight stored to vector memory

# Parallel pattern
How to execute agents in parallel
- WRONG: forEach(areas) → sequential, slow, incomplete
- RIGHT: List multiple Task() calls in single response
- Brain executes all Task() calls simultaneously
- Each agent stores findings, then synthesize all

# Directive
PARALLEL agents! EVERY corner! MAXIMUM coverage! Dense synthesis! User approval!

</command>