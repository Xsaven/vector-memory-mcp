<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Compilation\BrainCLI;
use BrainCore\Compilation\Operator;
use BrainCore\Compilation\Runtime;
use BrainCore\Compilation\Store;
use BrainCore\Compilation\Tools\BashTool;
use BrainCore\Compilation\Tools\ReadTool;
use BrainCore\Compilation\Tools\WebSearchTool;
use BrainNode\Agents\AgentMaster;
use BrainNode\Agents\DocumentationMaster;
use BrainNode\Agents\ExploreMaster;
use BrainNode\Agents\WebResearchMaster;
use BrainNode\Mcp\VectorMemoryMcp;

#[Meta('id', 'init-brain')]
#[Meta('description', 'Comprehensive Brain.php initialization - scans project, analyzes docs/code, generates optimized configuration')]
#[Purpose('Discovers project context, analyzes docs/code, researches best practices, generates optimized .brain/node/Brain.php with project-specific guidelines, stores insights to vector memory')]
class InitBrainCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {
        // =====================================================
        // IRON RULES
        // =====================================================

        $this->rule('temporal-context-first')->critical()
            ->text(['Temporal context MUST be initialized first:', BashTool::call('date +"%Y-%m-%d %H:%M:%S %Z"')])
            ->why('Ensures all research and recommendations reflect current year best practices')
            ->onViolation('Missing temporal context leads to outdated recommendations');

        $this->rule('parallel-research')->critical()
            ->text('Execute independent research tasks in parallel for efficiency')
            ->why('Maximizes throughput and minimizes total execution time')
            ->onViolation('Sequential execution wastes time on independent tasks');

        $this->rule('evidence-based')->critical()
            ->text('All Brain.php guidelines must be backed by discovered project evidence')
            ->why('Prevents generic configurations that do not match project reality')
            ->onViolation('Speculation leads to misaligned Brain behavior');

        $this->rule('preserve-existing')->critical()
            ->text(['Backup existing', Runtime::NODE_DIRECTORY('Brain.php'), 'before modifications'])
            ->why('Prevents data loss and enables rollback if needed')
            ->onViolation('Data loss and inability to recover previous configuration');

        $this->rule('vector-memory-storage')->high()
            ->text('Store all significant insights to vector memory with semantic tags')
            ->why('Enables future context retrieval and knowledge accumulation')
            ->onViolation('Knowledge loss and inability to leverage past discoveries');

        // =====================================================
        // PHASE 1: TEMPORAL CONTEXT INITIALIZATION
        // =====================================================

        $this->guideline('phase1-temporal-context')
            ->goal('Initialize temporal awareness for all subsequent operations')
            ->example()
            ->phase(
                BashTool::describe('date +"%Y-%m-%d"', Store::as('CURRENT_DATE'))
            )
            ->phase(
                BashTool::describe('date +"%Y"', Store::as('CURRENT_YEAR'))
            )
            ->phase(
                BashTool::describe('date +"%Y-%m-%d %H:%M:%S %Z"', Store::as('TIMESTAMP'))
            )
            ->phase(Operator::verify('All temporal variables set'))
            ->note('This ensures all research queries include current year for up-to-date results');

        // =====================================================
        // PHASE 2: PROJECT DISCOVERY (PARALLEL)
        // =====================================================

