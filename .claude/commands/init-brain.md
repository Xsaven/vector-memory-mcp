---
name: init-brain
description: "Comprehensive Brain.php initialization - scans project, analyzes docs/code, generates optimized configuration"
---

<command>
<meta>
<id>init-brain</id>
<description>Comprehensive Brain.php initialization - scans project, analyzes docs/code, generates optimized configuration</description>
</meta>
<purpose>Discovers project context, analyzes docs/code, researches best practices, generates optimized .brain/node/Brain.php with project-specific guidelines, stores insights to vector memory</purpose>
<iron_rules>
<rule id="temporal-context-first" severity="critical">
<text>Temporal context MUST be initialized first: Bash('date +"%Y-%m-%d %H:%M:%S %Z"')</text>
<why>Ensures all research and recommendations reflect current year best practices</why>
<on_violation>Missing temporal context leads to outdated recommendations</on_violation>
</rule>
<rule id="parallel-research" severity="critical">
<text>Execute independent research tasks in parallel for efficiency</text>
<why>Maximizes throughput and minimizes total execution time</why>
<on_violation>Sequential execution wastes time on independent tasks</on_violation>
</rule>
<rule id="evidence-based" severity="critical">
<text>All Brain.php guidelines must be backed by discovered project evidence</text>
<why>Prevents generic configurations that do not match project reality</why>
<on_violation>Speculation leads to misaligned Brain behavior</on_violation>
</rule>
<rule id="preserve-existing" severity="critical">
<text>Backup existing .brain/node/Brain.php before modifications</text>
<why>Prevents data loss and enables rollback if needed</why>
<on_violation>Data loss and inability to recover previous configuration</on_violation>
</rule>
<rule id="vector-memory-storage" severity="high">
<text>Store all significant insights to vector memory with semantic tags</text>
<why>Enables future context retrieval and knowledge accumulation</why>
<on_violation>Knowledge loss and inability to leverage past discoveries</on_violation>
</rule>
</iron_rules>
<guidelines>
<guideline id="phase1-temporal-context">
GOAL(Initialize temporal awareness for all subsequent operations)
<example>
<phase name="1">Bash(date +"%Y-%m-%d") → [STORE-AS($)] → END-Bash</phase>
<phase name="2">Bash(date +"%Y") → [STORE-AS($)] → END-Bash</phase>
<phase name="3">Bash(date +"%Y-%m-%d %H:%M:%S %Z") → [STORE-AS($)] → END-Bash</phase>
<phase name="4">
VERIFY-SUCCESS(All temporal variables set)
NOTE(This ensures all research queries include current year for up-to-date results)
</phase>
</example>
</guideline>
<guideline id="phase2-project-discovery">
GOAL(Discover project structure, technology stack, and patterns)
<example>
NOTE(Execute all discovery tasks in parallel for efficiency)
<phase name="parallel-discovery-tasks">TASK → [(Task(@agent-, 'TASK → [(Check if .docs/ directory exists using Glob + Use Glob("**/.docs/**/*.md") to find documentation files + IF(.docs/ exists) → THEN → [Read all .md files from .docs/ directory → Extract: project goals, requirements, architecture decisions, domain terminology → STORE-AS($)] → ELSE → [No .docs/ found → STORE-AS($ = \'null\')] → END-IF)] → END-TASK', 'CONTEXT(Documentation discovery for project context)') + Task(@agent-, 'TASK → [(Analyze project root structure + Use Glob to find: composer.json, package.json, .env.example, README.md + Read key dependency files + Identify project type (Laravel, Node.js, hybrid, etc.) + Extract technology stack from dependency files + STORE-AS($) + STORE-AS($ = \'{languages: [...], frameworks: [...], packages: [...], services: [...]}\'))] → END-TASK', 'CONTEXT(Codebase structure and tech stack analysis)') + Task(@agent-, 'TASK → [(Scan for architectural patterns + Use Glob to find PHP/JS/TS files in app/ and src/ directories + Analyze code structure and organization + Identify: MVC, DDD, CQRS, microservices, monolith, etc. + Detect design patterns: repositories, services, factories, observers, etc. + Find coding conventions: naming, structure, organization + STORE-AS($ = \'{architecture_style: "...", design_patterns: [...], conventions: [...]}\'))] → END-TASK', 'CONTEXT(Architecture pattern discovery)') + Task(@agent-, 'TASK → [(Read(\'.brain/node/Brain.php\') + Extract current includes and configuration + Identify what is already configured + STORE-AS($ = \'{includes: [...], custom_rules: [...], custom_guidelines: [...]}\'))] → END-TASK', 'CONTEXT(Current Brain configuration analysis)'))] → END-TASK</phase>
<phase name="2">VERIFY-SUCCESS(All discovery tasks completed)</phase>
<phase name="3">STORE-AS($ = 'Merged results from all discovery tasks')</phase>
</example>
</guideline>
<guideline id="phase3-documentation-analysis">
GOAL(Deep analysis of project documentation to extract requirements and domain knowledge)
<example>
<phase name="1">IF(STORE-GET($) !== null) → THEN → [Task(@agent-, 'INPUT(STORE-GET($))', 'TASK → [(Analyze all documentation files + Extract: project goals, requirements, constraints, domain concepts + Identify: key workflows, business rules, integration points + Map documentation to Brain configuration needs + Suggest: custom includes, rules, guidelines based on docs)] → END-TASK', 'OUTPUT({goals: [...], requirements: [...], domain_concepts: [...], suggested_config: {...}})') → STORE-AS($)] → ELSE → [No documentation found - will rely on codebase analysis only → STORE-AS($ = 'null')] → END-IF</phase>
</example>
</guideline>
<guideline id="phase4-best-practices-research">
GOAL(Research current best practices for discovered technologies)
<example>
NOTE(Execute research tasks in parallel for each major technology)
<phase name="1">FOREACH(STORE-GET($)) → [Task(@agent-, 'INPUT(STORE-GET($))', 'TASK → [(WebSearch({framework} best practices {current_year}) + WebSearch({framework} architectural patterns {current_year}) + WebSearch({framework} code organization {current_year}) + Extract: recommended patterns, conventions, anti-patterns + Identify: framework-specific Brain configuration needs)] → END-TASK', 'OUTPUT({framework: "...", best_practices: [...], recommendations: [...]})')] → END-FOREACH</phase>
<phase name="2">STORE-AS($ = 'Collected results from all research tasks')</phase>
</example>
</guideline>
<guideline id="phase5-includes-analysis">
GOAL(Analyze available includes and select optimal set for project)
<example>
<phase name="1">Bash(brain list:includes) → [STORE-AS($)] → END-Bash</phase>
<phase name="2">Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [(Analyze all available Brain includes + Map project needs to include capabilities + Categorize includes: Essential, Recommended, Optional, Not Needed + Identify missing includes that should be created + Generate optimal include configuration for this project)] → END-TASK', 'OUTPUT({essential_includes: [...], recommended_includes: [...], optional_includes: [...], missing_includes: [...], rationale: {...}})')</phase>
<phase name="3">STORE-AS($)</phase>
</example>
</guideline>
<guideline id="phase6-custom-guidelines">
GOAL(Generate project-specific custom guidelines for Brain.php)
<example>
<phase name="1">Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [(Identify project-specific patterns requiring custom guidelines + Generate guidelines using Builder API syntax + Focus on: coding standards, architectural rules, domain logic + Ensure guidelines are actionable and verifiable + Format as PHP Builder API code ready for Brain.php)] → END-TASK', 'OUTPUT({custom_guidelines: [{id: "...", type: "rule|guideline", code: "..."}], rationale: {...}})')</phase>
<phase name="2">STORE-AS($)</phase>
</example>
</guideline>
<guideline id="phase7-brain-generation">
GOAL(Generate optimized Brain.php configuration file)
<example>
<phase name="1">Backup existing Brain.php</phase>
<phase name="2">Bash(cp .brain/node/Brain.php .brain/node/Brain.php.backup) → [Create backup before modification] → END-Bash</phase>
<phase name="3">Generate new Brain.php content</phase>
<phase name="4">Task(@agent-, 'INPUT(STORE-GET($) && STORE-GET($) && STORE-GET($) && STORE-GET($))', 'TASK → [(Generate complete Brain.php file using Builder API + Include all essential includes from recommendation + Add recommended includes with comments + Integrate custom guidelines into handle() method + Maintain proper PHP structure and namespaces + Follow existing Brain.php formatting conventions + Add comprehensive documentation comments)] → END-TASK', 'OUTPUT({brain_php_content: "...", changes_summary: {...}})')</phase>
<phase name="5">Write new Brain.php</phase>
<phase name="6">STORE-AS($)</phase>
<phase name="7">NOTE(Brain.php updated with project-specific configuration)</phase>
</example>
</guideline>
<guideline id="phase8-compilation">
GOAL(Compile Brain.php and validate output)
<example>
<phase name="1">Bash(brain compile) → [Compile .brain/node/Brain.php to .claude/CLAUDE.md] → END-Bash</phase>
<phase name="2">VERIFY-SUCCESS(Compilation succeeded .claude/CLAUDE.md exists No syntax errors All includes resolved)</phase>
<phase name="3">IF(compilation failed) → THEN → [Restore backup → Bash('mv .brain/node/Brain.php.backup .brain/node/Brain.php') → Report errors → OUTPUT(Compilation failed - backup restored)] → END-IF</phase>
</example>
</guideline>
<guideline id="phase9-knowledge-storage">
GOAL(Store all insights to vector memory for future reference)
<example>
<phase name="1">mcp__vector-memory__store_memory('INPUT(content: "Brain Initialization - Project: {project_type}, Tech Stack: {tech_stack}, Patterns: {architecture_patterns}, Date: {current_date}" && category: "architecture" && tags: ["init-brain", "project-discovery", "configuration"])')</phase>
<phase name="2">mcp__vector-memory__store_memory('INPUT(content: "Best Practices Research - Frameworks: {frameworks}, Recommendations: {best_practices}, Date: {current_date}" && category: "learning" && tags: ["init-brain", "best-practices", "research"])')</phase>
<phase name="3">mcp__vector-memory__store_memory('INPUT(content: "Brain Configuration - Includes: {includes}, Custom Guidelines: {custom_guidelines_count}, Date: {current_date}" && category: "architecture" && tags: ["init-brain", "brain-config", "includes"])')</phase>
</example>
</guideline>
<guideline id="phase10-report">
GOAL(Generate comprehensive initialization report)
<example>
<phase name="1">OUTPUT(Brain Initialization Complete  Project Discovery:   Type: {project_type}   Tech Stack: {tech_stack}   Architecture: {architecture_patterns}  Documentation Analysis:   Files Analyzed: {docs_file_count}   Domain Concepts: {domain_concepts_count}   Requirements: {requirements_count}  Includes Configuration:   Essential: {essential_includes_list}   Recommended: {recommended_includes_list}   Total: {total_includes_count}  Custom Guidelines:   Rules: {custom_rules_count}   Guidelines: {custom_guidelines_count}  Best Practices:   Frameworks Researched: {frameworks_count}   Recommendations Applied: {recommendations_count}  Output Files:   Source: .brain/node/Brain.php   Compiled: .claude/CLAUDE.md   Backup (if not default empty file): .brain/node/Brain.php.backup  Vector Memory:   Insights Stored: {insights_count}   Categories: architecture, learning  Next Steps:   1. Review generated Brain.php configuration   2. Test Brain behavior with sample tasks   3. Adjust custom guidelines as needed   4. Run: brain compile (if modified)   5. Consider running: /init-agents for agent generation)</phase>
</example>
</guideline>
<guideline id="error-recovery">
<text>Comprehensive error handling for all failure scenarios</text>
<example>
<phase name="1">IF(no .docs/ found) → THEN → [Continue with codebase analysis only → Log: Documentation not available] → END-IF</phase>
<phase name="2">IF(tech stack detection fails) → THEN → [Use manual fallback detection → Analyze file extensions and structure] → END-IF</phase>
<phase name="3">IF(web research fails) → THEN → [Use cached knowledge from vector memory → Continue with available information] → END-IF</phase>
<phase name="4">IF(brain list:includes fails) → THEN → [Use hardcoded standard includes list → Log: Include discovery failed] → END-IF</phase>
<phase name="5">IF(Brain.php generation fails) → THEN → [Preserve backup → Report detailed error → Provide manual configuration guidance] → END-IF</phase>
<phase name="6">IF(brain compile fails) → THEN → [Restore backup → Analyze compilation errors → Suggest fixes] → END-IF</phase>
<phase name="7">IF(vector memory storage fails) → THEN → [Continue without storage → Log: Memory storage unavailable] → END-IF</phase>
</example>
</guideline>
<guideline id="quality-gates">
<text>Validation checkpoints throughout initialization</text>
<example>Gate 1: Temporal context initialized (date, year, timestamp)</example>
<example>Gate 2: Project discovery completed with valid tech stack</example>
<example>Gate 3: At least one discovery task succeeded (docs OR codebase)</example>
<example>Gate 4: Includes recommendation generated with rationale</example>
<example>Gate 5: Brain.php backup created successfully</example>
<example>Gate 6: New Brain.php passes syntax validation</example>
<example>Gate 7: Compilation completes without errors</example>
<example>Gate 8: Compiled output exists at .claude/CLAUDE.md</example>
<example>Gate 9: At least one insight stored to vector memory</example>
</guideline>
<guideline id="example-laravel-project">
SCENARIO(Laravel project with comprehensive documentation)
<example>
<phase name="1">Discovery: Laravel 11, PHP 8.3, MySQL, Redis, Queue, Sanctum</phase>
<phase name="2">Docs: 15 .md files with architecture, requirements, domain logic</phase>
<phase name="3">Research: Laravel 2025 best practices, service container patterns</phase>
<phase name="4">Includes: LaravelBoostGuidelines, QualityGates, DDD patterns</phase>
<phase name="5">Custom Guidelines: Repository pattern rules, service layer conventions</phase>
<phase name="6">Result: Optimized Brain.php with Laravel-specific configuration</phase>
<phase name="7">Insights: 5 architectural insights stored to vector memory</phase>
</example>
</guideline>
<guideline id="example-node-project">
SCENARIO(Node.js/Express project without documentation)
<example>
<phase name="1">Discovery: Node.js 20, Express, TypeScript, MongoDB, Docker</phase>
<phase name="2">Docs: None found - codebase analysis only</phase>
<phase name="3">Research: Express 2025 patterns, TypeScript best practices</phase>
<phase name="4">Includes: CoreConstraints, ErrorRecovery, QualityGates</phase>
<phase name="5">Custom Guidelines: REST API conventions, middleware patterns</phase>
<phase name="6">Result: Brain.php with Node.js-aware configuration</phase>
<phase name="7">Insights: 3 tech stack insights stored</phase>
</example>
</guideline>
<guideline id="example-hybrid-project">
SCENARIO(Hybrid PHP/JavaScript project with microservices)
<example>
<phase name="1">Discovery: Laravel API + React SPA + Docker + Kafka</phase>
<phase name="2">Docs: Architectural decision records, API specs, deployment docs</phase>
<phase name="3">Research: Microservices patterns, event-driven architecture</phase>
<phase name="4">Includes: Multiple domain-specific includes + custom service layer</phase>
<phase name="5">Custom Guidelines: Microservice boundaries, event schemas, API versioning</phase>
<phase name="6">Result: Complex Brain.php with multi-paradigm support</phase>
<phase name="7">Insights: 12 cross-cutting concerns stored</phase>
</example>
</guideline>
<guideline id="performance-optimization">
<text>Optimization strategies for efficient initialization</text>
<example>
<phase name="1">Parallel Execution: All independent tasks run simultaneously</phase>
<phase name="2">Selective Reading: Only read files needed for analysis</phase>
<phase name="3">Incremental Storage: Store insights progressively, not at end</phase>
<phase name="4">Smart Caching: Leverage vector memory for repeated runs</phase>
<phase name="5">Early Validation: Fail fast on critical errors</phase>
<phase name="6">Streaming Output: Report progress as phases complete</phase>
</example>
</guideline>
<guideline id="directive">
<text>Core initialization directive</text>
<example>Discover thoroughly! Research current practices! Configure precisely! Validate rigorously! Store knowledge! Report comprehensively!</example>
</guideline>
</guidelines>
</command>