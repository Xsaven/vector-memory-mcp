---
name: "init-agents"
description: "Incremental Agent Gap Analyzer - Auto-generates missing domain agents (project)"
---

<command>
<meta>
<id>init-agents</id>
<description>Incremental Agent Gap Analyzer - Auto-generates missing domain agents (project)</description>
</meta>
<execute>Auto-analyze
.claude/CLAUDE.md
and existing agents → identify gaps → generate missing agents via
brain make:master
→ safe for repeated runs. Supports optional $ARGUMENTS for targeted search.</execute>
<provides>The InitAgents command initializes the project with agents based on industry best practices.</provides>

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

## Mandatory-agentmaster-delegation (CRITICAL)
Brain MUST delegate ALL agent generation to AgentMaster. FORBIDDEN: Brain creating agents directly.
- **why**: Separation of orchestration (Brain) and execution (AgentMaster). Brain is NOT an executor.
- **on_violation**: ABORT. Delegate to AgentMaster immediately. Brain orchestrates, never executes.

## Project-agents-only (CRITICAL)
ONLY create PROJECT-SPECIFIC agents using Master variation FORBIDDEN: Creating or modifying SYSTEM agents (AgentMaster, PromptMaster, CommitMaster, etc.) FORBIDDEN: Modifying agents from vendor/jarvis-brain/core/src/ System agents use SystemMaster variation and are OFF LIMITS
- **why**: System agents are pre-configured via SystemMaster variation. Project agents use Master variation.
- **on_violation**: Skip system agent. Only create project-specific agents.

## Preserve-system-agents (CRITICAL)
System agents (ending with Master) are managed by SystemMaster variation Examples: AgentMaster, PromptMaster, CommitMaster, WebResearchMaster, ExploreMaster, DocumentationMaster, VectorMaster, ScriptMaster These agents are specialized for Brain orchestration - NEVER regenerate or modify them
- **why**: System agents have carefully tuned configurations for Brain ecosystem
- **on_violation**: ABORT modification. System agents are immutable.

## Parallel-agentmaster-execution (CRITICAL)
Run up to 5 AgentMaster instances in PARALLEL. Each AgentMaster can generate 1-3 agents per batch.
- **why**: Maximizes throughput. Sequential generation wastes time. Parallel = 5x faster.
- **on_violation**: Batch agents into groups of 3, delegate to 5 AgentMasters concurrently.

## Auto-approve-default (CRITICAL)
Default behavior is FULLY AUTOMATED (no user prompts). $HAS_AUTO_APPROVE = true confirms. Gap analysis and generation run autonomously. Without -y: show summary before generation starts. With -y: fully silent pipeline.
- **why**: Automated workflow requires zero interaction by default.
- **on_violation**: Proceed autonomously. Never block on user input.

## Brain-make-master-only (CRITICAL)
AgentMaster MUST use Bash('brain make:master') for creation - NOT Write() or Edit()
- **why**: Ensures proper PHP archetype structure and compilation compatibility
- **on_violation**: Reject manually created agents

## No-regeneration (CRITICAL)
Skip existing agents. Idempotent operation.
- **why**: Safe for repeated execution
- **on_violation**: Skip and continue

## Delegates-web-research (HIGH)
Delegate web research to WebResearchMaster. Brain NEVER executes WebSearch.
- **why**: Maintains delegation hierarchy
- **on_violation**: Delegate to WebResearchMaster


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
STORE-AS($TARGET_DOMAIN = {optional domain hint from $CLEAN_ARGS: "Laravel", "React", "API", or empty for full discovery})

# Phase0 arguments processing
GOAL(Process optional user arguments to narrow search scope and improve targeting)
- `1`: OUTPUT(=== INIT:AGENTS ACTIVATED ===  === PHASE 0: ARGUMENTS PROCESSING === Processing input...)
- `2`: Parse $CLEAN_ARGS for specific domain/technology/agent hints
- `3`: IF($CLEAN_ARGS not empty) →
  Extract from $TARGET_DOMAIN: technology, specific_agents
  STORE-AS($SEARCH_FILTER = {domain: $TARGET_DOMAIN, tech: ..., agents: [...], keywords: [...]})
  Set search_mode = "targeted"
  Log: "Targeted mode: focusing on {domain}/{tech}"