        $this->guideline('phase2-project-discovery')
            ->goal('Discover project structure, technology stack, and patterns')
            ->example()
            ->note('Execute all discovery tasks in parallel for efficiency')
            ->phase()
            ->name('parallel-discovery-tasks')
            ->do(
                Operator::task([
                    // Task 2.1: Documentation Discovery
                    ExploreMaster::call(
                        Operator::task([
                            'Check if .docs/ directory exists using Glob',
                            'Use Glob("**/.docs/**/*.md") to find documentation files',
                            Operator::if('.docs/ exists', [
                                'Read all .md files from .docs/ directory',
                                'Extract: project goals, requirements, architecture decisions, domain terminology',
                                Store::as('DOCS_CONTENT'),
                            ], [
                                'No .docs/ found',
                                Store::as('DOCS_CONTENT', 'null'),
                            ]),
                        ]),
                        Operator::context('Documentation discovery for project context')
                    ),

                    // Task 2.2: Codebase Structure Analysis
                    ExploreMaster::call(
                        Operator::task([
                            'Analyze project root structure',
                            'Use Glob to find: composer.json, package.json, .env.example, README.md',
                            'Read key dependency files',
                            'Identify project type (Laravel, Node.js, hybrid, etc.)',
                            'Extract technology stack from dependency files',
                            Store::as('PROJECT_TYPE'),
                            Store::as('TECH_STACK', '{languages: [...], frameworks: [...], packages: [...], services: [...]}'),
                        ]),
                        Operator::context('Codebase structure and tech stack analysis')
                    ),

                    // Task 2.3: Architecture Pattern Discovery
                    ExploreMaster::call(
                        Operator::task([
                            'Scan for architectural patterns',
                            'Use Glob to find PHP/JS/TS files in app/ and src/ directories',
                            'Analyze code structure and organization',
                            'Identify: MVC, DDD, CQRS, microservices, monolith, etc.',
                            'Detect design patterns: repositories, services, factories, observers, etc.',
                            'Find coding conventions: naming, structure, organization',
                            Store::as('ARCHITECTURE_PATTERNS', '{architecture_style: "...", design_patterns: [...], conventions: [...]}'),
                        ]),
                        Operator::context('Architecture pattern discovery')
                    ),

                    // Task 2.4: Existing Brain Configuration Analysis
                    ExploreMaster::call(
                        Operator::task([
                            ReadTool::call(Runtime::NODE_DIRECTORY('Brain.php')),
                            'Extract current includes and configuration',
                            'Identify what is already configured',
                            Store::as('CURRENT_BRAIN_CONFIG', '{includes: [...], custom_rules: [...], custom_guidelines: [...]}'),
                        ]),
                        Operator::context('Current Brain configuration analysis')
                    ),
                ])
            )
            ->phase(Operator::verify('All discovery tasks completed'))
            ->phase(Store::as('PROJECT_CONTEXT', 'Merged results from all discovery tasks'));

        // =====================================================
        // PHASE 3: DOCUMENTATION DEEP ANALYSIS
        // =====================================================

        $this->guideline('phase3-documentation-analysis')
            ->goal('Deep analysis of project documentation to extract requirements and domain knowledge')
            ->example()
            ->phase(
                Operator::if(Store::get('DOCS_CONTENT') . ' !== null', [
                    DocumentationMaster::call(
                        Operator::input(Store::get('DOCS_CONTENT')),
                        Operator::task([
                            'Analyze all documentation files',
                            'Extract: project goals, requirements, constraints, domain concepts',
                            'Identify: key workflows, business rules, integration points',
                            'Map documentation to Brain configuration needs',
                            'Suggest: custom includes, rules, guidelines based on docs',
                        ]),
                        Operator::output('{goals: [...], requirements: [...], domain_concepts: [...], suggested_config: {...}}'),
                    ),
                    Store::as('DOCS_ANALYSIS'),
                ], [
                    'No documentation found - will rely on codebase analysis only',
                    Store::as('DOCS_ANALYSIS', 'null'),
                ])
            );

        // =====================================================
        // PHASE 4: BEST PRACTICES RESEARCH (PARALLEL)
        // =====================================================

        $this->guideline('phase4-best-practices-research')
            ->goal('Research current best practices for discovered technologies')
            ->example()
            ->note('Execute research tasks in parallel for each major technology')
            ->phase(
                Operator::forEach(Store::get('TECH_STACK.frameworks'), [
                    WebResearchMaster::call(
                        Operator::input(Store::get('CURRENT_YEAR')),
                        Operator::task([
                            WebSearchTool::describe('{framework} best practices {current_year}'),
                            WebSearchTool::describe('{framework} architectural patterns {current_year}'),
                            WebSearchTool::describe('{framework} code organization {current_year}'),
                            'Extract: recommended patterns, conventions, anti-patterns',
                            'Identify: framework-specific Brain configuration needs',
                        ]),
                        Operator::output('{framework: "...", best_practices: [...], recommendations: [...]}'),
                    ),
                ])
            )
            ->phase(Store::as('BEST_PRACTICES', 'Collected results from all research tasks'));

        // =====================================================
        // PHASE 5: AVAILABLE INCLUDES ANALYSIS
        // =====================================================

