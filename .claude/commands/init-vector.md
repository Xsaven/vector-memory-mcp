---
name: "init-vector"
description: "Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents"
---

<command>
<meta>
<id>init-vector</id>
<description>Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents</description>
</meta>
<execute>Systematically scan and document entire project into vector memory. Sequential ExploreMaster agents explore logical areas, communicating through vector memory for continuity. Each agent searches memory before exploring, stores findings after. Enables comprehensive project knowledge base for all agents.</execute>
<provides>The /init-vector command automates project knowledge base setup via parallel agent execution.</provides>

# Iron Rules
## Parallel-execution (CRITICAL)
Launch INDEPENDENT areas in PARALLEL (multiple Task calls in single response)
- **why**: Maximizes throughput, reduces total initialization time
- **on_violation**: Group independent areas, launch simultaneously

## Brain-docs-then-document-master (CRITICAL)
Use brain docs for INDEX, then DocumentationMaster agents to ANALYZE content
- **why**: brain docs = metadata index, DocumentationMaster = content analysis + vector storage
- **on_violation**: brain docs → group docs → parallel DocumentationMaster agents

## Dense-storage (CRITICAL)
Store compact JSON-like format: {key:value} pairs, no verbose prose
- **why**: Maximizes information density, improves vector search relevance
- **on_violation**: Reformat to: path|type|files|classes|patterns|deps

## Memory-before-after (HIGH)
search_memories BEFORE exploring, store_memory AFTER
- **why**: Context continuity between agents
- **on_violation**: Add mandatory memory operations

## No-questions (HIGH)
Fully automated - no user prompts
- **why**: Batch initialization workflow
- **on_violation**: Proceed autonomously

## Exclude-brain-directory (CRITICAL)
NEVER scan .brain/ - Brain system internals, not project code
- **why**: Brain config files pollute vector memory with irrelevant system data
- **on_violation**: Skip .brain/ in structure discovery and all exploration phases


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($INIT_PARAMS = {initialization parameters extracted from $RAW_INPUT})

# Phase1 status
GOAL(Check memory state, determine fresh vs augment mode)
- `1`: mcp__vector-memory__get_memory_stats('{}')
- `2`: STORE-AS($MEM = {total, categories})
- `3`: IF($MEM.total === 0) → Fresh init | ELSE → Augment existing

# Phase2 structure
GOAL(Quick structure scan to identify areas for parallel exploration)
- `1`: Task(@agent-explore, 'TASK →'."\\n"
    .'  QUICK SCAN ONLY - identify directories, not deep analysis'."\\n"
    .'  Glob("*") → list root directories'."\\n"
    .'  EXCLUDE: .brain/, vendor/, node_modules/, .git/'."\\n"
    .'  Classify: code(src/app), tests, config, docs(.docs), build, deps'."\\n"
    .'  Output JSON: {areas: [{path, type, priority}]}'."\\n"
    .'→ END-TASK', 'OUTPUT({areas: [{path, type, priority: high|medium|low}]})', 'STORE-AS($AREAS)')

# Phase3 parallel code
GOAL(PARALLEL: Launch code exploration agents simultaneously)
- `1`: 
BATCH 1 - Code Areas (LAUNCH IN PARALLEL):
Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: src/ or app/'."\\n"
    .'  Thoroughness: very thorough'."\\n"
    .'  BEFORE: search_memories("src code architecture", 3)'."\\n"
    .'  DO: Glob(**/*.php), Grep(class|function|namespace)'."\\n"
    .'  EXTRACT: {path|files|classes|namespaces|patterns|deps}'."\\n"
    .'  AFTER: store_memory(compact_json, "architecture", ["init-vector","src"])'."\\n"
    .'→ END-TASK', 'OUTPUT({path:"src",files:N,classes:N,key_patterns:[]})') Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: tests/'."\\n"
    .'  Thoroughness: medium'."\\n"
    .'  BEFORE: search_memories("tests structure", 3)'."\\n"
    .'  DO: Glob(**/*Test.php), identify test framework'."\\n"
    .'  EXTRACT: {path|test_files|coverage_areas|framework}'."\\n"
    .'  AFTER: store_memory(compact_json, "architecture", ["init-vector","tests"])'."\\n"
    .'→ END-TASK', 'OUTPUT({path:"tests",files:N,framework:str})')

- `2`: NOTE: Both agents run SIMULTANEOUSLY via parallel Task calls

# Phase3 documentation
GOAL(Index .docs/ via brain docs, then analyze content via DocumentationMaster agents)
- `1`: 
STEP 1 - Get documentation index:
Bash('brain docs') STORE-AS($DOCS_INDEX = [{path, name, description, type}])

- `2`: 
STEP 2 - Adaptive batching based on doc count:
IF(docs_count <= 3) → Single DocumentationMaster for all IF(docs_count 4-8) → 2 DocumentationMaster agents in parallel IF(docs_count 9-15) → 3 DocumentationMaster agents in parallel IF(docs_count > 15) → Batch by type (guide, api, concept, etc.)

