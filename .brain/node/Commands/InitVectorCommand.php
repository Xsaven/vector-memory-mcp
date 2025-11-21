<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Compilation\Operator;
use BrainCore\Compilation\Store;
use BrainCore\Compilation\Tools\BashTool;
use BrainNode\Agents\ExploreMaster;
use BrainNode\Mcp\VectorMemoryMcp;

#[Meta('id', 'init-vector')]
#[Meta('description', 'Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents')]
#[Purpose('Systematically scan and document entire project into vector memory. Sequential ExploreMaster agents explore logical areas, communicating through vector memory for continuity. Each agent searches memory before exploring, stores findings after. Enables comprehensive project knowledge base for all agents.')]
class InitVectorCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {
        // Iron Rules
        $this->rule('memory-first-mandatory')->critical()
            ->text('Every ExploreMaster MUST search vector memory BEFORE exploring to maintain continuity')
            ->why('Enables context sharing between sequential agents and prevents duplicate work')
            ->onViolation('Include explicit MANDATORY BEFORE: search_memories in delegation');

        $this->rule('memory-store-mandatory')->critical()
            ->text('Every ExploreMaster MUST store findings to vector memory AFTER exploring')
            ->why('Builds knowledge base for future agents and creates persistent project documentation')
            ->onViolation('Include explicit MANDATORY AFTER: store_memory in delegation');

        $this->rule('vector-memory-is-communication-channel')->critical()
            ->text('Vector memory is the PRIMARY communication channel between sequential agents')
            ->why('Agents pass context through memory, not through Brain. Short reports to Brain, detailed data to memory.')
            ->onViolation('Emphasize memory usage in delegation instructions');

        $this->rule('no-interactive-questions')->high()
            ->text('NO interactive questions - fully automated workflow')
            ->why('Automated initialization for project knowledge base')
            ->onViolation('Execute fully automated without user prompts');

        $this->rule('thoroughness-appropriate')->high()
            ->text('Use "medium" thoroughness for most areas, "very thorough" only for complex core areas')
            ->why('Balances comprehensive coverage with reasonable execution time')
            ->onViolation('Adjust thoroughness based on area complexity');

        // Phase 1: Check Vector Memory Status
        $this->guideline('phase1-memory-status')
            ->goal('Check if vector memory is empty (first-time setup) or has existing data')
            ->example()
            ->phase(VectorMemoryMcp::call('get_memory_stats', '{}'))
            ->phase(Store::as('MEMORY_STATUS', '{total_memories, categories, age}'))
            ->phase(Operator::if('total_memories === 0', [
                Store::as('IS_FRESH_INIT', 'true'),
                Operator::output('Vector memory empty - performing first-time initialization'),
            ]))
            ->phase(Operator::if('total_memories > 0', [
                Store::as('IS_FRESH_INIT', 'false'),
                Operator::output('Vector memory has {total_memories} entries - augmenting existing knowledge'),
            ]));

        // Phase 2: Initial Project Structure Scan
        $this->guideline('phase2-structure-scan')
            ->goal('Get high-level project overview and identify logical areas to explore')
            ->example()
            ->phase(Operator::output('Scanning project structure...'))
            ->phase(
                ExploreMaster::call(
                    Operator::task([
                        'MANDATORY BEFORE: mcp__vector-memory__search_memories(query: "project structure overview", limit: 5, category: "architecture")',
                        'IF(memory has recent structure) → THEN → Use cached structure, verify with quick scan → END-IF',
                        'IF(no cached structure) → THEN → Full structure discovery → END-IF',
                        'Explore root directory structure (quick scan)',
                        'Identify main directories: src/, tests/, docs/, config/, vendor/, node_modules/, etc.',
                        'Determine project type: Laravel, Node.js, React, etc.',
                        'Map logical exploration areas based on directory structure',
                        'Priority: src/ > tests/ > config/ > docs/ > other',
                        'MANDATORY AFTER: mcp__vector-memory__store_memory(content: "Project Structure:\\n{directory_tree}\\n\\nProject Type: {type}\\n\\nAreas: {areas_list}\\n\\nPriority: {priority_order}", category: "architecture", tags: ["init-vector", "project-structure", "overview"])',
                    ]),
                    Operator::output('{structure, project_type, areas: [{name, path, priority, complexity}], directory_tree}'),
                    Store::as('PROJECT_STRUCTURE')
                )
            )
            ->phase(Operator::output('Structure discovered: {areas_count} areas identified'));

