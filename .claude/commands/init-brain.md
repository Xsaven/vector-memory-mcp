---
name: "init-brain"
description: "Comprehensive Brain.php initialization - scans project, analyzes docs\/code, generates optimized configuration"
---

<command>
<meta>
<id>init-brain</id>
<description>Comprehensive Brain.php initialization - scans project, analyzes docs/code, generates optimized configuration</description>
</meta>
<execute>Discovers project context, analyzes docs/code, researches best practices, generates optimized .brain/node/Brain.php with project-specific guidelines, stores insights to vector memory</execute>
<provides>The InitBrain command automates smart distribution of project-specific configuration across Brain.php, Common.php, and Master.php based on project context discovery.</provides>

# Iron Rules
## Temporal-context-first (CRITICAL)
Temporal context MUST be initialized first: Bash('date +"%Y-%m-%d %H:%M:%S %Z"')
- **why**: Ensures all research and recommendations reflect current year best practices
- **on_violation**: Missing temporal context leads to outdated recommendations

## Parallel-research (CRITICAL)
Execute independent research tasks in parallel for efficiency
- **why**: Maximizes throughput and minimizes total execution time
- **on_violation**: Sequential execution wastes time on independent tasks

## Evidence-based (CRITICAL)
All Brain.php guidelines must be backed by discovered project evidence
- **why**: Prevents generic configurations that do not match project reality
- **on_violation**: Speculation leads to misaligned Brain behavior

## Vector-memory-storage (HIGH)
Store all significant insights to vector memory with semantic tags
- **why**: Enables future context retrieval and knowledge accumulation
- **on_violation**: Knowledge loss and inability to leverage past discoveries

## Preserve-variation (CRITICAL)
NEVER modify or replace existing #[Includes()] attributes on Brain.php Brain already has a Variation (e.g., Scrutinizer) - preserve it Standard includes from vendor/jarvis-brain/core/src/Includes are OFF LIMITS
- **why**: Variations are pre-configured brain personalities with carefully tuned includes
- **on_violation**: Modifying Variation breaks brain coherence and predefined behavior