→ END-IF
- `4`: IF($CLEAN_ARGS empty) →
  Set search_mode = "discovery"
  Use full project analysis workflow
  Log: "Discovery mode: full project analysis"
→ END-IF
- `5`: Store search mode for use in subsequent phases

# Phase1 temporal context and web cache
GOAL(Get current date/year for temporal context AND check vector memory cache for recent patterns)
- `1`: Bash(date +"%Y-%m-%d") → [STORE-AS($CURRENT_DATE)] → END-Bash
- `2`: Bash(date +"%Y") → [STORE-AS($CURRENT_YEAR)] → END-Bash
- `3`: PARALLEL: Check vector memory cache while temporal context loads
- `4`: mcp__vector-memory__search_memories('{query: "multi-agent architecture patterns", category: "learning", limit: 3}')
- `5`: IF(cache_hit AND cache_age < 30 days) →
  STORE-AS($CACHED_PATTERNS = Cached industry patterns from vector memory)
  STORE-AS($CACHE_VALID = true)
  STORE-AS($CACHE_AGE = {days})
  Log: "Using cached patterns (age: {days} days)"
→ END-IF
- `6`: IF(no_cache OR cache_old) →
  STORE-AS($CACHE_VALID = false)
  Log: "Fresh web search required"
→ END-IF

# Phase1.5 web search best practices

GOAL(Delegate industry best practices research to WebResearchMaster with cache awareness)
NOTE(Delegated to WebResearchMaster for industry research)

- `1`: IF(search_mode === "discovery") →
  Task(@agent-web-research-master, 'INPUT(STORE-GET($CURRENT_YEAR) && STORE-GET($CACHED_PATTERNS) && STORE-GET($CACHE_VALID) && STORE-GET($CACHE_AGE))', TASK →
  IF(cache_valid === true) → Use cached patterns, skip web search
  IF(cache_valid === false) →
  Research: multi-agent system architecture best practices {year}
  Research: AI agent orchestration patterns {year}
  Research: domain-driven agent design principles {year}
  Synthesize findings into unified patterns
→ END-IF
→ END-TASK, 'OUTPUT({architecture: [...], orchestration: [...], domain_design: [...], sources: [...], cache_used: true|false})', 'STORE-AS($INDUSTRY_PATTERNS)')
  IF(fresh research performed) → Store results in vector memory
  mcp__vector-memory__store_memory('{content: $INDUSTRY_PATTERNS, category: "learning", tags: ["pattern", "reusable"]}')
→ END-IF
- `2`: IF(search_mode === "targeted") →
  Use $CACHED_PATTERNS from phase 1 if available
  Log: "Targeted mode - using cached patterns, skipping general research"
→ END-IF

# Phase2 inventory agents

GOAL(List all existing agents and separate SYSTEM agents from PROJECT agents)
NOTE(SYSTEM agents (use SystemMaster variation): AgentMaster, PromptMaster, CommitMaster, WebResearchMaster, ExploreMaster, DocumentationMaster, VectorMaster, ScriptMaster PROJECT agents (use Master variation): All other agents created for project-specific needs System agents are OFF LIMITS - only inventory, never modify or regenerate)