        // Phase 3: Sequential Area Exploration
        $this->guideline('phase3-area-exploration')
            ->goal('Explore each project area sequentially with ExploreMaster agents communicating via vector memory')
            ->example()
            ->phase(Operator::output('Beginning sequential area exploration...'))
            ->phase(Operator::forEach('area in $PROJECT_STRUCTURE.areas', [
                Operator::output('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'),
                Operator::output('Exploring: {area.name} ({area.path})'),
                Operator::output('Priority: {area.priority} | Complexity: {area.complexity}'),
                '',
                ExploreMaster::call(
                    Operator::input(Store::get('area')),
                    Operator::task([
                        '━━━ MANDATORY BEFORE: SEARCH VECTOR MEMORY ━━━',
                        'Execute: mcp__vector-memory__search_memories(query: "{area.name} {area.path} context previous findings", limit: 5, category: "architecture,code-solution")',
                        'Review: Analyze results for patterns, previous findings, related areas',
                        'Context: Use findings from previous agents to inform exploration',
                        '',
                        '━━━ EXPLORATION: {area.name} ━━━',
                        'Thoroughness: IF(area.complexity === "high") → very thorough → ELSE → medium → END-IF',
                        'Path: {area.path}',
                        '',
                        'TASK 1: File Discovery',
                        '  - Use Glob to discover all files in {area.path}',
                        '  - Identify file types and patterns',
                        '  - Map directory structure within area',
                        '',
                        'TASK 2: Code Analysis',
                        '  - Use Grep to find: class definitions, function signatures, imports',
                        '  - Identify naming conventions and patterns',
                        '  - Extract key abstractions and components',
                        '',
                        'TASK 3: Architecture Understanding',
                        '  - Analyze relationships between components',
                        '  - Identify design patterns and architectural styles',
                        '  - Document dependencies and workflows',
                        '',
                        'TASK 4: Technology Stack',
                        '  - Identify frameworks, libraries, tools used',
                        '  - Extract version information from config files',
                        '  - Note key technologies and dependencies',
                        '',
                        '━━━ MANDATORY AFTER: STORE TO VECTOR MEMORY ━━━',
                        'Execute: mcp__vector-memory__store_memory(',
                        '  content: "Area: {area.name} ({area.path})\\n\\n',
                        '    ## File Structure\\n{file_tree}\\n\\n',
                        '    ## Key Components\\n{components_list}\\n\\n',
                        '    ## Patterns & Conventions\\n{patterns}\\n\\n',
                        '    ## Technologies\\n{tech_stack}\\n\\n',
                        '    ## Architecture Notes\\n{architecture_insights}\\n\\n',
                        '    ## Dependencies\\n{dependencies}",',
                        '  category: "architecture",',
                        '  tags: ["init-vector", "{area.name}", "{area.path}", "exploration"]',
                        ')',
                        '',
                        '━━━ BRIEF REPORT TO BRAIN ━━━',
                        'Report: "Area {area.name} explored ✓ | Files: {count} | Components: {key_components} | Stored to memory"',
                    ]),
                    Operator::output('Brief progress update'),
                ),
                '',
                Operator::output('✓ {area.name} complete'),
                Operator::report('Progress: {completed}/{total} areas explored'),
            ]))
            ->phase(Operator::output('All areas explored ✓'));

        // Phase 4: Cross-Area Relationships Analysis
        $this->guideline('phase4-relationships')
            ->goal('Analyze relationships and dependencies between explored areas')
            ->example()
            ->phase(Operator::output('Analyzing cross-area relationships...'))
            ->phase(
                ExploreMaster::call(
                    Operator::task([
                        'MANDATORY BEFORE: mcp__vector-memory__search_memories(query: "project areas components architecture", limit: 20, category: "architecture")',
                        'Review: All area exploration results from memory',
                        '',
                        'TASK: Cross-Area Analysis',
                        '  - Identify imports/dependencies between areas',
                        '  - Map component relationships across boundaries',
                        '  - Analyze architectural patterns (layering, separation of concerns)',
                        '  - Document data flow and communication patterns',
                        '',
                        'MANDATORY AFTER: mcp__vector-memory__store_memory(',
                        '  content: "Project-Wide Architecture\\n\\n',
                        '    ## Area Dependencies\\n{dependency_graph}\\n\\n',
                        '    ## Component Relationships\\n{relationships}\\n\\n',
                        '    ## Architectural Patterns\\n{patterns}\\n\\n',
                        '    ## Data Flow\\n{data_flow}",',
                        '  category: "architecture",',
                        '  tags: ["init-vector", "project-wide", "relationships", "architecture"]',
                        ')',
                    ]),
                    Operator::output('Brief summary'),
                )
            )
            ->phase(Operator::output('Relationships analyzed ✓'));