## Project-includes-only (CRITICAL)
Only analyze and suggest includes from .brain/node/Includes/ FORBIDDEN: vendor/jarvis-brain/core/src/Includes/* modifications FORBIDDEN: Replacing or adding standard includes to Brain.php
- **why**: Standard includes are managed by Variations, not by init process
- **on_violation**: Standard includes are bundled with Variation - do not duplicate or override

## Smart-distribution (CRITICAL)
Distribute project-specific rules across THREE files to avoid duplication: .brain/node/Common.php - Shared by Brain AND all Agents .brain/node/Master.php - Shared by ALL Agents only (NOT Brain) .brain/node/Brain.php - Brain-specific only
- **why**: Prevents duplication across components, ensures single source of truth for each rule type
- **on_violation**: Rule placed in wrong file causes duplication or missing context

## Distribution-categories (CRITICAL)
COMMON: Environment (Docker, CI/CD), project tech stack, universal coding standards, shared config MASTER: Agent execution patterns, tool usage constraints, agent-specific guidelines, task handling BRAIN: Orchestration rules, delegation strategies, Brain-specific policies, workflow coordination
- **why**: Clear categorization ensures each file serves its specific purpose without overlap
- **on_violation**: Miscategorized rule leads to missing context or unnecessary duplication

## Incremental-enhancement (CRITICAL)
ALWAYS analyze existing file content BEFORE enhancement If file has rules/guidelines - PRESERVE valuable existing, ADD only missing NEVER blindly overwrite populated files - merge intelligently Compare discovered patterns with existing config to find gaps
- **why**: Preserves manual customizations and avoids losing valuable existing configuration
- **on_violation**: Valuable existing configuration lost, manual work discarded

## Extract-to-env-variables (CRITICAL)
ALL configurable values in generated code MUST use $this->var("KEY", default) WORKFLOW per file generation (6a, 6b, 7):   1. READ existing .brain/.env to get current variables   2. GENERATE code using $this->var("KEY", default) for configurable values   3. APPEND new variables to .env with # description and # variants: comments Variable candidates: thresholds, limits, toggles, versions, paths, model names Each variable: UPPER_SNAKE_CASE, sensible default, description, variants if applicable NEVER create empty/dummy variables - only those ACTUALLY USED in generated code
- **why**: Centralizes configuration, enables tuning without code changes, prevents magic values
- **on_violation**: Hardcoded values in code OR unused variables in .env


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($INIT_PARAMS = {initialization parameters extracted from $RAW_INPUT})

# Phase1 temporal context
GOAL(Initialize temporal awareness for all subsequent operations)
- `1`: Bash(date +"%Y-%m-%d") → [STORE-AS($CURRENT_DATE)] → END-Bash
- `2`: Bash(date +"%Y") → [STORE-AS($CURRENT_YEAR)] → END-Bash
- `3`: Bash(date +"%Y-%m-%d %H:%M:%S %Z") → [STORE-AS($TIMESTAMP)] → END-Bash
- `4`: 
VERIFY-SUCCESS(All temporal variables set)
NOTE(This ensures all research queries include current year for up-to-date results)


# Phase2 project discovery
GOAL(Discover project structure, technology stack, and patterns)
- NOTE(Execute all discovery tasks in parallel for efficiency)
- `parallel-discovery-tasks`: TASK →
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Check if .docs/ directory exists using Glob'."\\n"
    .'  Use Glob("**/.docs/**/*.md") to find documentation files'."\\n"
    .'  IF(.docs/ exists) →'."\\n"
    .'  Read all .md files from .docs/ directory'."\\n"
    .'  Extract: project goals, requirements, architecture decisions, domain terminology'."\\n"
    .'  STORE-AS($DOCS_CONTENT)'."\\n"
    .'→ ELSE →'."\\n"
    .'  No .docs/ found'."\\n"
    .'  STORE-AS($DOCS_CONTENT = null)'."\\n"
    .'→ END-IF'."\\n"
    .'→ END-TASK', 'CONTEXT(Documentation discovery for project context)')
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Analyze project root structure'."\\n"
    .'  Use Glob to find: composer.json, package.json, .env.example, README.md'."\\n"
    .'  Read key dependency files'."\\n"
    .'  Identify project type (Laravel, Node.js, hybrid, etc.)'."\\n"
    .'  Extract technology stack from dependency files'."\\n"
    .'  STORE-AS($PROJECT_TYPE)'."\\n"
    .'  STORE-AS($TECH_STACK = {languages: [...], frameworks: [...], packages: [...], services: [...]})'."\\n"
    .'→ END-TASK', 'CONTEXT(Codebase structure and tech stack analysis)')
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Scan for architectural patterns'."\\n"
    .'  Use Glob to find PHP/JS/TS files in app/ and src/ directories'."\\n"
    .'  Analyze code structure and organization'."\\n"
    .'  Identify: MVC, DDD, CQRS, microservices, monolith, etc.'."\\n"
    .'  Detect design patterns: repositories, services, factories, observers, etc.'."\\n"
    .'  Find coding conventions: naming, structure, organization'."\\n"
    .'  STORE-AS($ARCHITECTURE_PATTERNS = {architecture_style: "...", design_patterns: [...], conventions: [...]})'."\\n"
    .'→ END-TASK', 'CONTEXT(Architecture pattern discovery)')
  TASK →
  Read existing configuration files (known paths - no exploration needed):
  Read('.brain/node/Brain.php')
  Read('.brain/node/Common.php')
  Read('.brain/node/Master.php')
  For EACH file analyze handle() method content:
    - Extract existing $this->rule() definitions (id, severity, text)
    - Extract existing $this->guideline() definitions (id, phases, examples)
    - Identify custom logic and project-specific patterns
    - Mark as POPULATED if handle() has meaningful content beyond skeleton
  STORE-AS($CURRENT_BRAIN_CONFIG = {includes: [...], rules: [...], guidelines: [...], is_populated: bool})
  STORE-AS($CURRENT_COMMON_CONFIG = {rules: [...], guidelines: [...], is_populated: bool})
  STORE-AS($CURRENT_MASTER_CONFIG = {rules: [...], guidelines: [...], is_populated: bool})
→ END-TASK
→ END-TASK
- `2`: VERIFY-SUCCESS(All discovery tasks `completed`)
- `3`: STORE-AS($PROJECT_CONTEXT = Merged results from all discovery tasks)

