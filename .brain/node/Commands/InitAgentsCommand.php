<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Compilation\BrainCLI;
use BrainCore\Compilation\Operator;
use BrainCore\Compilation\Runtime;
use BrainCore\Compilation\Store;
use BrainCore\Compilation\Tools\BashTool;
use BrainCore\Compilation\Tools\ReadTool;
use BrainCore\Compilation\Tools\TaskTool;
use BrainCore\Compilation\Tools\WebSearchTool;
use BrainNode\Agents\AgentMaster;
use BrainNode\Agents\ExploreMaster;
use BrainNode\Agents\WebResearchMaster;
use BrainNode\Mcp\VectorMemoryMcp;

#[Meta('id', 'init-agents')]
#[Meta('description', 'Incremental Agent Gap Analyzer - Auto-generates missing domain agents (project)')]
#[Purpose(['Auto-analyze', Runtime::BRAIN_FILE, 'and existing agents → identify gaps → generate missing agents via', BrainCLI::MAKE_MASTER, '→ safe for repeated runs. Supports optional $ARGUMENTS for targeted search.'])]
class InitAgentsCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {
        // Iron Rules
        $this->rule('no-interactive-questions')->critical()
            ->text('NO interactive questions')
            ->why('Automated workflow for gap analysis and generation')
            ->onViolation('Execute fully automated without user prompts');

        $this->rule('temporal-context-first')->critical()
            ->text(['Temporal context FIRST:', BashTool::call('date')])
            ->why('Ensures up-to-date best practices in generated artifacts')
            ->onViolation('Missing temporal context leads to outdated patterns');

        $this->rule('brain-make-master-only')->critical()
            ->text(['MUST use', BashTool::call(BrainCLI::MAKE_MASTER), 'for agent creation - NOT Write() or Edit()'])
            ->why([BrainCLI::MAKE_MASTER, 'ensures proper PHP archetype structure and compilation compatibility'])
            ->onViolation('Manually created agents may have structural issues and compilation errors');

        $this->rule('no-regeneration')->critical()
            ->text('No regeneration of existing agents')
            ->why('Idempotent operation safe for repeated execution')
            ->onViolation('Wasted computation and potential conflicts');

        $this->rule('delegates-web-research')->high()
            ->text('Brain MUST delegate all web research to WebResearchMaster, never execute WebSearch directly')
            ->why('Maintains delegation hierarchy and prevents Brain from performing execution-level tasks')
            ->onViolation('Delegation protocol violation - escalate to Architect Agent');

        $this->rule('cache-web-results')->high()
            ->text('Store web research patterns in vector memory for 30 days to speed up repeated runs')
            ->why('Avoids redundant web searches and improves performance')
            ->onViolation('Unnecessary web API calls and slower execution');

        // Phase 0: Arguments Processing
        $this->guideline('phase0-arguments-processing')
            ->goal('Process optional user arguments to narrow search scope and improve targeting')
            ->example()
            ->phase('Parse $ARGUMENTS for specific domain/technology/agent hints')
            ->phase(Operator::if('$ARGUMENTS provided', [
                'Extract: target_domain (e.g., "Laravel", "React", "API"), target_technology, specific_agents',
                Store::as('SEARCH_FILTER', '{domain: ..., tech: ..., agents: [...], keywords: [...]}'),
                'Set search_mode = "targeted"',
                'Log: "Targeted mode: focusing on {domain}/{tech}"'
            ]))
            ->phase(Operator::if('$ARGUMENTS empty', [
                'Set search_mode = "discovery"',
                'Use full project analysis workflow',
                'Log: "Discovery mode: full project analysis"'
            ]))
            ->phase('Store search mode for use in subsequent phases');

