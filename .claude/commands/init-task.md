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

## Mandatory-user-approval (CRITICAL)
MUST get explicit user YES/APPROVE/CONFIRM before creating ANY tasks
- **why**: User must validate task breakdown before committing
- **on_violation**: Present task list and wait for explicit confirmation

## Estimate-required (CRITICAL)
MUST provide time estimate (8-40h) for EACH epic
- **why**: Estimates enable planning and identify tasks needing decomposition
- **on_violation**: Add estimate before presenting epic

## Exclude-brain-directory (CRITICAL)
NEVER analyze .brain/ - Brain system internals, not project code
- **why**: Brain config pollutes task list with irrelevant system tasks
- **on_violation**: Skip .brain/ in all exploration phases


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($INIT_PARAMS = {initialization parameters extracted from $RAW_INPUT})

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
- `1`: Task(@agent-explore, 'TASK →'."\\n"
    .'  QUICK STRUCTURE SCAN - identify directories only'."\\n"
    .'  Glob("*") → list root directories and key files'."\\n"
    .'  EXCLUDE: .brain/, vendor/, node_modules/, .git/'."\\n"
    .'  IDENTIFY: code(src/app), tests, config, docs(.docs), migrations, routes, build, public'."\\n"
    .'  Output JSON: {areas: [{path, type, estimated_files, priority}]}'."\\n"
    .'→ END-TASK', 'OUTPUT({areas: [{path, type, estimated_files, priority: critical|high|medium|low}]})', 'STORE-AS($PROJECT_AREAS)')

# Phase2 parallel code
GOAL(PARALLEL: Launch code exploration agents simultaneously)
- `1`: 
BATCH 1 - Core Code Areas (LAUNCH IN PARALLEL):
Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: src/ or app/ (MAIN CODE)'."\\n"
    .'  Thoroughness: very thorough'."\\n"
    .'  ANALYZE: directory structure, namespaces, classes, design patterns'."\\n"
    .'  IDENTIFY: entry points, core modules, service layers, models'."\\n"
    .'  EXTRACT: {path|files_count|classes|namespaces|patterns|complexity}'."\\n"
    .'  FOCUS ON: what needs to be built/refactored/improved'."\\n"
    .'→ END-TASK', 'OUTPUT({path:"src",files:N,modules:[],patterns:[],tech_debt:[]})', 'STORE-AS($CODE_ANALYSIS)') Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: tests/ (TEST COVERAGE)'."\\n"
    .'  Thoroughness: medium'."\\n"
    .'  ANALYZE: test structure, frameworks, coverage areas'."\\n"
    .'  IDENTIFY: `tested` modules, missing coverage, test patterns'."\\n"
    .'  EXTRACT: {path|test_files|framework|covered_modules|gaps}'."\\n"
    .'→ END-TASK', 'OUTPUT({path:"tests",files:N,framework:str,coverage_gaps:[]})', 'STORE-AS($TEST_ANALYSIS)') Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: database/ + migrations/ (DATA LAYER)'."\\n"
    .'  Thoroughness: thorough'."\\n"
    .'  ANALYZE: migrations, seeders, factories, schema'."\\n"
    .'  IDENTIFY: tables, relationships, indexes, `pending` migrations'."\\n"
    .'  EXTRACT: {migrations_count|tables|relationships|pending_changes}'."\\n"
    .'→ END-TASK', 'OUTPUT({migrations:N,tables:[],relationships:[],`pending`:[]})', 'STORE-AS($DATABASE_ANALYSIS)')

- `2`: NOTE: All 3 ExploreMaster agents run SIMULTANEOUSLY

