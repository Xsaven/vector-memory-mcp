---
name: init-agents
description: "Incremental Agent Gap Analyzer - Auto-generates missing domain agents (project)"
---

<command>
<meta>
<id>init-agents</id>
<description>Incremental Agent Gap Analyzer - Auto-generates missing domain agents (project)</description>
</meta>
<purpose>Auto-analyze
.claude/CLAUDE.md
and existing agents → identify gaps → generate missing agents via
brain make:master
→ safe for repeated runs. Supports optional $ARGUMENTS for targeted search.</purpose>
<iron_rules>
<rule id="no-interactive-questions" severity="critical">
<text>NO interactive questions</text>
<why>Automated workflow for gap analysis and generation</why>
<on_violation>Execute fully automated without user prompts</on_violation>
</rule>
<rule id="temporal-context-first" severity="critical">
<text>Temporal context FIRST: Bash('date')</text>
<why>Ensures up-to-date best practices in generated artifacts</why>
<on_violation>Missing temporal context leads to outdated patterns</on_violation>
</rule>
<rule id="brain-make-master-only" severity="critical">
<text>MUST use Bash('brain make:master') for agent creation - NOT Write() or Edit()</text>
<why>brain make:master ensures proper PHP archetype structure and compilation compatibility</why>
<on_violation>Manually created agents may have structural issues and compilation errors</on_violation>
</rule>
<rule id="no-regeneration" severity="critical">
<text>No regeneration of existing agents</text>
<why>Idempotent operation safe for repeated execution</why>
<on_violation>Wasted computation and potential conflicts</on_violation>
</rule>
<rule id="delegates-web-research" severity="high">
<text>Brain MUST delegate all web research to WebResearchMaster, never execute WebSearch directly</text>
<why>Maintains delegation hierarchy and prevents Brain from performing execution-level tasks</why>
<on_violation>Delegation protocol violation - escalate to Architect Agent</on_violation>
</rule>
<rule id="cache-web-results" severity="high">
<text>Store web research patterns in vector memory for 30 days to speed up repeated runs</text>
<why>Avoids redundant web searches and improves performance</why>
<on_violation>Unnecessary web API calls and slower execution</on_violation>
</rule>
</iron_rules>
<guidelines>
<guideline id="phase0-arguments-processing">
GOAL(Process optional user arguments to narrow search scope and improve targeting)
<example>
<phase name="1">Parse $ARGUMENTS for specific domain/technology/agent hints</phase>
<phase name="2">IF($ARGUMENTS provided) → THEN → [Extract: target_domain (e.g., "Laravel", "React", "API"), target_technology, specific_agents → STORE-AS($ = '{domain: ..., tech: ..., agents: [...], keywords: [...]}') → Set search_mode = "targeted" → Log: "Targeted mode: focusing on {domain}/{tech}"] → END-IF</phase>
<phase name="3">IF($ARGUMENTS empty) → THEN → [Set search_mode = "discovery" → Use full project analysis workflow → Log: "Discovery mode: full project analysis"] → END-IF</phase>
<phase name="4">Store search mode for use in subsequent phases</phase>
</example>
</guideline>
<guideline id="phase1-temporal-context-and-web-cache">
GOAL(Get current date/year for temporal context AND check vector memory cache for recent patterns)
<example>
<phase name="1">Bash(date +"%Y-%m-%d") → [STORE-AS($)] → END-Bash</phase>
<phase name="2">Bash(date +"%Y") → [STORE-AS($)] → END-Bash</phase>
<phase name="3">PARALLEL: Check vector memory cache while temporal context loads</phase>
<phase name="4">mcp__vector-memory__search_memories('{query: "multi-agent architecture patterns", category: "learning", limit: 3}')</phase>
<phase name="5">IF(cache_hit AND cache_age < 30 days) → THEN → [STORE-AS($ = 'Cached industry patterns from vector memory') → STORE-AS($ = 'true') → STORE-AS($ = '{days}') → Log: "Using cached patterns (age: {days} days)"] → END-IF</phase>
<phase name="6">IF(no_cache OR cache_old) → THEN → [STORE-AS($ = 'false') → Log: "Fresh web search required"] → END-IF</phase>
</example>
</guideline>
<guideline id="phase1.5-web-search-best-practices">