        // Phase 1: Get Temporal Context + Parallel Web Search
        $this->guideline('phase1-temporal-context-and-web-cache')
            ->goal('Get current date/year for temporal context AND check vector memory cache for recent patterns')
            ->example()
            ->phase(BashTool::describe('date +"%Y-%m-%d"', Store::as('CURRENT_DATE')))
            ->phase(BashTool::describe('date +"%Y"', Store::as('CURRENT_YEAR')))
            ->phase('PARALLEL: Check vector memory cache while temporal context loads')
            ->phase(VectorMemoryMcp::call('search_memories', '{query: "multi-agent architecture patterns", category: "learning", limit: 3}'))
            ->phase(Operator::if('cache_hit AND cache_age < 30 days', [
                Store::as('CACHED_PATTERNS', 'Cached industry patterns from vector memory'),
                Store::as('CACHE_VALID', 'true'),
                Store::as('CACHE_AGE', '{days}'),
                'Log: "Using cached patterns (age: {days} days)"'
            ]))
            ->phase(Operator::if('no_cache OR cache_old', [
                Store::as('CACHE_VALID', 'false'),
                'Log: "Fresh web search required"'
            ]));

        // Phase 1.5: Web Search - Best Practices (DELEGATED TO WebResearchMaster)
        $this->guideline('phase1.5-web-search-best-practices')
            ->goal('Delegate industry best practices research to WebResearchMaster with cache awareness')
            ->note('Delegated to WebResearchMaster for industry research')
            ->example()
            ->phase(Operator::if('search_mode === "discovery"', [
                WebResearchMaster::call(
                    Operator::input(
                        Store::get('CURRENT_YEAR'),
                        Store::get('CACHED_PATTERNS'),
                        Store::get('CACHE_VALID'),
                        Store::get('CACHE_AGE'),
                    ),
                    Operator::task(
                        'IF(cache_valid === true) → THEN → Use cached patterns, skip web search → END-IF',
                        'IF(cache_valid === false) → THEN → [',
                        '  Research: multi-agent system architecture best practices {year}',
                        '  Research: AI agent orchestration patterns {year}',
                        '  Research: domain-driven agent design principles {year}',
                        '  Synthesize findings into unified patterns',
                        '] → END-IF',
                    ),
                    Operator::output('{architecture: [...], orchestration: [...], domain_design: [...], sources: [...], cache_used: true|false}'),
                    Store::as('INDUSTRY_PATTERNS')
                ),
                'IF(fresh research performed) → Store results in vector memory',
                VectorMemoryMcp::call('store_memory', '{content: $INDUSTRY_PATTERNS, category: "learning", tags: ["agent-patterns", "best-practices", "{CURRENT_YEAR}"]}')
            ]))
            ->phase(Operator::if('search_mode === "targeted"', [
                'Use $CACHED_PATTERNS from phase 1 if available',
                'Log: "Targeted mode - using cached patterns, skipping general research"'
            ]));

        // Phase 2: Inventory Existing Agents
        $this->guideline('phase2-inventory-agents')
            ->goal('List all existing agents via', BrainCLI::LIST_MASTERS)
            ->example()
            ->phase([BashTool::call(BrainCLI::LIST_MASTERS), 'Parse output'])
            ->phase(Store::as('EXISTING_AGENTS', '[{id, name, description}, ...]'))
            ->phase(['Agents located in', Runtime::NODE_DIRECTORY('Agents/*.php')])
            ->phase('Count: total_agents = count($EXISTING_AGENTS)');

        // Phase 3: Extract Project Stack (DELEGATED - ENHANCED with search filter)
        $this->guideline('phase3-read-project-stack')
            ->goal('Extract project technology stack with optional filtering based on $ARGUMENTS')
            ->example(
                ExploreMaster::call(Operator::task([
                    Operator::if('search_mode === "targeted"', [
                        'Priority 1: Focus exploration on $SEARCH_FILTER.domain and $SEARCH_FILTER.tech',
                        'Priority 2: Validate against project documentation (.docs/, CLAUDE.md)',
                        'Priority 3: Extract related technologies and dependencies'
                    ]),
                    Operator::if('search_mode === "discovery"', [
                        'Priority 1: Explore .docs/ directory if exists. Find all *.md files.',
                        'Priority 2: Extract: technologies, frameworks, services, domain requirements',
                        'Priority 3: Explore project files in ./ for tech stack (composer.json, package.json, etc.)'
                    ]),
                    Store::as('PROJECT_STACK', '{technologies: [...], frameworks: [...], services: [...], domain_requirements: [...], primary_stack: "...", confidence: 0-1}'),
                ]))
            );