        $this->guideline('phase5-includes-analysis')
            ->goal('Analyze available includes and select optimal set for project')
            ->example()
            ->phase(
                BashTool::describe(BrainCLI::LIST_INCLUDES, Store::as('AVAILABLE_INCLUDES'))
            )
            ->phase(
                AgentMaster::call(
                    Operator::input(
                        Store::get('AVAILABLE_INCLUDES'),
                        Store::get('PROJECT_CONTEXT'),
                        Store::get('DOCS_ANALYSIS'),
                        Store::get('BEST_PRACTICES'),
                    ),
                    Operator::task([
                        'Analyze all available Brain includes',
                        'Map project needs to include capabilities',
                        'Categorize includes: Essential, Recommended, Optional, Not Needed',
                        'Identify missing includes that should be created',
                        'Generate optimal include configuration for this project',
                    ]),
                    Operator::output('{essential_includes: [...], recommended_includes: [...], optional_includes: [...], missing_includes: [...], rationale: {...}}'),
                )
            )
            ->phase(Store::as('INCLUDES_RECOMMENDATION'));

        // =====================================================
        // PHASE 6: CUSTOM GUIDELINES GENERATION
        // =====================================================

        $this->guideline('phase6-custom-guidelines')
            ->goal('Generate project-specific custom guidelines for Brain.php')
            ->example()
            ->phase(
                AgentMaster::call(
                    Operator::input(
                        Store::get('PROJECT_CONTEXT'),
                        Store::get('DOCS_ANALYSIS'),
                        Store::get('BEST_PRACTICES'),
                        Store::get('ARCHITECTURE_PATTERNS'),
                    ),
                    Operator::task([
                        'Identify project-specific patterns requiring custom guidelines',
                        'Generate guidelines using Builder API syntax',
                        'Focus on: coding standards, architectural rules, domain logic',
                        'Ensure guidelines are actionable and verifiable',
                        'Format as PHP Builder API code ready for Brain.php',
                    ]),
                    Operator::output('{custom_guidelines: [{id: "...", type: "rule|guideline", code: "..."}], rationale: {...}}'),
                )
            )
            ->phase(Store::as('CUSTOM_GUIDELINES'));

        // =====================================================
        // PHASE 7: BRAIN.PHP GENERATION
        // =====================================================

        $this->guideline('phase7-brain-generation')
            ->goal('Generate optimized Brain.php configuration file')
            ->example()
            ->phase('Backup existing Brain.php')
            ->phase(
                BashTool::describe(
                    'cp ' . Runtime::NODE_DIRECTORY('Brain.php') . ' ' . Runtime::NODE_DIRECTORY('Brain.php.backup'),
                    'Create backup before modification'
                )
            )
            ->phase('Generate new Brain.php content')
            ->phase(
                AgentMaster::call(
                    Operator::input(
                        Store::get('CURRENT_BRAIN_CONFIG'),
                        Store::get('INCLUDES_RECOMMENDATION'),
                        Store::get('CUSTOM_GUIDELINES'),
                        Store::get('PROJECT_CONTEXT'),
                    ),
                    Operator::task([
                        'Generate complete Brain.php file using Builder API',
                        'Include all essential includes from recommendation',
                        'Add recommended includes with comments',
                        'Integrate custom guidelines into handle() method',
                        'Maintain proper PHP structure and namespaces',
                        'Follow existing Brain.php formatting conventions',
                        'Add comprehensive documentation comments',
                    ]),
                    Operator::output('{brain_php_content: "...", changes_summary: {...}}'),
                )
            )
            ->phase('Write new Brain.php')
            ->phase(Store::as('NEW_BRAIN_PHP'))
            ->phase(
                Operator::note('Brain.php updated with project-specific configuration')
            );

        // =====================================================
        // PHASE 8: COMPILATION AND VALIDATION
        // =====================================================

