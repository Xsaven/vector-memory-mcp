# Jarvis Brain Node/Core

## Purpose
Two-package system for AI agent orchestration via declarative configuration:
- `jarvis-brain/core` – DTO-based blueprints, merger, format builders (XML/JSON/YAML/TOML)
- `jarvis-brain/node` – Executable node with archetypes (Brain, Agents, Skills, Commands, Includes, Mcp)

## Architecture

### Compilation Flow
```
.brain/node/*.php → brain compile → brain-core get:file --xml/json/yaml/toml → .claude/CLAUDE.md + agents/ + skills/ + commands/ + .mcp.json
```

### Compilation Variables

| Variable | Description | Claude Example |
|----------|-------------|----------------|
| `{{ PROJECT_DIRECTORY }}` | Root path | `/Users/xsaven/...` |
| `{{ BRAIN_DIRECTORY }}` | Brain dir | `.brain/` |
| `{{ NODE_DIRECTORY }}` | Source dir | `.brain/node/` |
| `{{ BRAIN_FILE }}` | Brain output | `.claude/CLAUDE.md` |
| `{{ BRAIN_FOLDER }}` | Brain folder | `.claude/` |
| `{{ AGENTS_FOLDER }}` | Agents folder | `.claude/agents/` |
| `{{ COMMANDS_FOLDER }}` | Commands folder | `.claude/commands/` |
| `{{ SKILLS_FOLDER }}` | Skills folder | `.claude/skills/` |
| `{{ MCP_FILE }}` | MCP config | `.mcp.json` |
| `{{ AGENT }}` | Target | `claude/codex/qwen/gemini` |
| `{{ DATE }}` | Date | `2025-11-03` |
| `{{ YEAR }}` | Year | `2025` |
| `{{ TIMESTAMP }}` | Unix timestamp | `1730654730` |

### Directory Structure
```
./
├── .brain/node/        # Source (Brain, Agents, Skills, Commands, Includes, Mcp)
├── core/src/           # Core (Archetypes, Blueprints, Includes, Merger, XmlBuilder)
├── .claude/            # Compiled output (DO NOT EDIT)
├── .mcp.json           # MCP configs
└── memory/             # Vector storage (MCP-only)
```

## Brain Includes

### Brain Files (core/src/Includes/Brain/)
1. BrainCore.php - Base rules, meta, style, response contract
2. PreActionValidation.php - Pre-action validation
3. AgentDelegation.php - Delegation stub
4. AgentResponseValidation.php - Post-execution validation
5. CognitiveArchitecture.php - Reasoning framework
6. CollectiveIntelligencePhilosophy.php - Multi-agent philosophy
7. CompactionRecovery.php - Context compaction
8. ContextAnalysis.php - Context awareness
9. CorrectionProtocolEnforcement.php - Error correction
10. DelegationProtocols.php - Task delegation
11. EdgeCases.php - Boundary scenarios

### Universal Files (core/src/Includes/Universal/)
1. AgentLifecycleFramework.php - 4-phase lifecycle
2. CoreConstraints.php - System constraints
3. ErrorRecovery.php - Error handling
4. InstructionWritingStandards.php - Instruction standards
5. LaravelBoostGuidelines.php - Laravel optimization
6. QualityGates.php - Quality checkpoints
7. ResponseFormatting.php - Response validation
8. SequentialReasoningCapability.php - Reasoning framework
9. VectorMasterStorageStrategy.php - Memory architecture

**Characteristics:**
- Only BrainCore.php has `#[Meta()]`; others use `#[Purpose()]`
- No XML references (descriptive names only)
- All extend BrainArchetype, implement handle()
- Includes merge at compile time

## Workflow Pseudo-Syntax

Declarative language for workflows embedded in guidelines. Compiles PHP → human-readable instructions.

### Core Operators

**Tools:**
```php
BashTool::call('date')                    // → Bash('date')
ReadTool::call(Runtime::NODE_DIRECTORY()) // → Read('.brain/node/')
TaskTool::agent('name', 'task')           // → Task(@agent-name 'task')
WebSearchTool::describe('query')          // → WebSearch(query)
```

**Storage:**
```php
Store::as('VAR')                          // → STORE-AS($VAR)
Store::get('VAR')                         // → STORE-GET($VAR)
```

**Control Flow:**
```php
Operator::task([...])                     // → TASK → [...] → END-TASK
Operator::if('cond', 'then', 'else')     // → IF(cond) → THEN → [...] → ELSE → [...] → END-IF
Operator::forEach('item', [...])          // → FOREACH(item) → [...] → END-FOREACH
Operator::skip('reason')                  // → SKIP(reason)
Operator::report('msg')                   // → REPORT(msg)
Operator::verify(...)                     // → VERIFY-SUCCESS(...)
Operator::output('format')                // → OUTPUT(format)
Operator::input(...)                      // → INPUT(...)
Operator::context('data')                 // → CONTEXT(data)
Operator::note('text')                    // → NOTE(text)
```

**Runtime Constants:**
```php
Runtime::BRAIN_FILE                       // → .claude/CLAUDE.md
Runtime::NODE_DIRECTORY('path')           // → .brain/node/path
Runtime::AGENTS_FOLDER                    // → .claude/agents/
BrainCLI::COMPILE                         // → brain compile
BrainCLI::MAKE_MASTER('Name')            // → brain make:master Name
BrainCLI::MASTER_LIST                     // → brain master:list
BrainCLI::LIST_INCLUDES                     // → brain list:includes
```