GOAL(Delegate industry best practices research to WebResearchMaster with cache awareness)
NOTE(Delegated to WebResearchMaster for industry research)

<example>
<phase name="1">IF(search_mode === "discovery") → THEN → [Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [IF(cache_valid === true) → THEN → Use cached patterns, skip web search → END-IF → IF(cache_valid === false) → THEN → [ →   Research: multi-agent system architecture best practices {year} →   Research: AI agent orchestration patterns {year} →   Research: domain-driven agent design principles {year} →   Synthesize findings into unified patterns → ] → END-IF] → END-TASK', 'OUTPUT({architecture: [...], orchestration: [...], domain_design: [...], sources: [...], cache_used: true|false})', 'STORE-AS($)') → IF(fresh research performed) → Store results in vector memory → mcp__vector-memory__store_memory('{content: $INDUSTRY_PATTERNS, category: "learning", tags: ["agent-patterns", "best-practices", "{CURRENT_YEAR}"]}')] → END-IF</phase>
<phase name="2">IF(search_mode === "targeted") → THEN → [Use $CACHED_PATTERNS from phase 1 if available → Log: "Targeted mode - using cached patterns, skipping general research"] → END-IF</phase>
</example>
</guideline>
<guideline id="phase2-inventory-agents">
GOAL(List all existing agents via brain list:masters)
<example>
<phase name="1">Bash('brain list:masters') Parse output</phase>
<phase name="2">STORE-AS($ = '[{id, name, description}, ...]')</phase>
<phase name="3">Agents located in .brain/node/Agents/*.php</phase>
<phase name="4">Count: total_agents = count($EXISTING_AGENTS)</phase>
</example>
</guideline>
<guideline id="phase3-read-project-stack">
GOAL(Extract project technology stack with optional filtering based on $ARGUMENTS)
<example>Task(@agent-, 'TASK → [(IF(search_mode === "targeted") → THEN → [Priority 1: Focus exploration on $SEARCH_FILTER.domain and $SEARCH_FILTER.tech → Priority 2: Validate against project documentation (.docs/, CLAUDE.md) → Priority 3: Extract related technologies and dependencies] → END-IF + IF(search_mode === "discovery") → THEN → [Priority 1: Explore .docs/ directory if exists. Find all *.md files. → Priority 2: Extract: technologies, frameworks, services, domain requirements → Priority 3: Explore project files in ./ for tech stack (composer.json, package.json, etc.)] → END-IF + STORE-AS($ = \'{technologies: [...], frameworks: [...], services: [...], domain_requirements: [...], primary_stack: "...", confidence: 0-1}\'))] → END-TASK')</example>
</guideline>
<guideline id="phase3.5-stack-specific-search">

GOAL(Delegate technology-specific research to WebResearchMaster based on discovered stack)
NOTE(Delegated to WebResearchMaster for technology-specific patterns)

<example>
<phase name="1">Extract primary technologies from $PROJECT_STACK (max 3 most important)</phase>
<phase name="2">Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [Extract top 3 most important technologies from stack → IF(search_mode === "targeted") → THEN → Focus on $SEARCH_FILTER tech → END-IF → FOREACH(technology in top_3_technologies) → [ →   IF(technology is major framework/language) → THEN → [ →     Research: {technology} specialized agents best practices {year} →     Research: {technology} multi-agent architecture examples {year} →     Extract: common patterns, agent types, use cases →   ] → END-IF → ] → END-FOREACH → Synthesize per-technology patterns] → END-TASK', 'OUTPUT({tech_patterns: {Laravel: [...], React: [...]}, tech_examples: {...}})', 'STORE-AS($)')</phase>
<phase name="3">Cache technology patterns in vector memory</phase>
<phase name="4">mcp__vector-memory__store_memory('{content: $TECH_PATTERNS, category: "learning", tags: ["tech-patterns", $PROJECT_STACK.primary_stack, "{CURRENT_YEAR}"]}')</phase>
<phase name="5">IF(search_mode === "targeted") → THEN → [Log: "Found {count} patterns for {$SEARCH_FILTER.tech}" → Boost relevance score for matching patterns] → END-IF</phase>
</example>
</guideline>
<guideline id="phase4-gap-analysis-enhanced">
GOAL(Identify missing domain agents with industry best practices validation and confidence scoring)
<example>
<phase name="1">First pass: Web-informed gap analysis</phase>
<phase name="2">Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [Gather best practices for agent coverage for the given project stack → Cross-reference with industry patterns from web search → Consider technology-specific agent requirements → IF(search_mode === "targeted") → THEN → [Focus on $SEARCH_FILTER domains only] → END-IF] → END-TASK', 'OUTPUT({covered_domains: [...], missing_agents: [{name: \'AgentName\', purpose: \'...\', capabilities: [...], industry_alignment: 0-1}], confidence: 0-1})')</phase>
<phase name="3">STORE-AS($)</phase>
<phase name="4">Second pass: Deep agent-level analysis with industry validation</phase>
<phase name="5">Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [Analyze domain expertise needed based on Project requirements → Compare with existing agents → Cross-validate against industry best practices → Validate each proposed agent against INDUSTRY_PATTERNS → Assign confidence score (0-1) to each missing agent recommendation → Prioritize critical gaps with high industry alignment → IF(search_mode === "targeted") → THEN → [Validate $SEARCH_FILTER.agents against project needs] → END-IF → FOREACH(missing domain) → [WebSearch({domain} agent architecture {current_year})] → END-FOREACH] → END-TASK', 'OUTPUT({covered_domains: [...], missing_agents: [{name: \'AgentName\', purpose: \'...\', capabilities: [...], confidence: 0-1, industry_alignment: 0-1, priority: "critical|high|medium"}], industry_coverage_score: 0-1})', 'NOTE(Focus on critical domain gaps with high confidence and industry alignment)')</phase>
<phase name="6">STORE-AS($)</phase>
<phase name="7">Filter: Only include agents with confidence >= 0.75 AND industry_alignment >= 0.7</phase>
<phase name="8">Sort by: priority DESC, confidence DESC, industry_alignment DESC</phase>
</example>
</guideline>
<guideline id="phase5-generate-agents">
GOAL(Create missing agents (sequential, 1 by 1) with confidence and industry alignment metadata)
<example>

NOTE(Created files must be valid PHP archetypes extending BrainCore\Archetypes\AgentArchetype)
FOREACH(agent in $GAP_ANALYSIS.missing_agents) → [IF(agent already exists in $EXISTING_AGENTS) → THEN → [SKIP(idempotent - preserving existing)] → END-IF → IF(agent.confidence < 0.75 OR agent.priority === "medium") → THEN → [Log: "Skipping low-confidence agent: {agent.name} (confidence: {agent.confidence})" → SKIP(low confidence or medium priority)] → END-IF → Log: "Generating {agent.name} (confidence: {agent.confidence}, industry_alignment: {agent.industry_alignment}, priority: {agent.priority})" → Bash(brain make:master {AgentName}) → [Creates .brain/node/Agents/{AgentName}.php] → END-Bash → Task(@agent-, 'TASK → [Read(\'.brain/node/Agents/{AgentName}.php\') → Update Purpose attribute based on gap analysis → Include industry best practices from $INDUSTRY_PATTERNS and $TECH_PATTERNS → Add appropriate includes (Universal + custom if needed) → Define agent-specific guidelines and capabilities → Follow existing agent structure (AgentMaster, CommitMaster, etc.) → Add metadata comment: confidence={agent.confidence}, industry_alignment={agent.industry_alignment}] → END-TASK', 'CONTEXT({agent_purpose}, {agent_capabilities} from gap analysis Industry patterns: {$INDUSTRY_PATTERNS} Technology patterns: {$TECH_PATTERNS[relevant_tech]} Confidence score: {agent.confidence})') → REPORT({completed}/{total} agents generated (avg confidence: {avg_confidence}))] → END-FOREACH

<phase name="1">Store generation summary</phase>
<phase name="2">STORE-AS($ = '{generated: [...], skipped: [...], avg_confidence: 0-1, total_agents: count}')</phase>
</example>
</guideline>
<guideline id="phase6-compile">
GOAL(Compile all agents to .claude/agents/)
<example>
<phase name="1">Bash(brain compile) → [Compiles .brain/node/Agents/*.php to .claude/agents/] → END-Bash</phase>
<phase name="2">VERIFY-SUCCESS(CHECK(.claude/agents/ for new agent files) Compilation completed without errors)</phase>
<phase name="3">Log: "Compilation complete. New agents available in {AGENTS_FOLDER}"</phase>
</example>
</guideline>
<guideline id="phase7-report-enhanced">
GOAL(Report generation results with confidence scores, industry alignment, and caching status)
<example>
<phase name="1">IF(agents_generated > 0) → THEN → [Calculate: avg_confidence = average(generated_agents.confidence) → Calculate: avg_industry_alignment = average(generated_agents.industry_alignment) → mcp__vector-memory__store_memory('{content: "Init Gap Analysis: mode={search_mode}, technologies={$PROJECT_STACK.technologies}, agents_generated={agents_count}, avg_confidence={avg_confidence}, avg_industry_alignment={avg_industry_alignment}, coverage=improved, date={$CURRENT_DATE}", category: "architecture", tags: ["init", "gap-analysis", "agents", "{CURRENT_YEAR}"]}') → OUTPUT(Generation summary with agent details, confidence scores, and industry alignment metrics)] → END-IF</phase>
<phase name="2">IF(agents_generated === 0) → THEN → [mcp__vector-memory__store_memory('{content: "Init Gap Analysis: mode={search_mode}, result=full_coverage, agents={agents_count}, date={$CURRENT_DATE}", category: "architecture", tags: ["init", "full-coverage", "{CURRENT_YEAR}"]}') → OUTPUT(Full coverage confirmation with existing agent list and industry coverage score)] → END-IF</phase>
<phase name="3">Include cache performance metrics: {cache_hits}, {web_searches_performed}</phase>
</example>
</guideline>
<guideline id="response-format-a-enhanced">
<text>Response when Agents Generated</text>
<example>Init Gap Analysis Complete</example>
<example>Mode: {search_mode} (targeted|discovery)</example>
<example>
Agents Generated: {agents_count}
<phase name="Created in">.brain/node/Agents/</phase>
<phase name="Compiled to">.claude/agents/</phase>
</example>
<example>New domains: {list_of_new_agent_names}</example>
<example>
Quality Metrics:
<phase name="1">Average confidence: {avg_confidence} (0-1 scale)</phase>
<phase name="2">Average industry alignment: {avg_industry_alignment} (0-1 scale)</phase>
<phase name="3">Priority breakdown: {critical_count} critical, {high_count} high</phase>
</example>
<example>
Coverage Improved:
<phase name="1">Technologies: {technologies_list}</phase>
<phase name="2">Industry coverage score: {industry_coverage_score} (0-1 scale)</phase>
<phase name="3">Total agents: {total_agents_count} (was: {old_count})</phase>
</example>
<example>Preserved: {existing_agents_count} existing agents</example>
<example>
Performance:
<phase name="1">Cache hits: {cache_hits}</phase>
<phase name="2">Web searches: {web_searches_count} (delegated to WebResearchMaster)</phase>
<phase name="3">Skipped agents: {skipped_count} (low confidence or medium priority)</phase>
</example>
<example>
Next Steps:
<phase name="1">Review generated agents in .brain/node/Agents/</phase>
<phase name="2">Customize agent capabilities if needed</phase>
<phase name="3">Recompile: brain compile (if customized)</phase>
<phase name="4">Agents are ready to use via Task(@agent- '...')</phase>
</example>
</guideline>
<guideline id="response-format-b-enhanced">
<text>Response when Full Coverage (no gaps detected)</text>
<example>Init Gap Analysis Complete</example>
<example>Mode: {search_mode} (targeted|discovery)</example>
<example>Status: Full domain coverage</example>
<example>Existing Agents: {agents_count}</example>
<example>{list_existing_agents_with_descriptions}</example>
<example>
Stack Coverage:
<phase name="1">Brain requirements: COVERED</phase>
<phase name="2">Project stack: {technologies_list}</phase>
<phase name="3">Domain expertise: COMPLETE</phase>
<phase name="4">Industry coverage score: {industry_coverage_score} (0-1 scale)</phase>
</example>
<example>
Industry Validation:
<phase name="1">Multi-agent patterns: ALIGNED</phase>
<phase name="2">Technology-specific agents: ALIGNED</phase>
<phase name="3">Best practices compliance: {compliance_score}</phase>
</example>
<example>
Performance:
<phase name="1">Cache hits: {cache_hits}</phase>
<phase name="2">Web searches: {web_searches_count} (delegated to WebResearchMaster)</phase>
</example>
<example>No new agents needed → System ready</example>
</guideline>
<guideline id="memory-optimization">
<text>Cache web research results for faster repeated runs</text>
<example>
<phase name="1">Before web search: Check vector memory for cached patterns</phase>
<phase name="2">Query patterns: "multi-agent architecture patterns", "{tech} agent patterns"</phase>
<phase name="3">Cache TTL: 30 days for industry patterns, 14 days for technology patterns</phase>
<phase name="4">IF(cache_hit AND cache_age < TTL) → THEN → [Use cached patterns → Pass cache context to WebResearchMaster → Log: "Cache hit: {pattern_type} (age: {days} days)" → WebResearchMaster skips web search] → END-IF</phase>
<phase name="5">IF(cache_miss OR cache_expired) → THEN → [Delegate to WebResearchMaster for fresh research → Store results with category: "learning" → Tag: ["agent-patterns", "best-practices", "{tech}", "{CURRENT_YEAR}"] → Log: "Cache miss: performing web search via WebResearchMaster"] → END-IF</phase>
<phase name="6">Post-analysis: Store gap analysis results for project context</phase>
<phase name="7">mcp__vector-memory__store_memory('{content: "Gap analysis for {project}: {summary}", category: "architecture", tags: ["gap-analysis", "{technologies}"]}')</phase>
</example>
</guideline>
<guideline id="error-recovery-enhanced">
<text>Error handling scenarios with graceful degradation</text>
<example>
<phase name="1">IF(no .docs/ found) → THEN → [Use Brain context only → Continue with gap analysis → Log: "No .docs/ - using Brain context"] → END-IF</phase>
<phase name="2">IF(agent already exists) → THEN → [SKIP(generation) → LOG as preserved → Continue with next agent] → END-IF</phase>
<phase name="3">IF(brain make:master fails) → THEN → [LOG error → SKIP(this agent) → Continue with remaining agents] → END-IF</phase>
<phase name="4">IF(brain compile fails) → THEN → [Report compilation errors → List failed agents → Manual intervention required] → END-IF</phase>
<phase name="5">IF(@agent- fails) → THEN → [Report error → Suggest manual agent creation → Continue with next agent] → END-IF</phase>
<phase name="6">IF(web search timeout) → THEN → [WebResearchMaster handles timeout internally → Falls back to vector memory cached patterns → Continues with available data → Marks analysis as "partial" in report → Log: "Web search timeout - using cached data only"] → END-IF</phase>
<phase name="7">IF(no internet connection) → THEN → [WebResearchMaster reports unavailable → Skip all web search phases → Use local project analysis only → Use cached patterns from vector memory → Warn user: "Limited coverage validation - no internet connection" → Continue with reduced confidence scores (-0.2 penalty)] → END-IF</phase>
<phase name="8">IF(vector memory unavailable) → THEN → [Skip caching operations → WebResearchMaster performs all web searches (no cache hits) → Continue without storing results → Log: "Vector memory unavailable - no caching"] → END-IF</phase>
<phase name="9">IF(low confidence for all proposed agents) → THEN → [Request additional context from user → Suggest manual review of project requirements → Output: "Unable to confidently identify missing agents. Manual review recommended."] → END-IF</phase>
<phase name="10">REPORT({successful_count}/{total_count} agents generated (avg confidence: {avg_confidence}))</phase>
</example>
</guideline>
<guideline id="quality-gates-enhanced">
<text>Quality validation checkpoints with confidence thresholds</text>
<example>Gate 1: Temporal context retrieved (date/year)</example>
<example>Gate 2: Vector memory cache checked for recent patterns</example>
<example>Gate 3: brain list:masters executed successfully</example>
<example>Gate 4: Web research delegated to WebResearchMaster OR cache hit</example>
<example>Gate 5: Gap analysis completed with valid output structure</example>
<example>Gate 6: Gap analysis includes confidence scores >= 0.75 for critical agents</example>
<example>Gate 7: Industry alignment scores >= 0.7 for all proposed agents</example>
<example>Gate 8: brain make:master creates valid PHP archetype</example>
<example>Gate 9: brain compile completes without errors</example>
<example>Gate 10: Generated agents appear in .claude/agents/</example>
<example>Gate 11: Generation summary includes quality metrics (confidence, industry_alignment)</example>
</guideline>
<guideline id="example-1-targeted-mode">
SCENARIO(User provides: "missing agent for Laravel" → Targeted mode)
<example>
<phase name="input">$ARGUMENTS = "missing agent for Laravel"</phase>
<phase name="parse">target_domain = "Laravel", search_mode = "targeted"</phase>
<phase name="delegation">WebResearchMaster: Focus on Laravel-specific agent patterns</phase>
<phase name="result">Gap detected: Laravel expertise missing (confidence: 0.92, industry_alignment: 0.88)</phase>
<phase name="action">brain make:master LaravelMaster → (Edit Purpose with industry patterns + Guidelines from best practices) → Compile</phase>
<phase name="output">LaravelMaster agent available (confidence: 0.92)</phase>
</example>
</guideline>
<guideline id="example-2-discovery-mode">
SCENARIO(No arguments → Full discovery mode with web research delegation)
<example>
<phase name="input">$ARGUMENTS empty, search_mode = "discovery"</phase>
<phase name="delegation">WebResearchMaster: Industry patterns for multi-agent architecture (2025)</phase>
<phase name="analysis">Project uses React + Node.js, no React agent exists</phase>
<phase name="validation">Industry patterns confirm: Frontend specialization needed (confidence: 0.87)</phase>
<phase name="action">brain make:master ReactMaster → Compile</phase>
<phase name="result">ReactMaster agent available via Task(@agent- '...') (confidence: 0.87, industry_alignment: 0.85)</phase>
</example>
</guideline>
<guideline id="example-3-cache-hit">
SCENARIO(Repeated run with cached patterns)
<example>
<phase name="input">Second run within 30 days</phase>
<phase name="cache">Cache hit: "multi-agent architecture patterns" (age: 5 days)</phase>
<phase name="delegation">WebResearchMaster: Skip web search, use cached patterns</phase>
<phase name="performance">No web searches needed, used cached data</phase>
<phase name="analysis">All domains covered by existing agents</phase>
<phase name="result">REPORT("No gaps detected → System ready" with agent list (cache_hits: 1, web_searches: 0, delegation_count: 2))</phase>
</example>
</guideline>
<guideline id="example-4-low-confidence-filter">
SCENARIO(Gap analysis with low confidence agents filtered out)
<example>
<phase name="input">Full discovery mode</phase>
<phase name="delegation">WebResearchMaster + AgentMaster: Comprehensive gap analysis</phase>
<phase name="analysis">Found 5 potential gaps: 3 high confidence (>0.75), 2 low confidence (<0.75)</phase>
<phase name="filter">Removed 2 low-confidence agents</phase>
<phase name="generation">Generated 3 agents with avg confidence: 0.84</phase>
<phase name="output">Report: "3 agents generated, 2 skipped (low confidence), delegation_count: 3"</phase>
</example>
</guideline>
<guideline id="directive">
<text>Generate ONLY missing agents! Preserve existing! Use brain make:master ONLY! Delegate web research to WebResearchMaster! Cache patterns! Validate with industry standards! Report confidence scores! Compile after generation!</text>
</guideline>
</guidelines>
</command>