        // Phase 3.5: Stack-Specific Web Search (DELEGATED TO WebResearchMaster)
        $this->guideline('phase3.5-stack-specific-search')
            ->goal('Delegate technology-specific research to WebResearchMaster based on discovered stack')
            ->note('Delegated to WebResearchMaster for technology-specific patterns')
            ->example()
            ->phase('Extract primary technologies from $PROJECT_STACK (max 3 most important)')
            ->phase(
                WebResearchMaster::call(
                    Operator::input(
                        Store::get('PROJECT_STACK.technologies'),
                        Store::get('CURRENT_YEAR'),
                        Store::get('SEARCH_FILTER'),
                        Store::get('SEARCH_MODE'),
                    ),
                    Operator::task(
                        'Extract top 3 most important technologies from stack',
                        'IF(search_mode === "targeted") → THEN → Focus on $SEARCH_FILTER tech → END-IF',
                        'FOREACH(technology in top_3_technologies) → [',
                        '  IF(technology is major framework/language) → THEN → [',
                        '    Research: {technology} specialized agents best practices {year}',
                        '    Research: {technology} multi-agent architecture examples {year}',
                        '    Extract: common patterns, agent types, use cases',
                        '  ] → END-IF',
                        '] → END-FOREACH',
                        'Synthesize per-technology patterns',
                    ),
                    Operator::output('{tech_patterns: {Laravel: [...], React: [...]}, tech_examples: {...}}'),
                    Store::as('TECH_PATTERNS')
                )
            )
            ->phase('Cache technology patterns in vector memory')
            ->phase(VectorMemoryMcp::call('store_memory', '{content: $TECH_PATTERNS, category: "learning", tags: ["tech-patterns", $PROJECT_STACK.primary_stack, "{CURRENT_YEAR}"]}'))
            ->phase(Operator::if('search_mode === "targeted"', [
                'Log: "Found {count} patterns for {$SEARCH_FILTER.tech}"',
                'Boost relevance score for matching patterns'
            ]));

        // Phase 4: Enhanced Gap Analysis with Industry Validation
        $this->guideline('phase4-gap-analysis-enhanced')
            ->goal('Identify missing domain agents with industry best practices validation and confidence scoring')
            ->example()
            ->phase('First pass: Web-informed gap analysis')
            ->phase(
                WebResearchMaster::call(
                    Operator::input(
                        Store::get('EXISTING_AGENTS'),
                        Store::get('PROJECT_STACK'),
                        Store::get('INDUSTRY_PATTERNS'),
                        Store::get('TECH_PATTERNS'),
                        Store::get('CURRENT_YEAR'),
                        Store::get('SEARCH_FILTER'),
                    ),
                    Operator::task(
                        'Gather best practices for agent coverage for the given project stack',
                        'Cross-reference with industry patterns from web search',
                        'Consider technology-specific agent requirements',
                        Operator::if('search_mode === "targeted"', 'Focus on $SEARCH_FILTER domains only')
                    ),
                    Operator::output('{covered_domains: [...], missing_agents: [{name: \'AgentName\', purpose: \'...\', capabilities: [...], industry_alignment: 0-1}], confidence: 0-1}'),
                )
            )
            ->phase(Store::as('WEB_GAP_ANALYSIS'))
            ->phase('Second pass: Deep agent-level analysis with industry validation')
            ->phase(
                AgentMaster::call(
                    Operator::input(
                        Store::get('EXISTING_AGENTS'),
                        Store::get('PROJECT_STACK'),
                        Store::get('WEB_GAP_ANALYSIS'),
                        Store::get('INDUSTRY_PATTERNS'),
                        Store::get('TECH_PATTERNS'),
                        Store::get('CURRENT_YEAR'),
                        Store::get('SEARCH_FILTER'),
                    ),
                    Operator::task(
                        'Analyze domain expertise needed based on Project requirements',
                        'Compare with existing agents',
                        'Cross-validate against industry best practices',
                        'Validate each proposed agent against INDUSTRY_PATTERNS',
                        'Assign confidence score (0-1) to each missing agent recommendation',
                        'Prioritize critical gaps with high industry alignment',
                        Operator::if('search_mode === "targeted"', 'Validate $SEARCH_FILTER.agents against project needs'),
                        Operator::forEach('missing domain', WebSearchTool::describe('{domain} agent architecture {current_year}')),
                    ),
                    Operator::output('{covered_domains: [...], missing_agents: [{name: \'AgentName\', purpose: \'...\', capabilities: [...], confidence: 0-1, industry_alignment: 0-1, priority: "critical|high|medium"}], industry_coverage_score: 0-1}'),
                    Operator::note('Focus on critical domain gaps with high confidence and industry alignment'),
                )
            )
            ->phase(Store::as('GAP_ANALYSIS'))
            ->phase('Filter: Only include agents with confidence >= 0.75 AND industry_alignment >= 0.7')
            ->phase('Sort by: priority DESC, confidence DESC, industry_alignment DESC');