**Agent Delegation:**
```php
ExploreMaster::call(...)                  // → Task(@agent-explore, ...)
AgentMaster::call(...)                    // → Task(@agent-agent-master, ...)
WebResearchMaster::call(...)              // → Task(@agent-web-research-master, ...)
```

**MCP:**
```php
VectorMemoryMcp::call('store_memory', '{...}')  // → mcp__vector-memory__store_memory('{...}')
```

### Compilation Rules
1. PHP static methods → compiled function calls
2. Nested Operator::* → nested blocks with END markers
3. Store::as('VAR') → $VAR
4. Agent class names → kebab-case @agent- prefix
5. Runtime constants → platform-specific paths

## Builder API Syntax

### Rules
```php
$this->rule('id')->critical()
    ->text('Description')
    ->why('Reason')
    ->onViolation('Action');
```
Severity: `critical()`, `high()`, `medium()`, `low()`

### Guidelines
```php
$this->guideline('id')
    ->text('Description')
    ->example('text');

// With phases
$this->guideline('id')
    ->example()
    ->phase('step-1', 'Logic')
    ->phase('step-2', 'Validation');

// Key-value
$this->guideline('id')
    ->example('value')->key('key');
```

### Other
```php
$this->style()
    ->language('English')
    ->tone('Analytical')
    ->brevity('Medium');

$this->response()->sections()
    ->section('name', 'brief', true);

$this->determinism()
    ->ordering('stable')
    ->randomness('off');
```

## Node Archetypes

### Brain (.brain/node/Brain.php)
- Main orchestrator, delegates tasks
- Output: `.claude/CLAUDE.md`
- Includes Universal + Brain-specific includes
- NEVER executes directly

### Agents (.brain/node/Agents/*.php)
- Specialized execution units
- Output: `.claude/agents/{name}.md`
- Invoked by Brain via Task tool

### Skills (.brain/node/Skills/*.php)
- Reusable capability modules
- Output: `.claude/skills/{name}.md`
- Shared across agents

### Commands (.brain/node/Commands/*.php)
- User slash commands
- Output: `.claude/commands/{name}.md`
- NOT for agent-to-agent communication

### Includes (.brain/node/Includes/*.php)
- Compile-time fragments
- NO output (merged into targets)
- Max depth: 255 levels
- DRY principle

### Mcp (.brain/node/Mcp/*.php)
- MCP server configs
- Output: `.mcp.json`
- JSON structure only

## Include System

**Compile-time merging:** Includes merge during compilation, disappear at runtime.

**Flow:** Source includes → Merger flattens → Builder outputs → Compiled (no include references)

**Recursive:** Includes can include includes up to 255 levels.

**DRY:** Change once → recompile → all targets updated.

## Multi-Target Support

| Target | Format | Builder |
|--------|--------|---------|
| claude | XML | XmlBuilder → `.claude/CLAUDE.md` |
| codex  | JSON | JsonBuilder → `.codex/CODEX.json` |
| qwen   | YAML | YamlBuilder → `.qwen/QWEN.yaml` |
| gemini | JSON | JsonBuilder → `.gemini/GEMINI.json` |

**Command:** `brain compile [target]`

## Memory Architecture

**Location:** `./memory/` (SQLite)

**Access:** MCP-only (NEVER direct file access)

**Tools:**
- store_memory, search_memories, list_recent_memories
- get_by_memory_id, delete_by_memory_id
- get_memory_stats, clear_old_memories

**Topology:**
- Master: Brain (exclusive write)
- Replica: Agents (read-only, cached)
- Sync: Async every 5min, consistency ≤10min

**Categories:** code-solution, bug-fix, architecture, learning, tool-usage, debugging, performance, security, other

## CLI Commands

### Brain CLI
```bash
brain compile [target]      # Compile (default: claude)
brain init                  # Initialize project
brain make:master Name      # Create agent
brain make:skill Name       # Create skill
brain make:command Name     # Create command
brain make:include Name     # Create include
brain make:mcp Name         # Create MCP config
brain list                  # List commands
```

### Brain Core
```bash
brain-core get:file file.php --xml|--json|--yaml|--toml
brain-core list
```

### Testing
```bash
cd core && ./vendor/bin/phpunit
./vendor/bin/phpunit tests/MergerTest.php
```

## Development Workflow

### Adding Archetype
```bash
cd core/src/Archetypes && # Add NewArchetype.php
cd ../Blueprints && # Add NewBlueprint.php
cd ../../tests && # Add test
./vendor/bin/phpunit
```

### Adding Include
```php
// core/src/Includes/Universal/NewInclude.php
namespace Core\Includes\Universal;
use Core\Archetypes\BrainArchetype;
use Core\Attributes\Purpose;

#[Purpose('Description')]
class NewInclude extends BrainArchetype {
    public function handle(): void {
        $this->guideline('id')->text('...');
    }
}

// Use in Brain.php
$this->include(NewInclude::class);
```

## Key Concepts

- **Archetypes & Blueprints:** DTO classes with builder APIs
- **Includes:** Compile-time merging (not runtime)
- **Merger:** Flattens include chains to associative array
- **XmlBuilder:** Compact XML (no tabs, only newlines)
- **Testing:** MergerTest, XmlBuilderTest, JsonBuilderTest, LegacyParityTest

## Output Format

- No tabs/indentation (newlines only)
- Double newlines between top-level blocks
- Self-closing empty tags
- Escaped content
- Enum → scalar values
- Stable ordering

## Notable Rules

- XML: no tabs, only newlines, double newlines between top-level
- Cross-references: descriptive names (no .xml)
- Only BrainCore.php has #[Meta()]
- Legacy XML files: reference only, not used

This document is for Brain system development, NOT user applications.