        $this->guideline('phase8-compilation')
            ->goal('Compile Brain.php and validate output')
            ->example()
            ->phase(
                BashTool::describe(
                    BrainCLI::COMPILE,
                    ['Compile', Runtime::NODE_DIRECTORY('Brain.php'), 'to', Runtime::BRAIN_FILE]
                )
            )
            ->phase(
                Operator::verify([
                    'Compilation succeeded',
                    Runtime::BRAIN_FILE . ' exists',
                    'No syntax errors',
                    'All includes resolved',
                ])
            )
            ->phase(
                Operator::if('compilation failed', [
                    'Restore backup',
                    BashTool::call('mv ' . Runtime::NODE_DIRECTORY('Brain.php.backup') . ' ' . Runtime::NODE_DIRECTORY('Brain.php')),
                    'Report errors',
                    Operator::output('Compilation failed - backup restored'),
                ])
            );

        // =====================================================
        // PHASE 9: KNOWLEDGE STORAGE
        // =====================================================

        $this->guideline('phase9-knowledge-storage')
            ->goal('Store all insights to vector memory for future reference')
            ->example()
            ->phase(
                VectorMemoryMcp::call('store_memory', Operator::input(
                    'content: "Brain Initialization - Project: {project_type}, Tech Stack: {tech_stack}, Patterns: {architecture_patterns}, Date: {current_date}"',
                    'category: "architecture"',
                    'tags: ["init-brain", "project-discovery", "configuration"]',
                ))
            )
            ->phase(
                VectorMemoryMcp::call('store_memory', Operator::input(
                    'content: "Best Practices Research - Frameworks: {frameworks}, Recommendations: {best_practices}, Date: {current_date}"',
                    'category: "learning"',
                    'tags: ["init-brain", "best-practices", "research"]',
                ))
            )
            ->phase(
                VectorMemoryMcp::call('store_memory', Operator::input(
                    'content: "Brain Configuration - Includes: {includes}, Custom Guidelines: {custom_guidelines_count}, Date: {current_date}"',
                    'category: "architecture"',
                    'tags: ["init-brain", "brain-config", "includes"]',
                ))
            );

        // =====================================================
        // PHASE 10: REPORT GENERATION
        // =====================================================

        $this->guideline('phase10-report')
            ->goal('Generate comprehensive initialization report')
            ->example()
            ->phase(
                Operator::output([
                    'Brain Initialization Complete',
                    '',
                    'Project Discovery:',
                    '  Type: {project_type}',
                    '  Tech Stack: {tech_stack}',
                    '  Architecture: {architecture_patterns}',
                    '',
                    'Documentation Analysis:',
                    '  Files Analyzed: {docs_file_count}',
                    '  Domain Concepts: {domain_concepts_count}',
                    '  Requirements: {requirements_count}',
                    '',
                    'Includes Configuration:',
                    '  Essential: {essential_includes_list}',
                    '  Recommended: {recommended_includes_list}',
                    '  Total: {total_includes_count}',
                    '',
                    'Custom Guidelines:',
                    '  Rules: {custom_rules_count}',
                    '  Guidelines: {custom_guidelines_count}',
                    '',
                    'Best Practices:',
                    '  Frameworks Researched: {frameworks_count}',
                    '  Recommendations Applied: {recommendations_count}',
                    '',
                    'Output Files:',
                    '  Source: ' . Runtime::NODE_DIRECTORY('Brain.php'),
                    '  Compiled: ' . Runtime::BRAIN_FILE,
                    '  Backup (if not default empty file): ' . Runtime::NODE_DIRECTORY('Brain.php.backup'),
                    '',
                    'Vector Memory:',
                    '  Insights Stored: {insights_count}',
                    '  Categories: architecture, learning',
                    '',
                    'Next Steps:',
                    '  1. Review generated Brain.php configuration',
                    '  2. Test Brain behavior with sample tasks',
                    '  3. Adjust custom guidelines as needed',
                    '  4. Run: brain compile (if modified)',
                    '  5. Consider running: /init-agents for agent generation',
                ])
            );

        // =====================================================
        // ERROR RECOVERY
        // =====================================================