# Phase2 5 environment discovery
GOAL(Discover environment configuration, containerization, and infrastructure patterns)
- NOTE(Environment rules go to Common.php - shared by Brain AND all Agents)
- `parallel-environment-tasks`: TASK →
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Use Glob to find: Dockerfile*, docker-compose*.yml, .dockerignore'."\\n"
    .'  Read Docker configurations if found'."\\n"
    .'  Extract: base images, services, ports, volumes, networks'."\\n"
    .'  Identify: container orchestration patterns (Docker Compose, K8s, etc.)'."\\n"
    .'  STORE-AS($DOCKER_CONFIG = {has_docker: bool, services: [...], patterns: [...]})'."\\n"
    .'→ END-TASK', 'CONTEXT(Docker and containerization discovery)')
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Use Glob to find: .github/workflows/*.yml, .gitlab-ci.yml, Jenkinsfile, bitbucket-pipelines.yml'."\\n"
    .'  Read CI/CD configurations if found'."\\n"
    .'  Extract: build steps, test runners, deployment targets'."\\n"
    .'  Identify: CI/CD platform and workflow patterns'."\\n"
    .'  STORE-AS($CICD_CONFIG = {platform: "...", workflows: [...], deployment_targets: [...]})'."\\n"
    .'→ END-TASK', 'CONTEXT(CI/CD pipeline discovery)')
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Use Glob to find: .editorconfig, .prettierrc*, .eslintrc*, phpcs.xml*, phpstan.neon*'."\\n"
    .'  Read linter/formatter configurations if found'."\\n"
    .'  Extract: code style rules, linting rules, analysis levels'."\\n"
    .'  Identify: tooling ecosystem (Prettier, ESLint, PHPStan, etc.)'."\\n"
    .'  STORE-AS($DEV_TOOLS_CONFIG = {formatters: [...], linters: [...], analyzers: [...]})'."\\n"
    .'→ END-TASK', 'CONTEXT(Development tooling discovery)')
  Task(@agent-explore, 'TASK →'."\\n"
    .'  Use Glob to find: .env.example, config/*.php, infrastructure/*'."\\n"
    .'  Analyze service connections: databases, caches, queues, storage'."\\n"
    .'  Identify: external service dependencies (AWS, GCP, Redis, Elasticsearch)'."\\n"
    .'  Map infrastructure topology'."\\n"
    .'  STORE-AS($INFRASTRUCTURE_CONFIG = {services: [...], external_deps: [...], topology: {...}})'."\\n"
    .'→ END-TASK', 'CONTEXT(Infrastructure and services discovery)')
→ END-TASK
- `2`: VERIFY-SUCCESS(Environment discovery `completed`)
- `3`: STORE-AS($ENVIRONMENT_CONTEXT = Merged environment configuration)

# Phase3 documentation analysis
GOAL(Deep analysis of project documentation to extract requirements and domain knowledge)
- `1`: IF(STORE-GET($DOCS_CONTENT) !== null) →
  Task(@agent-documentation-master, 'INPUT(STORE-GET($DOCS_CONTENT))', 'TASK →'."\\n"
    .'  Analyze all documentation files'."\\n"
    .'  Extract: project goals, requirements, constraints, domain concepts'."\\n"
    .'  Identify: key workflows, business rules, integration points'."\\n"
    .'  Map documentation to Brain configuration needs'."\\n"
    .'  Suggest: custom includes, rules, guidelines based on docs'."\\n"
    .'→ END-TASK', 'OUTPUT({goals: [...], requirements: [...], domain_concepts: [...], suggested_config: {...}})')
  STORE-AS($DOCS_ANALYSIS)
→ ELSE →
  No documentation found - will rely on codebase analysis only
  STORE-AS($DOCS_ANALYSIS = null)
→ END-IF

# Phase3 5 vector memory research

GOAL(Search vector memory for project-specific insights via direct MCP calls)
NOTE(Brain uses vector memory MCP tools directly - NO agent delegation needed Simple tool calls do not require agent orchestration overhead Multi-probe search: 2-3 focused queries per target file)

- `1`: Search vector memory for Common.php insights
- `2`: TASK →
  mcp__vector-memory__search_memories('{query: "environment Docker CI/CD containerization rules", limit: 5}')
  mcp__vector-memory__search_memories('{query: "tech stack PHP Node database coding standards", limit: 5}')
  mcp__vector-memory__search_memories('{query: "shared configuration infrastructure patterns", limit: 5}')
→ END-TASK
- `3`: STORE-AS($VECTOR_COMMON_INSIGHTS)
- `4`: Search vector memory for Master.php insights
- `5`: TASK →
  mcp__vector-memory__search_memories('{query: "agent execution patterns tool usage constraints", limit: 5}')
  mcp__vector-memory__search_memories('{query: "task handling decomposition estimation code generation", limit: 5}')
  mcp__vector-memory__search_memories('{query: "test writing conventions quality gates validation", limit: 5}')
→ END-TASK
- `6`: STORE-AS($VECTOR_MASTER_INSIGHTS)
- `7`: Search vector memory for Brain.php insights
- `8`: TASK →
  mcp__vector-memory__search_memories('{query: "orchestration delegation strategies agent selection", limit: 5}')
  mcp__vector-memory__search_memories('{query: "workflow coordination response synthesis validation", limit: 5}')
  mcp__vector-memory__search_memories('{query: "context management memory limits Brain policies", limit: 5}')
→ END-TASK
- `9`: STORE-AS($VECTOR_BRAIN_INSIGHTS)
- `10`: TASK →
  FILTER vector memory results:
    - Extract UNIQUE insights not in standard includes
    - Categorize by target file (Common/Master/Brain)
    - Reject duplicates and generic knowledge
  STORE-AS($VECTOR_CRITICAL_INSIGHTS = {common: [...], master: [...], brain: [...]})
→ END-TASK

# Phase4 project includes

GOAL(Analyze and suggest PROJECT-SPECIFIC includes only (NOT standard includes))
NOTE(IMPORTANT: Brain already has a Variation with standard includes configured This phase focuses ONLY on .brain/node/Includes/ FORBIDDEN: Suggesting or modifying vendor/jarvis-brain/core/src/Includes/* Brain analyzes ExploreMaster results directly - no additional agent needed)

- `1`: Task(@agent-explore, 'TASK →'."\\n"
    .'  Scan .brain/node/Includes/ for existing project includes'."\\n"
    .'  Read each include file to understand its purpose'."\\n"
    .'  Identify gaps in project-specific configuration'."\\n"
    .'→ END-TASK', 'CONTEXT(Project-specific includes discovery)')
- `2`: STORE-AS($EXISTING_PROJECT_INCLUDES)
- `3`: TASK →
  Brain analyzes EXISTING_PROJECT_INCLUDES directly:
    - Map discovered includes to project needs from PROJECT_CONTEXT
    - Identify MISSING project-specific includes based on DOCS_ANALYSIS
    - DO NOT suggest standard includes from vendor/jarvis-brain/core/src/Includes
    - Generate list of new project includes to create via brain make:include
  STORE-AS($PROJECT_INCLUDES_RECOMMENDATION = {existing: [...], suggested_new: [...], rationale: {...}})
→ END-TASK

# Phase5 smart distribution

GOAL(Categorize discovered rules/guidelines into Common, Master, or Brain files)
NOTE(CRITICAL: Each rule MUST go to exactly ONE file to avoid duplication .brain/node/Common.php - Shared by Brain AND all Agents .brain/node/Master.php - Shared by ALL Agents only .brain/node/Brain.php - Brain-specific only Brain performs categorization directly - simple logic, no agent needed)

- `1`: TASK →
  Brain categorizes ALL discovered patterns into target files:
  
  INPUT: PROJECT_CONTEXT, ENVIRONMENT_CONTEXT, DOCS_ANALYSIS, ARCHITECTURE_PATTERNS, VECTOR_CRITICAL_INSIGHTS
  
  COMMON.PHP (Brain + ALL Agents):
    - Docker/container environment rules (ports, services, networks)
    - CI/CD pipeline awareness (test commands, build steps)
    - Project tech stack rules (PHP version, Node version, database type)
    - Universal coding standards (naming conventions, file structure)
    - Shared configuration (env vars, paths, external services)
    - Development tooling rules (linters, formatters, analyzers)
  
  MASTER.PHP (ALL Agents only, NOT Brain):
    - Agent execution patterns (how agents should approach tasks)
    - Tool usage constraints (when to use which tools)
    - Task handling guidelines (decomposition, estimation, status flow)
    - Code generation patterns (templates, scaffolding)
    - Test writing conventions (test structure, coverage expectations)
    - Agent-specific quality gates (validation before completion)
  
  BRAIN.PHP (Brain-specific only):
    - Orchestration rules (delegation strategies, agent selection)
    - Brain-specific policies (approval chains, escalation)
    - Workflow coordination (multi-agent orchestration)
    - Response synthesis (how to merge agent results)
    - Brain-level validation (response quality gates)
  
  Generate PHP Builder API code for each category
  Use $this->rule() for constraints, $this->guideline() for patterns
  STORE-AS($DISTRIBUTED_GUIDELINES = {common: [...], master: [...], brain: [...]})
→ END-TASK

# Phase5a common enhancement

GOAL(Enhance Common.php with shared project rules for Brain AND all Agents)
NOTE(Common.php is included by BOTH BrainIncludesTrait AND AgentIncludesTrait Rules here apply universally - avoid agent-specific or brain-specific content Focus: environment, tech stack, coding standards, shared configuration)

- `1`: Read existing Common.php and .env
- `2`: TASK →
  Read('.brain/node/Common.php')
  IF(.brain/.env exists) →
  Read('.brain/.env')
  STORE-AS($EXISTING_ENV)
→ ELSE →
  STORE-AS($EXISTING_ENV = )
→ END-IF
→ END-TASK
- `3`: STORE-AS($CURRENT_COMMON_CONFIG)
- `4`: Task(@agent-prompt-master, 'INPUT(STORE-GET($CURRENT_COMMON_CONFIG) && STORE-GET($DISTRIBUTED_GUIDELINES.COMMON) && STORE-GET($ENVIRONMENT_CONTEXT))', 'TASK →'."\\n"
    .'  PRESERVE existing class structure, namespace, and extends IncludeArchetype'."\\n"
    .'  IF(CURRENT_COMMON_CONFIG.is_populated) →'."\\n"
    .'  MERGE MODE: File has existing content'."\\n"
    .'    - KEEP all existing rules/guidelines that are still relevant'."\\n"
    .'    - UPDATE rules if new discovery provides better info (same id, improved text)'."\\n"
    .'    - ADD only NEW rules/guidelines not already present'."\\n"
    .'    - REMOVE nothing unless explicitly obsolete'."\\n"
    .'    - Compare rule IDs to avoid duplicates'."\\n"
    .'→ ELSE →'."\\n"
    .'  FRESH MODE: File is empty/skeleton - add all discovered rules'."\\n"
    .'→ END-IF'."\\n"
    .'  Focus on environment and universal rules:'."\\n"
    .'    - Docker/container configuration awareness'."\\n"
    .'    - Tech stack version constraints'."\\n"
    .'    - Universal coding conventions'."\\n"
    .'    - Shared infrastructure knowledge'."\\n"
    .'  '."\\n"
    .'  CRITICAL - GENERATE CODE WITH $this->var() IMMEDIATELY:'."\\n"
    .'    WRONG: ->text("PHP version must be 8.3")'."\\n"
    .'    RIGHT: ->text(["PHP version must be", $this->var("PHP_VERSION", "8.3")])'."\\n"
    .'    WRONG: $limit = 100;'."\\n"
    .'    RIGHT: $limit = $this->var("MAX_LINE_LENGTH", 100);'."\\n"
    .'  '."\\n"
    .'    For EACH configurable value in generated code:'."\\n"
    .'      1. USE $this->var("KEY", default) IN THE CODE IMMEDIATELY'."\\n"
    .'      2. COLLECT to env_vars: {name: "KEY", default: "value", description: "...", variants: "..."}'."\\n"
    .'  '."\\n"
    .'    Candidates: PHP_VERSION, NODE_VERSION, DATABASE_TYPE, DOCKER_ENABLED'."\\n"
    .'    Candidates: PHPSTAN_LEVEL, TEST_COVERAGE_MIN, MAX_LINE_LENGTH'."\\n"
    .'  '."\\n"
    .'  Apply prompt engineering: clarity, brevity, token efficiency'."\\n"
    .'→ END-TASK', 'OUTPUT({common_php_content: "...", rules_kept: [...], rules_added: [...], rules_updated: [...], env_vars: [{name, default, description, variants}]})')
- `5`: Brain receives PromptMaster response with content + env_vars
- `6`: STORE-AS($ENHANCED_COMMON_PHP)
- `7`: TASK →
  Write .brain/node/Common.php from ENHANCED_COMMON_PHP.common_php_content
  IF(ENHANCED_COMMON_PHP.env_vars not empty) →
  APPEND to .brain/.env:
    # ═══ COMMON ═══ (if not already present)
    For EACH env_var in ENHANCED_COMMON_PHP.env_vars:
      IF var.name NOT in EXISTING_ENV:
        # {var.description}
        # variants: {var.variants}
        {var.name}={var.default}
→ END-IF
→ END-TASK
- `8`: NOTE(Common.php written + new env vars appended to .env)

# Phase5b master enhancement

GOAL(Enhance Master.php with agent-specific rules shared by ALL Agents)
NOTE(Master.php is included by AgentIncludesTrait only (NOT Brain) Rules here apply to all agents but NOT to Brain orchestration Focus: execution patterns, tool usage, task handling, code generation)

- `1`: Read existing Master.php
- `2`: Read('.brain/node/Master.php')
- `3`: STORE-AS($CURRENT_MASTER_CONFIG)
- `4`: Task(@agent-prompt-master, 'INPUT(STORE-GET($CURRENT_MASTER_CONFIG) && STORE-GET($DISTRIBUTED_GUIDELINES.MASTER) && STORE-GET($ARCHITECTURE_PATTERNS))', 'TASK →'."\\n"
    .'  PRESERVE existing class structure, namespace, and extends IncludeArchetype'."\\n"
    .'  IF(CURRENT_MASTER_CONFIG.is_populated) →'."\\n"
    .'  MERGE MODE: File has existing content'."\\n"
    .'    - KEEP all existing rules/guidelines that are still relevant'."\\n"
    .'    - UPDATE rules if new discovery provides better info (same id, improved text)'."\\n"
    .'    - ADD only NEW rules/guidelines not already present'."\\n"
    .'    - REMOVE nothing unless explicitly obsolete'."\\n"
    .'    - Compare rule IDs to avoid duplicates'."\\n"
    .'→ ELSE →'."\\n"
    .'  FRESH MODE: File is empty/skeleton - add all discovered rules'."\\n"
    .'→ END-IF'."\\n"
    .'  Focus on agent execution patterns:'."\\n"
    .'    - How agents should approach project tasks'."\\n"
    .'    - Tool usage patterns for this project'."\\n"
    .'    - Code generation conventions'."\\n"
    .'    - Test writing patterns'."\\n"
    .'    - Quality gates before task completion'."\\n"
    .'  '."\\n"
    .'  CRITICAL - GENERATE CODE WITH $this->var() IMMEDIATELY:'."\\n"
    .'    WRONG: ->text("Max task estimate is 8 hours")'."\\n"
    .'    RIGHT: ->text(["Max task estimate is", $this->var("MAX_TASK_ESTIMATE_HOURS", 8), "hours"])'."\\n"
    .'    WRONG: $model = "sonnet";'."\\n"
    .'    RIGHT: $model = $this->var("DEFAULT_AGENT_MODEL", "sonnet");'."\\n"
    .'  '."\\n"
    .'    For EACH configurable value in generated code:'."\\n"
    .'      1. USE $this->var("KEY", default) IN THE CODE IMMEDIATELY'."\\n"
    .'      2. COLLECT to env_vars: {name: "KEY", default: "value", description: "...", variants: "..."}'."\\n"
    .'  '."\\n"
    .'    Candidates: MAX_TASK_ESTIMATE_HOURS, DEFAULT_AGENT_MODEL, PARALLEL_TASKS'."\\n"
    .'    Candidates: REQUIRE_TESTS, MIN_COVERAGE, CODE_REVIEW_ENABLED'."\\n"
    .'  '."\\n"
    .'  Apply prompt engineering: clarity, brevity, token efficiency'."\\n"
    .'→ END-TASK', 'OUTPUT({master_php_content: "...", rules_kept: [...], rules_added: [...], rules_updated: [...], env_vars: [{name, default, description, variants}]})')
- `5`: Brain receives PromptMaster response with content + env_vars
- `6`: STORE-AS($ENHANCED_MASTER_PHP)
- `7`: TASK →
  Write .brain/node/Master.php from ENHANCED_MASTER_PHP.master_php_content
  IF(ENHANCED_MASTER_PHP.env_vars not empty) →
  APPEND to .brain/.env:
    # ═══ MASTER ═══ (if not already present)
    For EACH env_var in ENHANCED_MASTER_PHP.env_vars:
      IF var.name NOT in EXISTING_ENV:
        # {var.description}
        # variants: {var.variants}
        {var.name}={var.default}
→ END-IF
→ END-TASK
- `8`: NOTE(Master.php written + new env vars appended to .env)

# Phase6 brain enhancement

GOAL(Enhance Brain.php with Brain-specific orchestration rules ONLY)
NOTE(CRITICAL: Preserve ALL existing #[Includes()] attributes - they define the Variation ONLY add Brain-specific rules (orchestration, delegation, synthesis) Common rules go to Common.php, agent rules go to Master.php)

- `1`: Enhance handle() method with Brain-specific content only
- `2`: Task(@agent-prompt-master, 'INPUT(STORE-GET($CURRENT_BRAIN_CONFIG) && STORE-GET($PROJECT_INCLUDES_RECOMMENDATION) && STORE-GET($DISTRIBUTED_GUIDELINES.BRAIN) && STORE-GET($PROJECT_CONTEXT))', 'TASK →'."\\n"
    .'  PRESERVE existing #[Includes()] attributes (Variation) - DO NOT MODIFY'."\\n"
    .'  PRESERVE existing class structure and namespace'."\\n"
    .'  IF(CURRENT_BRAIN_CONFIG.is_populated) →'."\\n"
    .'  MERGE MODE: File has existing handle() content'."\\n"
    .'    - KEEP all existing rules/guidelines in handle() that are still relevant'."\\n"
    .'    - UPDATE rules if new discovery provides better info (same id, improved text)'."\\n"
    .'    - ADD only NEW Brain-specific rules not already present'."\\n"
    .'    - REMOVE nothing unless explicitly obsolete'."\\n"
    .'    - Compare rule IDs to avoid duplicates'."\\n"
    .'→ ELSE →'."\\n"
    .'  FRESH MODE: handle() is empty/skeleton - add all Brain-specific rules'."\\n"
    .'→ END-IF'."\\n"
    .'  Focus on Brain-specific rules only (Common/Master rules already distributed):'."\\n"
    .'    - Orchestration and delegation strategies'."\\n"
    .'    - Agent selection criteria for this project'."\\n"
    .'    - Response synthesis patterns'."\\n"
    .'    - Brain-level validation gates'."\\n"
    .'  '."\\n"
    .'  CRITICAL - GENERATE CODE WITH $this->var() IMMEDIATELY:'."\\n"
    .'    WRONG: ->text("Default model is sonnet")'."\\n"
    .'    RIGHT: ->text(["Default model is", $this->var("DEFAULT_MODEL", "sonnet")])'."\\n"
    .'    WRONG: $depth = 2;'."\\n"
    .'    RIGHT: $depth = $this->var("MAX_DELEGATION_DEPTH", 2);'."\\n"
    .'  '."\\n"
    .'    For EACH configurable value in generated code:'."\\n"
    .'      1. USE $this->var("KEY", default) IN THE CODE IMMEDIATELY'."\\n"
    .'      2. COLLECT to env_vars: {name: "KEY", default: "value", description: "...", variants: "..."}'."\\n"
    .'  '."\\n"
    .'    Candidates: DEFAULT_MODEL, MAX_DELEGATION_DEPTH, VALIDATION_THRESHOLD'."\\n"
    .'    Candidates: ENABLE_PARALLEL_AGENTS, MAX_RETRIES, RESPONSE_MAX_TOKENS'."\\n"
    .'  '."\\n"
    .'  If suggested new project includes, add to #[Includes()] AFTER existing'."\\n"
    .'  Apply prompt engineering: clarity, brevity, token efficiency'."\\n"
    .'→ END-TASK', 'OUTPUT({brain_php_content: "...", preserved_variation: "...", rules_kept: [...], rules_added: [...], rules_updated: [...], env_vars: [{name, default, description, variants}]})')
- `3`: Brain receives PromptMaster response with content + env_vars
- `4`: STORE-AS($ENHANCED_BRAIN_PHP)
- `5`: TASK →
  Write .brain/node/Brain.php from ENHANCED_BRAIN_PHP.brain_php_content
  IF(ENHANCED_BRAIN_PHP.env_vars not empty) →
  APPEND to .brain/.env:
    # ═══ BRAIN ═══ (if not already present)
    For EACH env_var in ENHANCED_BRAIN_PHP.env_vars:
      IF var.name NOT in EXISTING_ENV:
        # {var.description}
        # variants: {var.variants}
        {var.name}={var.default}
→ END-IF
→ END-TASK
- `6`: NOTE(Brain.php written + new env vars appended to .env)

# Phase7 compilation
GOAL(Validate syntax and compile all enhanced files)
- `1`: Validate PHP syntax for all modified files
- `2`: TASK →
  Bash(php -l .brain/node/Common.php) → [Validate Common.php syntax] → END-Bash
  Bash(php -l .brain/node/Master.php) → [Validate Master.php syntax] → END-Bash
  Bash(php -l .brain/node/Brain.php) → [Validate Brain.php syntax] → END-Bash
→ END-TASK
- `3`: IF(any syntax validation failed) →
  Report syntax errors with file:line details
  Provide fix suggestions
  OUTPUT(Syntax validation failed - review errors above)
→ END-IF
- `4`: Compile Brain ecosystem
- `5`: Bash(brain compile) → [Compile .brain/node/Brain.php with includes to .claude/CLAUDE.md] → END-Bash
- `6`: VERIFY-SUCCESS(Compilation succeeded .claude/CLAUDE.md exists No compilation errors Common.php included via BrainIncludesTrait Master.php available for AgentIncludesTrait)
- `7`: IF(compilation failed) →
  Report compilation errors with details
  Provide fix suggestions
  OUTPUT(Compilation failed - review errors above)
→ END-IF

# Phase8 knowledge storage
GOAL(Store all insights to vector memory for future reference)
- `1`: mcp__vector-memory__store_memory('INPUT(content: "Brain Initialization - Project: {project_type}, Tech Stack: {tech_stack}, Patterns: {architecture_patterns}, Date: {current_date}" && category: "architecture" && tags: ["init-brain", "project-discovery", "configuration"])')
- `2`: mcp__vector-memory__store_memory('INPUT(content: "Environment Discovery - Docker: {has_docker}, CI/CD: {cicd_platform}, Dev Tools: {dev_tools}, Date: {current_date}" && category: "architecture" && tags: ["init-brain", "environment", "infrastructure"])')
- `3`: mcp__vector-memory__store_memory('INPUT(content: "Smart Distribution - Common: {common_rules_count} rules, Master: {master_rules_count} rules, Brain: {brain_rules_count} rules, Date: {current_date}" && category: "architecture" && tags: ["init-brain", "distribution", "configuration"])')
- `4`: mcp__vector-memory__store_memory('INPUT(content: "Vector Memory Mining - Common: {vector_common_count}, Master: {vector_master_count}, Brain: {vector_brain_count}, Total `validated`: {vector_total_validated}, Date: {current_date}" && category: "learning" && tags: ["init-brain", "vector-mining", "insights"])')

# Phase9 report
GOAL(Generate comprehensive initialization report with smart distribution summary)
- `1`: OUTPUT(Brain Ecosystem Initialization Complete  ═══════════════════════════════════════════════════════ SMART DISTRIBUTION SUMMARY ═══════════════════════════════════════════════════════  .brain/node/Common.php (Brain + ALL Agents):   Mode: {common_mode}   Kept: {common_rules_kept} | Added: {common_rules_added} | Updated: {common_rules_updated}   ENV vars: {common_env_count}  .brain/node/Master.php (ALL Agents only):   Mode: {master_mode}   Kept: {master_rules_kept} | Added: {master_rules_added} | Updated: {master_rules_updated}   ENV vars: {master_env_count}  .brain/node/Brain.php (Brain only):   Variation: {existing_variation_name} (PRESERVED)   Mode: {brain_mode}   Kept: {brain_rules_kept} | Added: {brain_rules_added} | Updated: {brain_rules_updated}   ENV vars: {brain_env_count}  ═══════════════════════════════════════════════════════ DISCOVERY RESULTS ═══════════════════════════════════════════════════════  Project:   Type: {project_type}   Tech Stack: {tech_stack}   Architecture: {architecture_patterns}  Environment:   Docker: {has_docker}   CI/CD Platform: {cicd_platform}   Dev Tools: {dev_tools}   Infrastructure: {infrastructure_services}  Documentation:   Files Analyzed: {docs_file_count}   Domain Concepts: {domain_concepts_count}   Requirements: {requirements_count}  Vector Memory Mining:   Total Mined: {vector_total_mined}   Critical Filtered: {vector_critical_count}   Added to Common: {vector_common_count}   Added to Master: {vector_master_count}   Added to Brain: {vector_brain_count}  ═══════════════════════════════════════════════════════ OUTPUT FILES ═══════════════════════════════════════════════════════  Source Files:   .brain/node/Brain.php   .brain/node/Common.php   .brain/node/Master.php  Compiled Output:   .claude/CLAUDE.md  Configuration:   .brain/.env   Variables: {env_settings_count} ({env_kept} kept, {env_added} added)  ═══════════════════════════════════════════════════════ VECTOR MEMORY ═══════════════════════════════════════════════════════    Insights Stored: {insights_count}   Categories: architecture, learning   Tags: init-brain, project-discovery, distribution  ═══════════════════════════════════════════════════════ NEXT STEPS ═══════════════════════════════════════════════════════    1. Review enhanced files:      - Common.php: shared environment/coding rules      - Master.php: agent execution patterns      - Brain.php: orchestration rules (Variation preserved)    2. If project includes suggested:      brain make:include {name}    3. Test Brain behavior with sample tasks    4. After any modifications:      brain compile    5. Consider running:      /init-agents for agent generation      /init-vector for vector memory population)

# Error recovery
Comprehensive error handling for all `failure` scenarios
- `1`: IF(no .docs/ found) →
  Continue with codebase analysis only
  Log: Documentation not available
→ END-IF
- `2`: IF(tech stack detection fails) →
  Use manual fallback detection
  Analyze file extensions and structure
→ END-IF
- `3`: IF(vector memory research fails) →
  Continue with codebase-only discovery
  Log: Vector memory unavailable
→ END-IF
- `4`: IF(brain list:includes fails) →
  Use hardcoded standard includes list
  Log: Include discovery failed
→ END-IF
- `5`: IF(Brain.php generation fails) →
  Report detailed error with file:line
  Provide manual fix guidance
→ END-IF
- `6`: IF(brain compile fails) →
  Analyze compilation errors
  Provide fix suggestions
→ END-IF
- `7`: IF(vector memory storage fails) →
  Continue without storage
  Log: Memory storage unavailable
→ END-IF

# Quality gates
Validation checkpoints throughout initialization
- Gate 1: Temporal context initialized (date, year, timestamp)
- Gate 2: Project discovery `completed` with valid tech stack
- Gate 3: Environment discovery `completed` (Docker, CI/CD, Dev Tools)
- Gate 4: At least one discovery task succeeded (docs OR codebase)
- Gate 5: Smart distribution categorization `completed` (Common/Master/Brain)
- Gate 6: All enhanced files written successfully
- Gate 7: All enhanced files pass PHP syntax validation
- Gate 8: Compilation completes without errors
- Gate 9: Compiled output exists at .claude/CLAUDE.md
- Gate 10: At least one insight stored to vector memory

# Example laravel docker project
SCENARIO(Laravel project with Docker, Sail, and comprehensive documentation)
- `1`: Discovery: Laravel 11, PHP 8.3, MySQL, Redis, Queue, Sanctum
- `2`: Environment: Docker (Sail), GitHub Actions CI/CD, PHPStan L8
- `3`: Docs: 15 .md files with architecture, requirements, domain logic
- `4`: Vector Mining: 12 insights found, 8 `validated` for distribution
- `6`: SMART DISTRIBUTION:
- `7`:   Common.php: Docker/Sail environment rules, PHP 8.3 type constraints, MySQL conventions
- `8`:   Master.php: Service class patterns, repository usage, Pest test conventions
- `9`:   Brain.php: Agent delegation for Laravel domains (Auth, Queue, Cache)
- `11`: Result: All three files enhanced, Scrutinizer Variation preserved
- `12`: Insights: 8 architectural insights stored to vector memory

# Example node docker project
SCENARIO(Node.js/Express project with Docker and TypeScript)
- `1`: Discovery: Node.js 20, Express, TypeScript, MongoDB
- `2`: Environment: Docker Compose, GitLab CI, ESLint + Prettier
- `3`: Docs: None found - codebase analysis only
- `4`: Vector Mining: 7 insights found, 5 `validated` for distribution
- `6`: SMART DISTRIBUTION:
- `7`:   Common.php: Docker network rules, Node 20 constraints, ESLint compliance
- `8`:   Master.php: TypeScript type generation, async/await patterns, Jest test structure
- `9`:   Brain.php: API route delegation strategy
- `11`: Result: All three files enhanced, Architect Variation preserved
- `12`: Insights: 5 tech stack insights stored

# Example hybrid microservices
SCENARIO(Hybrid PHP/JavaScript microservices with Kubernetes)
- `1`: Discovery: Laravel API + React SPA + Docker + Kafka
- `2`: Environment: Kubernetes, GitHub Actions, PHPStan + ESLint
- `3`: Docs: ADRs, API specs, deployment docs, domain model
- `4`: Vector Mining: 18 insights found, 12 `validated` for distribution
- `6`: SMART DISTRIBUTION:
- `7`:   Common.php: K8s service discovery, cross-service authentication, Kafka topic naming
- `8`:   Master.php: Event schema validation, API contract testing, service boundary respect
- `9`:   Brain.php: Multi-service orchestration, cross-domain delegation, event saga coordination
- `11`: Project Includes: Suggested MicroserviceBoundaries.php, EventSchemas.php
- `12`: Result: All three files enhanced with microservice awareness
- `13`: Insights: 12 cross-cutting concerns stored

# Performance optimization
Optimization strategies for efficient initialization
- `1`: Parallel Execution: All independent tasks run simultaneously
- `2`: Selective Reading: Only read files needed for analysis
- `3`: Incremental Storage: Store insights progressively, not at end
- `4`: Smart Caching: Leverage vector memory for repeated runs
- `5`: Early Validation: Fail fast on critical errors
- `6`: Streaming Output: Report progress as phases complete

# Directive
Core initialization directive
- Discover thoroughly! Research current practices! Configure precisely! Validate rigorously! Store knowledge! Report comprehensively!

</command>