        // Phase 5: Final Completion Summary
        $this->guideline('phase5-completion')
            ->goal('Report completion and store initialization summary')
            ->example()
            ->phase(VectorMemoryMcp::call('get_memory_stats', '{}'))
            ->phase(Store::as('FINAL_MEMORY_STATUS', '{total_memories, categories, size}'))
            ->phase(Store::as('AREAS_EXPLORED', 'count($PROJECT_STRUCTURE.areas)'))
            ->phase(
                VectorMemoryMcp::call('store_memory', '{
                    content: "Vector Memory Initialization Complete\\n\\n
                        Timestamp: {current_date}\\n
                        Areas Explored: {$AREAS_EXPLORED}\\n
                        Project Type: {$PROJECT_STRUCTURE.project_type}\\n
                        Total Memories: {$FINAL_MEMORY_STATUS.total_memories}\\n
                        Categories: {$FINAL_MEMORY_STATUS.categories}\\n
                        Status: Comprehensive project knowledge base established",
                    category: "architecture",
                    tags: ["init-vector", "completion", "summary"]
                }')
            )
            ->phase(Operator::output([
                '',
                '═══════════════════════════════════════════════════════════',
                'VECTOR MEMORY INITIALIZATION COMPLETE',
                '═══════════════════════════════════════════════════════════',
                '',
                'Project: {$PROJECT_STRUCTURE.project_type}',
                'Areas Explored: {$AREAS_EXPLORED}',
                '',
                'Memory Status:',
                '  Total Memories: {$FINAL_MEMORY_STATUS.total_memories}',
                '  Categories: {$FINAL_MEMORY_STATUS.categories}',
                '  Size: {$FINAL_MEMORY_STATUS.size}',
                '',
                'Coverage:',
                '  ✓ Project structure and organization',
                '  ✓ File patterns and naming conventions',
                '  ✓ Component architecture and relationships',
                '  ✓ Technology stack and dependencies',
                '  ✓ Cross-area dependencies and data flow',
                '',
                'Next Steps:',
                '  • All agents can now leverage project knowledge via vector memory',
                '  • Use mcp__vector-memory__search_memories() to query knowledge base',
                '  • Run /init-vector again to refresh/augment project knowledge',
                '',
                '═══════════════════════════════════════════════════════════',
            ]));

        // Memory Communication Pattern
        $this->guideline('memory-communication-pattern')
            ->text('Standard pattern for agent-to-agent communication via vector memory')
            ->example()
            ->phase('BEFORE EXPLORATION:')
            ->do([
                'Execute: mcp__vector-memory__search_memories(query: "{relevant_context}", limit: 5)',
                'Review: Previous findings and context',
                'Apply: Insights from previous agents',
            ])
            ->phase('DURING EXPLORATION:')
            ->do([
                'Focus: Execute exploration tasks',
                'Discover: File structure, code patterns, architecture',
                'Analyze: Components, relationships, technologies',
            ])
            ->phase('AFTER EXPLORATION:')
            ->do([
                'Document: Comprehensive findings',
                'Execute: mcp__vector-memory__store_memory(content: "{detailed_findings}", category: "architecture", tags: [...])',
                'Report: Brief progress to Brain',
            ])
            ->phase('CRITICAL: Vector memory is the knowledge base. Your findings enable next agents!');

        // Exploration Areas Template
        $this->guideline('exploration-areas-template')
            ->text('Standard areas to explore in typical projects')
            ->example()
            ->phase('Core Source Code:')
            ->do(['src/ or app/ - main application code', 'Priority: HIGH', 'Thoroughness: very thorough'])
            ->phase('Tests:')
            ->do(['tests/ or __tests__/ - test suites', 'Priority: HIGH', 'Thoroughness: medium'])
            ->phase('Configuration:')
            ->do(['config/ - application configuration', 'Priority: MEDIUM', 'Thoroughness: medium'])
            ->phase('Documentation:')
            ->do(['.docs/ - project documentation', 'Priority: MEDIUM', 'Thoroughness: medium'])
            ->phase('Build & Deploy:')
            ->do(['build scripts, CI/CD configs', 'Priority: LOW', 'Thoroughness: quick'])
            ->phase('Dependencies:')
            ->do(['package.json, composer.json, etc.', 'Priority: LOW', 'Thoroughness: quick'])
            ->phase('Note: Areas dynamically determined based on actual project structure');

