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

## No-interactive-questions (CRITICAL)
NO interactive questions. Fully automated gap analysis and generation.
- **why**: Automated workflow requires zero user prompts
- **on_violation**: Execute fully automated

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


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($INIT_PARAMS = {initialization parameters extracted from $RAW_INPUT})

# Phase0 arguments processing
GOAL(Process optional user arguments to narrow search scope and improve targeting)
- `1`: OUTPUT(=== INIT:AGENTS ACTIVATED ===  === PHASE 0: ARGUMENTS PROCESSING === Processing input...)
- `2`: STORE-AS($TARGET_DOMAIN = {extract domain hint from $RAW_INPUT if provided})
- `3`: Parse $RAW_INPUT for specific domain/technology/agent hints
- `4`: IF($RAW_INPUT provided) →
  Extract: target_domain from $TARGET_DOMAIN (e.g., "Laravel", "React", "API"), target_technology, specific_agents
  STORE-AS($SEARCH_FILTER = {domain: $TARGET_DOMAIN, tech: ..., agents: [...], keywords: [...]})
  Set search_mode = "targeted"
  Log: "Targeted mode: focusing on {domain}/{tech}"
→ END-IF
- `5`: IF($RAW_INPUT empty) →
  Set search_mode = "discovery"
  Use full project analysis workflow
  Log: "Discovery mode: full project analysis"
→ END-IF
- `6`: Store search mode for use in subsequent phases

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
  Task(@agent-web-research-master, 'INPUT(STORE-GET($CURRENT_YEAR) && STORE-GET($CACHED_PATTERNS) && STORE-GET($CACHE_VALID) && STORE-GET($CACHE_AGE))', 'TASK →'."\\n"
    .'  IF(cache_valid === true) → Use cached patterns, skip web search'."\\n"
    .'  IF(cache_valid === false) →'."\\n"
    .'  Research: multi-agent system architecture best practices {year}'."\\n"
    .'  Research: AI agent orchestration patterns {year}'."\\n"
    .'  Research: domain-driven agent design principles {year}'."\\n"
    .'  Synthesize findings into unified patterns'."\\n"
    .'→ END-IF'."\\n"
    .'→ END-TASK', 'OUTPUT({architecture: [...], orchestration: [...], domain_design: [...], sources: [...], cache_used: true|false})', 'STORE-AS($INDUSTRY_PATTERNS)')
  IF(fresh research performed) → Store results in vector memory
  mcp__vector-memory__store_memory('{content: $INDUSTRY_PATTERNS, category: "learning", tags: ["agent-patterns", "best-practices", "{CURRENT_YEAR}"]}')
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
- Task(@agent-explore, 'TASK →'."\\n"
    .'  IF(search_mode === "targeted") →'."\\n"
    .'  Priority 1: Focus exploration on $SEARCH_FILTER.domain and $SEARCH_FILTER.tech'."\\n"
    .'  Priority 2: Validate against project documentation (.docs/, CLAUDE.md)'."\\n"
    .'  Priority 3: Extract related technologies and dependencies'."\\n"
    .'→ END-IF'."\\n"
    .'  IF(search_mode === "discovery") →'."\\n"
    .'  Priority 1: Explore .docs/ directory if exists. Find all *.md files.'."\\n"
    .'  Priority 2: Extract: technologies, frameworks, services, domain requirements'."\\n"
    .'  Priority 3: Explore project files in ./ for tech stack (composer.json, package.json, etc.)'."\\n"
    .'→ END-IF'."\\n"
    .'  STORE-AS($PROJECT_STACK = {technologies: [...], frameworks: [...], services: [...], domain_requirements: [...], primary_stack: "...", confidence: 0-1})'."\\n"
    .'→ END-TASK')

# Phase3.5 stack specific search

GOAL(Delegate technology-specific research to WebResearchMaster based on discovered stack)
NOTE(Delegated to WebResearchMaster for technology-specific patterns)

- `1`: Extract primary technologies from $PROJECT_STACK (max 3 most important)
- `2`: Task(@agent-web-research-master, 'INPUT(STORE-GET($PROJECT_STACK.TECHNOLOGIES) && STORE-GET($CURRENT_YEAR) && STORE-GET($SEARCH_FILTER) && STORE-GET($SEARCH_MODE))', 'TASK →'."\\n"
    .'  Extract top 3 most important technologies from stack'."\\n"
    .'  IF(search_mode === "targeted") →'."\\n"
    .'  Focus on STORE-GET($SEARCH_FILTER) tech'."\\n"
    .'→ END-IF'."\\n"
    .'  FOREACH(technology in top_3_technologies) →'."\\n"
    .'  IF(technology is major framework/language) →'."\\n"
    .'  Research: {technology} specialized agents best practices {year}'."\\n"
    .'  Research: {technology} multi-agent architecture examples {year}'."\\n"
    .'  Extract: common patterns, agent types, use cases'."\\n"
    .'→ END-IF'."\\n"
    .'→ END-FOREACH'."\\n"
    .'  Synthesize per-technology patterns'."\\n"
    .'→ END-TASK', 'OUTPUT({tech_patterns: {Laravel: [...], React: [...]}, tech_examples: {...}})', 'STORE-AS($TECH_PATTERNS)')