        // Phase 4.5: Validate Against Industry Standards (REMOVED - integrated into phase4)
        // This validation is now part of the AgentMaster delegation in phase4
        // AgentMaster can use WebSearchTool internally as part of its workflow

        // Phase 5: Generate Missing Agents (ENHANCED with confidence tracking)
        $this->guideline('phase5-generate-agents')
            ->goal('Create missing agents (sequential, 1 by 1) with confidence and industry alignment metadata')
            ->example()
            ->note('Created files must be valid PHP archetypes extending', AgentArchetype::class)
            ->forEach('agent in $GAP_ANALYSIS.missing_agents', [
                Operator::if('agent already exists in $EXISTING_AGENTS', Operator::skip('idempotent - preserving existing')),
                Operator::if('agent.confidence < 0.75 OR agent.priority === "medium"', [
                    'Log: "Skipping low-confidence agent: {agent.name} (confidence: {agent.confidence})"',
                    Operator::skip('low confidence or medium priority')
                ]),
                'Log: "Generating {agent.name} (confidence: {agent.confidence}, industry_alignment: {agent.industry_alignment}, priority: {agent.priority})"',
                BashTool::describe(BrainCLI::MAKE_MASTER('{AgentName}'), ['Creates', Runtime::NODE_DIRECTORY('Agents/{AgentName}.php')]),
                AgentMaster::call(
                    Operator::task(
                        ReadTool::call(Runtime::NODE_DIRECTORY('Agents/{AgentName}.php')),
                        'Update Purpose attribute based on gap analysis',
                        'Include industry best practices from $INDUSTRY_PATTERNS and $TECH_PATTERNS',
                        'Add appropriate includes (Universal + custom if needed)',
                        'Define agent-specific guidelines and capabilities',
                        'Follow existing agent structure (AgentMaster, CommitMaster, etc.)',
                        'Add metadata comment: confidence={agent.confidence}, industry_alignment={agent.industry_alignment}',
                    ),
                    Operator::context([
                        '{agent_purpose}, {agent_capabilities} from gap analysis',
                        'Industry patterns: {$INDUSTRY_PATTERNS}',
                        'Technology patterns: {$TECH_PATTERNS[relevant_tech]}',
                        'Confidence score: {agent.confidence}',
                    ])
                ),
                Operator::report('{completed}/{total} agents generated (avg confidence: {avg_confidence})'),
            ])
            ->phase('Store generation summary')
            ->phase(Store::as('GENERATION_SUMMARY', '{generated: [...], skipped: [...], avg_confidence: 0-1, total_agents: count}'));