        // Error Handling
        $this->guideline('error-handling')
            ->text('Graceful error handling during initialization')
            ->example()
            ->phase()->if('vector memory unavailable', [
                'Report: "Vector memory MCP unavailable - cannot initialize"',
                'Suggest: Check MCP server status',
                'Abort: Cannot proceed without memory access',
            ])
            ->phase()->if('area exploration fails', [
                'Log: "Area {name} exploration failed: {error}"',
                'Continue: Proceed with next area',
                'Store: Partial results to memory',
                'Report: Failed areas in completion summary',
            ])
            ->phase()->if('ExploreMaster timeout', [
                'Log: "Area {name} exploration timeout"',
                'Skip: Move to next area',
                'Report: Timeout in summary',
            ])
            ->phase()->if('empty project or no files', [
                'Report: "Project appears empty or inaccessible"',
                'Store: Basic structure info',
                'Complete: Mark as initialized with minimal data',
            ]);

        // Quality Metrics
        $this->guideline('quality-metrics')
            ->text('Quality targets for initialization')
            ->example('Areas explored: 100% of discovered directories')->key('coverage')
            ->example('Memory stores: 1 per area + 1 project-wide + 1 completion')->key('memory-ops')
            ->example('Agent memory searches: 1 per area exploration')->key('memory-reads')
            ->example('Thoroughness: medium for most, very thorough for src/')->key('thoroughness')
            ->example('Execution time: ≤ 5 minutes for typical project')->key('performance');

        // Parallel Execution Consideration
        $this->guideline('parallel-execution-note')
            ->text('Future optimization: parallel exploration for independent areas')
            ->example()
            ->phase('Current: Sequential execution (one area at a time)')
            ->phase('Rationale: Ensures memory continuity and prevents race conditions')
            ->phase('Future: Independent areas (e.g., src/, tests/, docs/) could run in parallel')
            ->phase('Optimization: 3-5 ExploreMaster::call() in single response for independent areas')
            ->phase('Note: Requires careful coordination to prevent memory conflicts');

        // Examples
        $this->guideline('example-1-fresh-init')
            ->scenario('First-time initialization with empty vector memory')
            ->example()
            ->phase('input', '/init-vector (no arguments)')
            ->phase('phase1', 'Memory status: 0 entries → IS_FRESH_INIT = true')
            ->phase('phase2', 'Structure scan: discovered 5 areas (src/, tests/, config/, docs/, build/)')
            ->phase('phase3', 'Sequential exploration:')
            ->do([
                'Area 1: src/ (very thorough) → 150 files, 85 classes → stored to memory',
                'Area 2: tests/ (medium) → 45 files, 30 test classes → stored to memory',
                'Area 3: config/ (medium) → 12 files, configs → stored to memory',
                'Area 4: docs/ (medium) → 8 files, documentation → stored to memory',
                'Area 5: build/ (quick) → 3 files, build scripts → stored to memory',
            ])
            ->phase('phase4', 'Relationships: 23 cross-area dependencies → stored to memory')
            ->phase('phase5', 'Completion: 157 total memories, 5 areas explored ✓');

        $this->guideline('example-2-augment-existing')
            ->scenario('Re-running initialization with existing vector memory')
            ->example()
            ->phase('input', '/init-vector')
            ->phase('phase1', 'Memory status: 157 entries → IS_FRESH_INIT = false → augmenting')
            ->phase('phase2', 'Structure scan: agent searches memory, finds cached structure, verifies with quick scan')
            ->phase('phase3', 'Sequential exploration: each agent searches memory first, finds previous findings, augments with new discoveries')
            ->phase('phase4', 'Relationships: updates existing relationship data')
            ->phase('phase5', 'Completion: 203 total memories (+46), refreshed knowledge base ✓');

        $this->guideline('example-3-area-specific')
            ->scenario('Single area exploration demonstrating memory communication')
            ->example()
            ->phase('area', 'src/ (complexity: high, priority: HIGH)')
            ->phase('before', 'Agent searches: "src code structure patterns" → finds 3 related memories from docs/ and config/')
            ->phase('during', 'Agent explores src/ thoroughly:')
            ->do([
                'Glob: discovers 150 files (*.php, *.ts, etc.)',
                'Grep: finds 85 class definitions, 200+ functions',
                'Analyzes: MVC pattern, service layer, repositories',
                'Identifies: Laravel framework, dependency injection',
            ])
            ->phase('after', 'Agent stores: comprehensive src/ findings to memory with tags')
            ->phase('report', 'Brief: "src/ explored ✓ | 150 files | 85 classes | Stored to memory"');

        // Directive
        $this->guideline('directive')
            ->text('Initialize vector memory! Sequential exploration! Memory-first communication! Brief reports! Comprehensive documentation! Build knowledge base!');
    }
}