- `3`: 
STEP 3 - PARALLEL DocumentationMaster agents (example for 6 docs):
Task(@agent-documentation-master, 'TASK →'."\\n"
    .'  Docs batch: [{path1}, {path2}, {path3}]'."\\n"
    .'  Read each doc via Read tool'."\\n"
    .'  EXTRACT per doc: {name|type|key_concepts|related_to}'."\\n"
    .'  AFTER: store_memory(compact_json, "learning", ["init-vector","docs","{type}"])'."\\n"
    .'→ END-TASK', 'OUTPUT({docs_analyzed:3,topics:[]})') Task(@agent-documentation-master, 'TASK →'."\\n"
    .'  Docs batch: [{path4}, {path5}, {path6}]'."\\n"
    .'  Read each doc via Read tool'."\\n"
    .'  EXTRACT per doc: {name|type|key_concepts|related_to}'."\\n"
    .'  AFTER: store_memory(compact_json, "learning", ["init-vector","docs","{type}"])'."\\n"
    .'→ END-TASK', 'OUTPUT({docs_analyzed:3,topics:[]})')

- `4`: NOTE: Each doc → separate memory entry for precise vector search

# Phase3 parallel config
GOAL(PARALLEL: Config and build areas simultaneously)
- `1`: 
BATCH 2 - Config/Build (LAUNCH IN PARALLEL):
Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: config/'."\\n"
    .'  Thoroughness: quick'."\\n"
    .'  DO: Glob(config/*.php), extract key names'."\\n"
    .'  EXTRACT: {configs:[names],env_vars:[],services:[]}'."\\n"
    .'  AFTER: store_memory(compact_json, "architecture", ["init-vector","config"])'."\\n"
    .'→ END-TASK', 'OUTPUT({path:"config",configs:[]})') Task(@agent-explore, 'TASK →'."\\n"
    .'  Area: build/CI files'."\\n"
    .'  Thoroughness: quick'."\\n"
    .'  DO: Find .github/, docker*, Makefile, composer.json, package.json'."\\n"
    .'  EXTRACT: {ci:bool,docker:bool,deps:{php:[],js:[]}}'."\\n"
    .'  AFTER: store_memory(compact_json, "architecture", ["init-vector","build"])'."\\n"
    .'→ END-TASK', 'OUTPUT({ci:bool,docker:bool,deps:{}})')


# Phase4 synthesis
GOAL(Synthesize all findings into project-wide architecture)
- `1`: mcp__vector-memory__search_memories('{query: "init-vector", limit: 20, tags: ["init-vector"]}')
- `2`: STORE-AS($ALL_FINDINGS)
- `3`: mcp__vector-memory__store_memory('{'."\\n"
    .'                    content: "PROJECT:{type}|AREAS:{list}|STACK:{tech}|PATTERNS:{arch}|DEPS:{graph}",'."\\n"
    .'                    category: "architecture",'."\\n"
    .'                    tags: ["init-vector", "project-wide", "synthesis"]'."\\n"
    .'                }')

# Phase5 complete
GOAL(Report completion with metrics)
- `1`: mcp__vector-memory__get_memory_stats('{}')
- `2`: OUTPUT(═══ INIT-VECTOR COMPLETE ═══ Areas: {count} | Memories: {total} | Time: {elapsed} Parallel batches: 2 | Agents launched: {agent_count} ═══════════════════════════)

# Storage format
Compact storage format for maximum vector search relevance
- BAD: "The src/ directory contains 150 PHP files organized in MVC pattern..."
- GOOD: "src|150php|MVC|App\\Models,App\\Http|Laravel11|eloquent,routing"
- Format: path|files|pattern|namespaces|framework|features

# Parallel pattern
How to execute agents in parallel
- `1`: WRONG: forEach(areas) → sequential, slow
- `2`: RIGHT: List multiple Task() calls in single response
- `3`: Brain executes all Task() calls simultaneously
- `4`: Each agent works independently, stores to memory
- `5`: Wait for all to complete, then synthesize

# Brain docs usage
brain docs for INDEX, DocumentationMaster for CONTENT analysis
- Bash('brain docs')
- Bash('brain docs keyword1,keyword2')
- Index returns: path, name, description, type, date, version
- Then: DocumentationMaster reads & analyzes actual content
- Each doc → Read → Extract key concepts → store_memory

# Errors
Error handling
- MCP unavailable → abort, report
- Agent timeout → skip area, continue, report in summary
- Empty area → store minimal, proceed

# Example fresh
SCENARIO(Fresh initialization with 8 docs)
- `1`: Memory: 0 entries → fresh init
- `2`: Structure scan: 5 areas (src, tests, config, .docs, build)
- `3a`: PARALLEL: ExploreMaster(src/) + ExploreMaster(tests/) → 2 agents
- `3b`: brain docs → 8 docs found → batch into 3+3+2
- `3b-parallel`: PARALLEL: 3x DocumentationMaster agents
- `3c`: PARALLEL: ExploreMaster(config/) + ExploreMaster(build/) → 2 agents
- `4`: Synthesis: search init-vector memories → project-wide summary
- `5`: Complete: 15 memories, 7 agents (4 Explore + 3 DocMaster)

# Directive
PARALLEL agents! brain docs → DocumentationMaster! Dense storage! Fast init!

</command>