# Phase3 parallel config
GOAL(PARALLEL: Config, routes, and infrastructure analysis)
- `1`: 
BATCH 2 - Config & Infrastructure (LAUNCH IN PARALLEL):
Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: config/ (CONFIGURATION)'."\\n"
    .'  Thoroughness: quick'."\\n"
    .'  ANALYZE: config files, env vars, service bindings'."\\n"
    .'  IDENTIFY: services configured, missing configs, security settings'."\\n"
    .'  EXTRACT: {configs:[names],services:[],env_vars_needed:[]}'."\\n"
    .'→ END-TASK', 'OUTPUT({configs:[],services:[],security_gaps:[]})', 'STORE-AS($CONFIG_ANALYSIS)') Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: routes/ (API SURFACE)'."\\n"
    .'  Thoroughness: thorough'."\\n"
    .'  ANALYZE: route definitions, middleware, controllers'."\\n"
    .'  IDENTIFY: endpoints, auth requirements, API versioning'."\\n"
    .'  EXTRACT: {routes_count|endpoints:[method,path,controller]|middleware:[]}'."\\n"
    .'→ END-TASK', 'OUTPUT({routes:N,api_endpoints:[],web_routes:[],middleware:[]})', 'STORE-AS($ROUTES_ANALYSIS)') Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: build/CI (.github/, docker*, Makefile)'."\\n"
    .'  Thoroughness: quick'."\\n"
    .'  ANALYZE: CI/CD pipelines, Docker setup, build scripts'."\\n"
    .'  IDENTIFY: deployment process, missing CI steps, containerization'."\\n"
    .'  EXTRACT: {ci:bool,docker:bool,pipelines:[],missing:[]}'."\\n"
    .'→ END-TASK', 'OUTPUT({ci:bool,docker:bool,deployment_ready:bool,gaps:[]})', 'STORE-AS($BUILD_ANALYSIS)')

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
Task(@agent-documentation-master, 'TASK →'."\\n"
    .'  Analyze ALL project documentation from DOCS_INDEX'."\\n"
    .'  Read: README*, CONTRIBUTING*, ARCHITECTURE*, API docs, .docs/*.md'."\\n"
    .'  EXTRACT: {name|purpose|requirements|constraints|decisions|endpoints|integrations}'."\\n"
    .'  FOCUS ON: project goals, requirements, API contracts, integrations'."\\n"
    .'→ END-TASK', 'OUTPUT({docs_analyzed:N,requirements:[],constraints:[],api_specs:[],integrations:[]})', 'STORE-AS($DOCS_ANALYSIS)')


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
  Task(@agent-web-research-master, 'TASK →'."\\n"
    .'  Research external dependencies: {detected_services}'."\\n"
    .'  Find: API documentation, rate limits, best practices'."\\n"
    .'  Find: known issues, integration patterns, gotchas'."\\n"
    .'  OUTPUT: integration requirements, constraints, risks'."\\n"
    .'→ END-TASK', 'OUTPUT({services_researched:N,requirements:[],risks:[]})', 'STORE-AS($EXTERNAL_CONTEXT)')
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
mcp__sequential-thinking__sequentialthinking('{'."\\n"
    .'                    thought: "Analyzing comprehensive research from 8+ parallel agents. Synthesizing into strategic epics.",'."\\n"
    .'                    thoughtNumber: 1,'."\\n"
    .'                    totalThoughts: 8,'."\\n"
    .'                    nextThoughtNeeded: true'."\\n"
    .'                }')

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
GOAL(Present epics for user approval (MANDATORY GATE))
- `1`: 
FORMAT epic list as table:
# | Epic Title | Priority | Estimate | Dependencies | Tags ---|------------|----------|----------|--------------|----- 1 | Foundation Setup | critical | 16h | - | [epic,infra,setup] 2 | Core Models | high | 24h | #1 | [epic,backend,models] ... (all epics)

- `2`: 
SUMMARY:
Total epics: {count} Total estimated hours: {sum} Critical path: {epics with dependencies} Research agents used: {count} (Explore, Doc, Vector, Web) Areas analyzed: code, tests, database, config, routes, build, docs, memory

- `3`: 
PROMPT:
Ask: "Approve epic creation? (yes/no/modify)" VALIDATE(User response is YES, APPROVE, or CONFIRM) → FAILS → Wait for explicit approval


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
mcp__vector-memory__store_memory('{'."\\n"
    .'                    content: "PROJECT_INIT|epics:{count}|hours:{total}|areas:{list}|stack:{tech}|critical_path:{deps}",'."\\n"
    .'                    category: "architecture",'."\\n"
    .'                    tags: ["project-init", "epics", "planning", "init-task"]'."\\n"
    .'                }')

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

# Parallel pattern
How to execute agents in parallel
- WRONG: forEach(areas) → sequential, slow, incomplete
- RIGHT: List multiple Task() calls in single response
- Brain executes all Task() calls simultaneously
- Each agent stores findings, then synthesize all

# Directive
PARALLEL agents! EVERY corner! MAXIMUM coverage! Dense synthesis! User approval!

</command>