        // Phase 6: Compile Agents
        $this->guideline('phase6-compile')
            ->goal('Compile all agents to', Runtime::AGENTS_FOLDER)
            ->example()
            ->phase(BashTool::describe(BrainCLI::COMPILE, ['Compiles', Runtime::NODE_DIRECTORY('Agents/*.php'), 'to', Runtime::AGENTS_FOLDER]))
            ->phase(Operator::verify(
                Operator::check(Runtime::AGENTS_FOLDER, 'for new agent files'),
                'Compilation completed without errors'
            ))
            ->phase('Log: "Compilation complete. New agents available in {AGENTS_FOLDER}"');

        // Phase 7: Report with Confidence Metrics
        $this->guideline('phase7-report-enhanced')
            ->goal('Report generation results with confidence scores, industry alignment, and caching status')
            ->example()
            ->phase()->if('agents_generated > 0', [
                'Calculate: avg_confidence = average(generated_agents.confidence)',
                'Calculate: avg_industry_alignment = average(generated_agents.industry_alignment)',
                VectorMemoryMcp::call('store_memory', '{content: "Init Gap Analysis: mode={search_mode}, technologies={$PROJECT_STACK.technologies}, agents_generated={agents_count}, avg_confidence={avg_confidence}, avg_industry_alignment={avg_industry_alignment}, coverage=improved, date={$CURRENT_DATE}", category: "architecture", tags: ["init", "gap-analysis", "agents", "{CURRENT_YEAR}"]}'),
                Operator::output('Generation summary with agent details, confidence scores, and industry alignment metrics')
            ])
            ->phase()->if('agents_generated === 0', [
                VectorMemoryMcp::call('store_memory', '{content: "Init Gap Analysis: mode={search_mode}, result=full_coverage, agents={agents_count}, date={$CURRENT_DATE}", category: "architecture", tags: ["init", "full-coverage", "{CURRENT_YEAR}"]}'),
                Operator::output('Full coverage confirmation with existing agent list and industry coverage score')
            ])
            ->phase('Include cache performance metrics: {cache_hits}, {web_searches_performed}');

        // Response Format - Option A: Agents Generated
        $this->guideline('response-format-a-enhanced')
            ->text('Response when Agents Generated')
            ->example('Init Gap Analysis Complete')
            ->example('Mode: {search_mode} (targeted|discovery)')
            ->example('Agents Generated: {agents_count}')
            ->phase('Created in', Runtime::NODE_DIRECTORY('Agents/'))
            ->phase('Compiled to', Runtime::AGENTS_FOLDER)
            ->example('New domains: {list_of_new_agent_names}')
            ->example('Quality Metrics:')
            ->phase('Average confidence: {avg_confidence} (0-1 scale)')
            ->phase('Average industry alignment: {avg_industry_alignment} (0-1 scale)')
            ->phase('Priority breakdown: {critical_count} critical, {high_count} high')
            ->example('Coverage Improved:')
            ->phase('Technologies: {technologies_list}')
            ->phase('Industry coverage score: {industry_coverage_score} (0-1 scale)')
            ->phase('Total agents: {total_agents_count} (was: {old_count})')
            ->example('Preserved: {existing_agents_count} existing agents')
            ->example('Performance:')
            ->phase('Cache hits: {cache_hits}')
            ->phase('Web searches: {web_searches_count} (delegated to WebResearchMaster)')
            ->phase('Skipped agents: {skipped_count} (low confidence or medium priority)')
            ->example('Next Steps:')
            ->phase(['Review generated agents in', Runtime::NODE_DIRECTORY('Agents/')])
            ->phase('Customize agent capabilities if needed')
            ->phase('Recompile: brain compile (if customized)')
            ->phase(['Agents are ready to use via', TaskTool::agent('{name}', '...')]);