        $this->guideline('error-recovery')
            ->text('Comprehensive error handling for all failure scenarios')
            ->example()
            ->phase()->if('no .docs/ found', [
                'Continue with codebase analysis only',
                'Log: Documentation not available',
            ])
            ->phase()->if('tech stack detection fails', [
                'Use manual fallback detection',
                'Analyze file extensions and structure',
            ])
            ->phase()->if('web research fails', [
                'Use cached knowledge from vector memory',
                'Continue with available information',
            ])
            ->phase()->if(BrainCLI::LIST_INCLUDES . ' fails', [
                'Use hardcoded standard includes list',
                'Log: Include discovery failed',
            ])
            ->phase()->if('Brain.php generation fails', [
                'Preserve backup',
                'Report detailed error',
                'Provide manual configuration guidance',
            ])
            ->phase()->if(BrainCLI::COMPILE . ' fails', [
                'Restore backup',
                'Analyze compilation errors',
                'Suggest fixes',
            ])
            ->phase()->if('vector memory storage fails', [
                'Continue without storage',
                'Log: Memory storage unavailable',
            ]);

        // =====================================================
        // QUALITY GATES
        // =====================================================

        $this->guideline('quality-gates')
            ->text('Validation checkpoints throughout initialization')
            ->example('Gate 1: Temporal context initialized (date, year, timestamp)')
            ->example('Gate 2: Project discovery completed with valid tech stack')
            ->example('Gate 3: At least one discovery task succeeded (docs OR codebase)')
            ->example('Gate 4: Includes recommendation generated with rationale')
            ->example('Gate 5: Brain.php backup created successfully')
            ->example('Gate 6: New Brain.php passes syntax validation')
            ->example('Gate 7: Compilation completes without errors')
            ->example('Gate 8: Compiled output exists at ' . Runtime::BRAIN_FILE)
            ->example('Gate 9: At least one insight stored to vector memory');

        // =====================================================
        // EXAMPLES
        // =====================================================

        $this->guideline('example-laravel-project')
            ->scenario('Laravel project with comprehensive documentation')
            ->example()
            ->phase('Discovery: Laravel 11, PHP 8.3, MySQL, Redis, Queue, Sanctum')
            ->phase('Docs: 15 .md files with architecture, requirements, domain logic')
            ->phase('Research: Laravel 2025 best practices, service container patterns')
            ->phase('Includes: LaravelBoostGuidelines, QualityGates, DDD patterns')
            ->phase('Custom Guidelines: Repository pattern rules, service layer conventions')
            ->phase('Result: Optimized Brain.php with Laravel-specific configuration')
            ->phase('Insights: 5 architectural insights stored to vector memory');

        $this->guideline('example-node-project')
            ->scenario('Node.js/Express project without documentation')
            ->example()
            ->phase('Discovery: Node.js 20, Express, TypeScript, MongoDB, Docker')
            ->phase('Docs: None found - codebase analysis only')
            ->phase('Research: Express 2025 patterns, TypeScript best practices')
            ->phase('Includes: CoreConstraints, ErrorRecovery, QualityGates')
            ->phase('Custom Guidelines: REST API conventions, middleware patterns')
            ->phase('Result: Brain.php with Node.js-aware configuration')
            ->phase('Insights: 3 tech stack insights stored');

        $this->guideline('example-hybrid-project')
            ->scenario('Hybrid PHP/JavaScript project with microservices')
            ->example()
            ->phase('Discovery: Laravel API + React SPA + Docker + Kafka')
            ->phase('Docs: Architectural decision records, API specs, deployment docs')
            ->phase('Research: Microservices patterns, event-driven architecture')
            ->phase('Includes: Multiple domain-specific includes + custom service layer')
            ->phase('Custom Guidelines: Microservice boundaries, event schemas, API versioning')
            ->phase('Result: Complex Brain.php with multi-paradigm support')
            ->phase('Insights: 12 cross-cutting concerns stored');

        // =====================================================
        // PERFORMANCE OPTIMIZATION
        // =====================================================

        $this->guideline('performance-optimization')
            ->text('Optimization strategies for efficient initialization')
            ->example()
            ->phase('Parallel Execution: All independent tasks run simultaneously')
            ->phase('Selective Reading: Only read files needed for analysis')
            ->phase('Incremental Storage: Store insights progressively, not at end')
            ->phase('Smart Caching: Leverage vector memory for repeated runs')
            ->phase('Early Validation: Fail fast on critical errors')
            ->phase('Streaming Output: Report progress as phases complete');

        // =====================================================
        // DIRECTIVE
        // =====================================================

        $this->guideline('directive')
            ->text('Core initialization directive')
            ->example('Discover thoroughly! Research current practices! Configure precisely! Validate rigorously! Store knowledge! Report comprehensively!');
    }
}