- `3`: Cache technology patterns in vector memory
- `4`: mcp__vector-memory__store_memory('{content: $TECH_PATTERNS, category: "learning", tags: ["tech-patterns", $PROJECT_STACK.primary_stack, "{CURRENT_YEAR}"]}')
- `5`: IF(search_mode === "targeted") →
  Log: "Found {count} patterns for {$SEARCH_FILTER.tech}"
  Boost relevance score for matching patterns
→ END-IF

# Phase4 gap analysis

GOAL(Identify missing PROJECT-SPECIFIC agents (NOT system agents))
NOTE(Gap analysis compares against PROJECT_AGENTS only, not SYSTEM_AGENTS FORBIDDEN: Suggesting system agents (AgentMaster, PromptMaster, etc.) as missing New agents use Master variation, NOT SystemMaster AgentMaster analyzes gaps using already collected data - no additional web search needed)

- `1`: mcp__sequential-thinking__sequentialthinking('{'."\\n"
    .'                thought: "Analyzing agent gaps. Comparing: project requirements vs existing agents, industry patterns vs coverage, technology stack vs specialized needs.",'."\\n"
    .'                thoughtNumber: 1,'."\\n"
    .'                totalThoughts: 3,'."\\n"
    .'                nextThoughtNeeded: true'."\\n"
    .'            }')
- `2`: AgentMaster performs gap analysis using cached patterns
- `3`: Task(@agent-agent-master, 'INPUT(STORE-GET($PROJECT_AGENTS) && STORE-GET($SYSTEM_AGENTS) && STORE-GET($PROJECT_STACK) && STORE-GET($INDUSTRY_PATTERNS) && STORE-GET($TECH_PATTERNS) && STORE-GET($SEARCH_FILTER))', 'TASK →'."\\n"
    .'  Analyze domain expertise needed based on Project requirements'."\\n"
    .'  Compare with existing PROJECT agents (NOT system agents)'."\\n"
    .'  EXCLUDE any agent that overlaps with SYSTEM_AGENTS functionality'."\\n"
    .'  Cross-validate against INDUSTRY_PATTERNS and TECH_PATTERNS (already cached)'."\\n"
    .'  EXCLUDE: system agent types (orchestration, delegation, memory management)'."\\n"
    .'  INCLUDE: domain-specific agents (API, Cache, Auth, Payment, etc.)'."\\n"
    .'  Assign confidence score (0-1) to each missing agent recommendation'."\\n"
    .'  Prioritize critical gaps with high industry alignment'."\\n"
    .'  All new agents will use Master variation (NOT SystemMaster)'."\\n"
    .'  IF(search_mode === "targeted") →'."\\n"
    .'  Focus on $SEARCH_FILTER domains only'."\\n"
    .'→ END-IF'."\\n"
    .'→ END-TASK', 'OUTPUT({covered_domains: [...], missing_agents: [{name: \\'AgentName\\', purpose: \\'...\\', capabilities: [...], confidence: 0-1, industry_alignment: 0-1, priority: "critical|high|medium", variation: "Master"}], industry_coverage_score: 0-1})', 'NOTE(Focus on PROJECT agent gaps with high confidence and industry alignment)')
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
  Task(@agent-agent-master, 'INPUT(batch_agents = [{name, purpose, capabilities, confidence, variation: "Master"}, ...] && STORE-GET($INDUSTRY_PATTERNS) && STORE-GET($TECH_PATTERNS) && STORE-GET($PROJECT_AGENTS))', 'TASK →'."\\n"
    .'  FOREACH agent in batch_agents:'."\\n"
    .'    1. Read created file: .brain/node/Agents/{agent.name}.php'."\\n"
    .'    2. Update #[Purpose()] with detailed domain expertise'."\\n"
    .'    3. Add #[Includes(Master::class)] for project agent variation'."\\n"
    .'    4. Add project-specific includes from .brain/node/Includes/'."\\n"
    .'    5. Define rules + guidelines in handle()'."\\n"
    .'    6. Use PHP API only (Runtime::, Operator::, Store::)'."\\n"
    .'  Return: {`completed`: [...], failed: [...]}'."\\n"
    .'→ END-TASK', 'OUTPUT({batch_id, `completed`: [names], failed: [names], errors: [...]})')
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
  mcp__vector-memory__store_memory('{content: "Init Gap Analysis: mode={search_mode}, technologies={$PROJECT_STACK.technologies}, agents_generated={agents_count}, avg_confidence={avg_confidence}, avg_industry_alignment={avg_industry_alignment}, coverage=improved, date={$CURRENT_DATE}", category: "architecture", tags: ["init", "gap-analysis", "agents", "{CURRENT_YEAR}"]}')
  OUTPUT(Generation summary with agent details, confidence scores, and industry alignment metrics)
→ END-IF
- `2`: IF(agents_generated === 0) →
  mcp__vector-memory__store_memory('{content: "Init Gap Analysis: mode={search_mode}, result=full_coverage, agents={agents_count}, date={$CURRENT_DATE}", category: "architecture", tags: ["init", "full-coverage", "{CURRENT_YEAR}"]}')
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
Graceful degradation
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