        // Response Format - Option B: Full Coverage
        $this->guideline('response-format-b-enhanced')
            ->text('Response when Full Coverage (no gaps detected)')
            ->example('Init Gap Analysis Complete')
            ->example('Mode: {search_mode} (targeted|discovery)')
            ->example('Status: Full domain coverage')
            ->example('Existing Agents: {agents_count}')
            ->example('{list_existing_agents_with_descriptions}')
            ->example('Stack Coverage:')
            ->phase('Brain requirements: COVERED')
            ->phase('Project stack: {technologies_list}')
            ->phase('Domain expertise: COMPLETE')
            ->phase('Industry coverage score: {industry_coverage_score} (0-1 scale)')
            ->example('Industry Validation:')
            ->phase('Multi-agent patterns: ALIGNED')
            ->phase('Technology-specific agents: ALIGNED')
            ->phase('Best practices compliance: {compliance_score}')
            ->example('Performance:')
            ->phase('Cache hits: {cache_hits}')
            ->phase('Web searches: {web_searches_count} (delegated to WebResearchMaster)')
            ->example()
            ->do('No new agents needed', 'System ready');

        // Memory Optimization
        $this->guideline('memory-optimization')
            ->text('Cache web research results for faster repeated runs')
            ->example()
            ->phase('Before web search: Check vector memory for cached patterns')
            ->phase('Query patterns: "multi-agent architecture patterns", "{tech} agent patterns"')
            ->phase('Cache TTL: 30 days for industry patterns, 14 days for technology patterns')
            ->phase(Operator::if('cache_hit AND cache_age < TTL', [
                'Use cached patterns',
                'Pass cache context to WebResearchMaster',
                'Log: "Cache hit: {pattern_type} (age: {days} days)"',
                'WebResearchMaster skips web search'
            ]))
            ->phase(Operator::if('cache_miss OR cache_expired', [
                'Delegate to WebResearchMaster for fresh research',
                'Store results with category: "learning"',
                'Tag: ["agent-patterns", "best-practices", "{tech}", "{CURRENT_YEAR}"]',
                'Log: "Cache miss: performing web search via WebResearchMaster"'
            ]))
            ->phase('Post-analysis: Store gap analysis results for project context')
            ->phase(VectorMemoryMcp::call('store_memory', '{content: "Gap analysis for {project}: {summary}", category: "architecture", tags: ["gap-analysis", "{technologies}"]}'));

        // Error Recovery
        $this->guideline('error-recovery-enhanced')
            ->text('Error handling scenarios with graceful degradation')
            ->example()
            ->phase()->if('no .docs/ found', ['Use Brain context only', 'Continue with gap analysis', 'Log: "No .docs/ - using Brain context"'])
            ->phase()->if('agent already exists', [Operator::skip('generation'), 'LOG as preserved', 'Continue with next agent'])
            ->phase()->if([BrainCLI::MAKE_MASTER, 'fails'], ['LOG error', Operator::skip('this agent'), 'Continue with remaining agents'])
            ->phase()->if([BrainCLI::COMPILE, 'fails'], ['Report compilation errors', 'List failed agents', 'Manual intervention required'])
            ->phase()->if([AgentMaster::id(), 'fails'], ['Report error', 'Suggest manual agent creation', 'Continue with next agent'])
            ->phase()->if('web search timeout', [
                'WebResearchMaster handles timeout internally',
                'Falls back to vector memory cached patterns',
                'Continues with available data',
                'Marks analysis as "partial" in report',
                'Log: "Web search timeout - using cached data only"'
            ])
            ->phase()->if('no internet connection', [
                'WebResearchMaster reports unavailable',
                'Skip all web search phases',
                'Use local project analysis only',
                'Use cached patterns from vector memory',
                'Warn user: "Limited coverage validation - no internet connection"',
                'Continue with reduced confidence scores (-0.2 penalty)'
            ])
            ->phase()->if('vector memory unavailable', [
                'Skip caching operations',
                'WebResearchMaster performs all web searches (no cache hits)',
                'Continue without storing results',
                'Log: "Vector memory unavailable - no caching"'
            ])
            ->phase()->if('low confidence for all proposed agents', [
                'Request additional context from user',
                'Suggest manual review of project requirements',
                'Output: "Unable to confidently identify missing agents. Manual review recommended."'
            ])
            ->phase()->report('{successful_count}/{total_count} agents generated (avg confidence: {avg_confidence})');