- `1`: Bash('brain list:masters') Parse output
- `2`: STORE-AS($ALL_AGENTS = [{id, name, description}, ...])
- `3`: Filter system agents (names ending with "Master" AND in system list)
- `4`: STORE-AS($SYSTEM_AGENTS = [AgentMaster, PromptMaster, CommitMaster, WebResearchMaster, ExploreMaster, DocumentationMaster, VectorMaster, ScriptMaster, ...])
- `5`: Filter project agents (all others)
- `6`: STORE-AS($PROJECT_AGENTS = [...project-specific agents...])
- `7`: Agents located in .brain/node/Agents/*.php
- `8`: Count: system_agents = count($SYSTEM_AGENTS), project_agents = count($PROJECT_AGENTS)
- `9`: Log: "System agents (protected): {system_count}, Project agents (manageable): {project_count}"

# Phase3 read project stack
GOAL(Extract project technology stack with optional filtering based on $TARGET_DOMAIN)
- Task(@agent-explore, TASK →
  IF(search_mode === "targeted") →
  Priority 1: Focus exploration on $SEARCH_FILTER.domain and $SEARCH_FILTER.tech
  Priority 2: Validate against project documentation (.docs/, CLAUDE.md)
  Priority 3: Extract related technologies and dependencies
→ END-IF
  IF(search_mode === "discovery") →
  Priority 1: Explore .docs/ directory if exists. Find all *.md files.
  Priority 2: Extract: technologies, frameworks, services, domain requirements
  Priority 3: Explore project files in ./ for tech stack (composer.json, package.json, etc.)
→ END-IF
  STORE-AS($PROJECT_STACK = {technologies: [...], frameworks: [...], services: [...], domain_requirements: [...], primary_stack: "...", confidence: 0-1})
→ END-TASK)

# Phase3.5 stack specific search

GOAL(Delegate technology-specific research to WebResearchMaster based on discovered stack)
NOTE(Delegated to WebResearchMaster for technology-specific patterns)

- `1`: Extract primary technologies from $PROJECT_STACK (max 3 most important)
- `2`: Task(@agent-web-research-master, 'INPUT(STORE-GET($PROJECT_STACK.TECHNOLOGIES) && STORE-GET($CURRENT_YEAR) && STORE-GET($SEARCH_FILTER) && STORE-GET($SEARCH_MODE))', TASK →
  Extract top 3 most important technologies from stack
  IF(search_mode === "targeted") →
  Focus on STORE-GET($SEARCH_FILTER) tech
→ END-IF
  FOREACH(technology in top_3_technologies) →
  IF(technology is major framework/language) →
  Research: {technology} specialized agents best practices {year}
  Research: {technology} multi-agent architecture examples {year}
  Extract: common patterns, agent types, use cases
→ END-IF
→ END-FOREACH
  Synthesize per-technology patterns
→ END-TASK, 'OUTPUT({tech_patterns: {Laravel: [...], React: [...]}, tech_examples: {...}})', 'STORE-AS($TECH_PATTERNS)')
- `3`: Cache technology patterns in vector memory
- `4`: mcp__vector-memory__store_memory('{content: $TECH_PATTERNS, category: "learning", tags: ["pattern", "reusable"]}')
- `5`: IF(search_mode === "targeted") →
  Log: "Found {count} patterns for {$SEARCH_FILTER.tech}"
  Boost relevance score for matching patterns
→ END-IF

# Phase4 gap analysis

GOAL(Identify missing PROJECT-SPECIFIC agents (NOT system agents))
NOTE(Gap analysis compares against PROJECT_AGENTS only, not SYSTEM_AGENTS FORBIDDEN: Suggesting system agents (AgentMaster, PromptMaster, etc.) as missing New agents use Master variation, NOT SystemMaster AgentMaster analyzes gaps using already collected data - no additional web search needed)

- `1`: mcp__sequential-thinking__sequentialthinking({
                thought: "Analyzing agent gaps. Comparing: project requirements vs existing agents, industry patterns vs coverage, technology stack vs specialized needs.",
                thoughtNumber: 1,
                totalThoughts: 3,
                nextThoughtNeeded: true
            })
- `2`: AgentMaster performs gap analysis using cached patterns
- `3`: Task(@agent-agent-master, 'INPUT(STORE-GET($PROJECT_AGENTS) && STORE-GET($SYSTEM_AGENTS) && STORE-GET($PROJECT_STACK) && STORE-GET($INDUSTRY_PATTERNS) && STORE-GET($TECH_PATTERNS) && STORE-GET($SEARCH_FILTER))', TASK →
  Analyze domain expertise needed based on Project requirements
  Compare with existing PROJECT agents (NOT system agents)
  EXCLUDE any agent that overlaps with SYSTEM_AGENTS functionality
  Cross-validate against INDUSTRY_PATTERNS and TECH_PATTERNS (already cached)
  EXCLUDE: system agent types (orchestration, delegation, memory management)
  INCLUDE: domain-specific agents (API, Cache, Auth, Payment, etc.)
  Assign confidence score (0-1) to each missing agent recommendation
  Prioritize critical gaps with high industry alignment
  All new agents will use Master variation (NOT SystemMaster)
  IF(search_mode === "targeted") →
  Focus on $SEARCH_FILTER domains only
→ END-IF
→ END-TASK, 'OUTPUT({covered_domains: [...], missing_agents: [{name: \\'AgentName\\', purpose: \\'...\\', capabilities: [...], confidence: 0-1, industry_alignment: 0-1, priority: "critical|high|medium", variation: "Master"}], industry_coverage_score: 0-1})', 'NOTE(Focus on PROJECT agent gaps with high confidence and industry alignment)')
- `4`: STORE-AS($GAP_ANALYSIS)
- `5`: Filter: confidence >= 0.6, industry_alignment >= 0.6, priority != "low"
- `6`: Validate: NO agent names match SYSTEM_AGENTS list
- `7`: Sort by: priority DESC, confidence DESC, industry_alignment DESC

# Phase5 parallel generation

GOAL(Create missing PROJECT agents via PARALLEL AgentMaster delegation. Max 5 concurrent AgentMasters.)
NOTE(Created agents are PROJECT agents using Master variation Created files must be valid PHP archetypes extending BrainCore\\Archetypes\\AgentArchetype FORBIDDEN: Creating any agent that matches SYSTEM_AGENTS list)

- `1`: Step 1: Filter and batch agents
- `2`: Remove: existing agents, confidence < 0.6, priority === "low" Remove: any agent matching SYSTEM_AGENTS names (AgentMaster, PromptMaster, etc.) Keep: confidence >= 0.6 AND (priority === "critical" OR priority === "high") STORE-AS($FILTERED_AGENTS = [...filtered project agents only...])
- `3`: Step 2: Batch into groups of 3 (max 5 batches = 15 agents)
- `4`: batch_1 = agents[0:3], batch_2 = agents[3:6], ... STORE-AS($AGENT_BATCHES = [[batch1], [batch2], [batch3], [batch4], [batch5]])
- `5`: Step 3: Pre-create all agent files via brain make:master
- `6`: FOREACH(agent in $FILTERED_AGENTS) →
  Bash(brain make:master {agent.name}) → [Creates .brain/node/Agents/{agent.name}.php] → END-Bash
→ END-FOREACH
- `7`: Step 4: PARALLEL delegation to 5 AgentMasters
- `8`: CRITICAL: Launch ALL 5 Task() calls in SINGLE message block Each AgentMaster receives batch of 1-3 PROJECT agents to configure AgentMasters work CONCURRENTLY, not sequentially All agents use Master variation (for project agents)
- `9`: FOREACH(batch in $AGENT_BATCHES (PARALLEL)) →
  Task(@agent-agent-master, 'INPUT(batch_agents = [{name, purpose, capabilities, confidence, variation: "Master"}, ...] && STORE-GET($INDUSTRY_PATTERNS) && STORE-GET($TECH_PATTERNS) && STORE-GET($PROJECT_AGENTS))', TASK →
  FOREACH agent in batch_agents:
    1. Read created file: .brain/node/Agents/{agent.name}.php
    2. Update #[Purpose()] with detailed domain expertise
    3. Add #[Includes(Master::class)] for project agent variation
    4. Add project-specific includes from .brain/node/Includes/
    5. Define rules + guidelines in handle()
    6. Use PHP API only (Runtime::, Operator::, Store::)
  Return: {`completed`: [...], failed: [...]}
→ END-TASK, 'OUTPUT({batch_id, `completed`: [names], failed: [names], errors: [...]})')
→ END-FOREACH
- `10`: Step 5: Aggregate results from all AgentMasters
- `11`: Merge: all `completed` PROJECT agents from 5 AgentMaster responses Collect: all failures for error report STORE-AS($GENERATION_SUMMARY = {generated: [...], failed: [...], total: N, variation: "Master"})

# Phase6 compile
GOAL(Compile all agents to .claude/agents/)
- `1`: Bash(brain compile) → [Compiles .brain/node/Agents/*.php to .claude/agents/] → END-Bash
- `2`: VERIFY-SUCCESS(CHECK(.claude/agents/ for new agent files) Compilation `completed` without errors)
- `3`: Log: "Compilation complete. New agents available in {AGENTS_FOLDER}"

# Phase7 report enhanced
GOAL(Report generation results with confidence scores, industry alignment, and caching status)
- `1`: IF(agents_generated > 0) →
  Calculate: avg_confidence = average(generated_agents.confidence)
  Calculate: avg_industry_alignment = average(generated_agents.industry_alignment)
  mcp__vector-memory__store_memory('{content: "INIT-AGENTS|mode={search_mode}|tech={$PROJECT_STACK.technologies}|agents={agents_count}|confidence={avg_confidence}|alignment={avg_industry_alignment}|coverage=improved", category: "architecture", tags: ["insight", "project-wide"]}')
  OUTPUT(Generation summary with agent details, confidence scores, and industry alignment metrics)
→ END-IF
- `2`: IF(agents_generated === 0) →
  mcp__vector-memory__store_memory('{content: "INIT-AGENTS|mode={search_mode}|result=full_coverage|agents={agents_count}", category: "architecture", tags: ["insight", "project-wide"]}')
  OUTPUT(Full coverage confirmation with existing agent list and industry coverage score)
→ END-IF
- `3`: Include cache performance metrics: {cache_hits}, {web_searches_performed}

# Response format
Response structure
- Header: Init Gap Analysis Complete | Mode: {search_mode}
- System Agents (protected): {system_count} | Variation: SystemMaster
- Project Agents Generated: {count} | Variation: Master
- Quality: confidence={avg}, alignment={avg}
- Performance: cache_hits={n}, parallel_batches={n}
- `1`: Created: .brain/node/Agents/ | Compiled: .claude/agents/
- `2`: Ready via: [DELEGATE] @agent-{name}: '...'

# Error recovery
Command-specific error handling (trait provides baseline tool error / MCP `failure` policy)
- no .docs/ → Brain context only, continue
- agent exists → skip, log preserved
- system agent suggested → skip, log "system agent protected"
- make:master fails → skip agent, continue
- compile fails → report errors, list failed
- AgentMaster fails → skip batch, continue
- web timeout → use cached, mark partial
- no internet → local only, -0.2 confidence
- memory unavailable → skip cache, continue

# Quality gates
Validation checkpoints
- Gate 1-3: temporal context, cache check, list:masters
- Gate 4: system vs project agents separated
- Gate 5-6: web delegation, gap analysis output
- Gate 7: NO system agents in GAP_ANALYSIS.missing_agents
- Gate 8-9: confidence >= 0.6, alignment >= 0.6
- Gate 10-11: make:master `success`, compile `success`
- Gate 12: .claude/agents/ populated with project agents

# Example parallel batch
SCENARIO(System: 8 protected (SystemMaster) | Project: 10 discovered → 4 batches → 4 parallel AgentMasters)
- `inventory`: System agents (OFF LIMITS): AgentMaster, PromptMaster, CommitMaster, ExploreMaster, WebResearchMaster, DocumentationMaster, VectorMaster, ScriptMaster
- `gap`: 10 missing PROJECT agents: API, Cache, Queue, Auth, Payment, Report, Search, Export, Import, Sync
- `variation`: All new agents use Master variation (NOT SystemMaster)
- `batch`: batch_1=[API,Cache,Queue], batch_2=[Auth,Payment,Report], batch_3=[Search,Export], batch_4=[Import,Sync]
- `parallel`: Launch 4 Task(@agent-agent-master) in SINGLE message
- `result`: All 10 PROJECT agents created with Master variation in ~1 AgentMaster cycle

# Directive
PROJECT agents ONLY! Master variation! NEVER touch system agents! DELEGATE to AgentMaster! PARALLEL batches! brain make:master! Compile!

</command>