        // Quality Gates
        $this->guideline('quality-gates-enhanced')
            ->text('Quality validation checkpoints with confidence thresholds')
            ->example('Gate 1: Temporal context retrieved (date/year)')
            ->example('Gate 2: Vector memory cache checked for recent patterns')
            ->example('Gate 3: brain list:masters executed successfully')
            ->example('Gate 4: Web research delegated to WebResearchMaster OR cache hit')
            ->example('Gate 5: Gap analysis completed with valid output structure')
            ->example('Gate 6: Gap analysis includes confidence scores >= 0.75 for critical agents')
            ->example('Gate 7: Industry alignment scores >= 0.7 for all proposed agents')
            ->example('Gate 8: brain make:master creates valid PHP archetype')
            ->example('Gate 9: brain compile completes without errors')
            ->example(['Gate 10: Generated agents appear in', Runtime::AGENTS_FOLDER])
            ->example('Gate 11: Generation summary includes quality metrics (confidence, industry_alignment)');

        // Examples
        $this->guideline('example-1-targeted-mode')
            ->scenario('User provides: "missing agent for Laravel" → Targeted mode')
            ->example()
            ->phase('input', '$ARGUMENTS = "missing agent for Laravel"')
            ->phase('parse', 'target_domain = "Laravel", search_mode = "targeted"')
            ->phase('delegation', 'WebResearchMaster: Focus on Laravel-specific agent patterns')
            ->phase('result', 'Gap detected: Laravel expertise missing (confidence: 0.92, industry_alignment: 0.88)')
            ->phase()->name('action')
            ->do(BrainCLI::MAKE_MASTER('LaravelMaster'), ['Edit Purpose with industry patterns', 'Guidelines from best practices'], 'Compile')
            ->phase('output', 'LaravelMaster agent available (confidence: 0.92)');

        $this->guideline('example-2-discovery-mode')
            ->scenario('No arguments → Full discovery mode with web research delegation')
            ->example()
            ->phase('input', '$ARGUMENTS empty, search_mode = "discovery"')
            ->phase('delegation', 'WebResearchMaster: Industry patterns for multi-agent architecture (2025)')
            ->phase('analysis', 'Project uses React + Node.js, no React agent exists')
            ->phase('validation', 'Industry patterns confirm: Frontend specialization needed (confidence: 0.87)')
            ->phase('action', Operator::do(BrainCLI::MAKE_MASTER('ReactMaster'), 'Compile'))
            ->phase('result', ['ReactMaster agent available via', TaskTool::agent('react-master', '...'), '(confidence: 0.87, industry_alignment: 0.85)']);

        $this->guideline('example-3-cache-hit')
            ->scenario('Repeated run with cached patterns')
            ->example()
            ->phase('input', 'Second run within 30 days')
            ->phase('cache', 'Cache hit: "multi-agent architecture patterns" (age: 5 days)')
            ->phase('delegation', 'WebResearchMaster: Skip web search, use cached patterns')
            ->phase('performance', 'No web searches needed, used cached data')
            ->phase('analysis', 'All domains covered by existing agents')
            ->phase()
            ->name('result')
            ->report('"No gaps detected → System ready" with agent list (cache_hits: 1, web_searches: 0, delegation_count: 2)');

        $this->guideline('example-4-low-confidence-filter')
            ->scenario('Gap analysis with low confidence agents filtered out')
            ->example()
            ->phase('input', 'Full discovery mode')
            ->phase('delegation', 'WebResearchMaster + AgentMaster: Comprehensive gap analysis')
            ->phase('analysis', 'Found 5 potential gaps: 3 high confidence (>0.75), 2 low confidence (<0.75)')
            ->phase('filter', 'Removed 2 low-confidence agents')
            ->phase('generation', 'Generated 3 agents with avg confidence: 0.84')
            ->phase('output', 'Report: "3 agents generated, 2 skipped (low confidence), delegation_count: 3"');

        // Directive
        $this->guideline('directive')
            ->text('Generate ONLY missing agents! Preserve existing! Use brain make:master ONLY! Delegate web research to WebResearchMaster! Cache patterns! Validate with industry standards! Report confidence scores! Compile after generation!');
    }
}
