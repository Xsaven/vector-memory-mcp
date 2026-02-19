---
description: "Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents"
---

<command>
<meta>
<id>init-vector</id>
<description>Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents</description>
</meta>
<execute>Systematically scan and document entire project into vector memory. Sequential ExploreMaster agents explore logical areas, communicating through vector memory for continuity. Each agent searches memory before exploring, stores findings after. Enables comprehensive project knowledge base for all agents.</execute>
<provides>Vector memory initialization: parallel project scanning, dense knowledge storage, brain docs integration. Populates vector memory with project structure, code, docs, and config insights for agent context.</provides>

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
Task tags MUST use ONLY predefined values. FORBIDDEN: inventing new tags, synonyms, variations. Allowed: decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression, feature, bugfix, refactor, research, docs, test, chore, spike, hotfix, backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration.
- **why**: Ad-hoc tags cause explosion ("user-auth", "authentication", "auth" = same thing, search finds none). Predefined list = consistent search.
- **on_violation**: Replace with closest predefined match. No match = skip tag, put context in content.

## Memory-tags-predefined-only (CRITICAL)
Memory tags MUST use ONLY predefined values. Allowed: pattern, solution, `failure`, decision, insight, workaround, deprecated, project-wide, module-specific, temporary, reusable.
- **why**: Unknown tags = unsearchable memories. Predefined = discoverable.
- **on_violation**: Replace with closest predefined match.

## Memory-categories-predefined-only (CRITICAL)
Memory category MUST be one of: code-solution, bug-fix, architecture, learning, debugging, performance, security, project-context. FORBIDDEN: "other", "general", "misc", or unlisted.
- **why**: "other" is garbage nobody searches. Every memory needs meaningful category.
- **on_violation**: Choose most relevant from predefined list.

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
- **why**: Ambiguity paralysis wastes more time than conservative interpretation. One precise question is enough — if user wanted detailed spec, they would have written docs.
- **on_violation**: Identify specific gap. One question or auto-decide. Proceed.

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

## Auto-approve-default (CRITICAL)
Default behavior is FULLY AUTOMATED (no user prompts). $HAS_AUTO_APPROVE = true confirms. Without -y: show completion summary. With -y: silent completion.
- **why**: Batch initialization workflow requires zero interaction by default.
- **on_violation**: Proceed autonomously. Never block on user input.

## Exclude-brain-directory (CRITICAL)
NEVER scan .brain/ - Brain system internals, not project code
- **why**: Brain config files pollute vector memory with irrelevant system data
- **on_violation**: Skip .brain/ in structure discovery and all exploration phases


# Task tag selection
GOAL(Select 1-4 tags per task. Combine dimensions for precision.)
WORKFLOW (pipeline stage): decomposed, validation-fix, blocked, stuck, needs-research, light-validation, parallel-safe, atomic, manual-only, regression
TYPE (work kind): feature, bugfix, refactor, research, docs, test, chore, spike, hotfix
DOMAIN (area): backend, frontend, database, api, auth, ui, config, infra, ci-cd, migration
Formula: 1 TYPE + 1 DOMAIN + 0-2 WORKFLOW. Example: ["feature", "api"] or ["bugfix", "auth", "validation-fix"]. Max 4 tags.

# Memory tag selection
GOAL(Select 1-3 tags per memory. Combine dimensions.)
CONTENT (kind): pattern, solution, `failure`, decision, insight, workaround, deprecated
SCOPE (breadth): project-wide, module-specific, temporary, reusable
Formula: 1 CONTENT + 0-1 SCOPE. Example: ["solution", "reusable"] or ["failure", "module-specific"]. Max 3 tags.

# Aggressive docs search
GOAL(Find documentation even if named differently than task/code)
- `1`: Generate keyword variations from task title/content:
- `2`:   1. Original: "FocusModeTest" → search "FocusModeTest"
- `3`:   2. Split CamelCase: "FocusModeTest" → search "FocusMode", "Focus Mode"
- `4`:   3. Remove suffix: "FocusModeTest" → search "Focus" (remove Mode, Test)
- `5`:   4. Domain words: extract meaningful nouns → search each
- `6`:   5. Parent context: if task has parent → include parent title keywords
- `7`: Common suffixes to STRIP: Test, Tests, Controller, Service, Repository, Command, Handler, Provider, Factory, Manager, Helper, Validator, Processor
- `8`: Search ORDER: most specific → most general. STOP when found.
- `9`: Minimum 3 search attempts before concluding "no documentation".
- `10`: WRONG: brain docs "UserAuthenticationServiceTest" → not found → done
- `11`: RIGHT: brain docs "UserAuthenticationServiceTest" → not found → brain docs "UserAuthentication" → not found → brain docs "Authentication" → FOUND!

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($HAS_AUTO_APPROVE = {true if $RAW_INPUT contains "-y" or "--yes"})
STORE-AS($CLEAN_ARGS = {$RAW_INPUT with -y/--yes flags removed})
STORE-AS($INIT_SCOPE = {optional scope filter from $CLEAN_ARGS: specific area/module to scan, or empty for full project})

# Phase1 status
GOAL(Check memory state, determine fresh vs augment mode)
- `1`: mcp__vector-memory__get_memory_stats('{}')
- `2`: STORE-AS($MEM = {total, categories})
- `3`: IF($MEM.total === 0) → Fresh init | ELSE → Augment existing

# Phase2 structure
GOAL(Quick structure scan to identify areas for parallel exploration)
- `1`: Task(@explore, TASK →
  QUICK SCAN ONLY - identify directories, not deep analysis
  Glob("*") → list root directories
  EXCLUDE: .brain/, vendor/, node_modules/, .git/
  Classify: code(src/app), tests, config, docs(.docs), build, deps
  IF($INIT_SCOPE not empty) →
  FOCUS on areas matching $INIT_SCOPE only
→ END-IF
  Output JSON: {areas: [{path, type, priority}]}
→ END-TASK, 'OUTPUT({areas: [{path, type, priority: high|medium|low}]})', 'STORE-AS($AREAS)')

# Phase3 parallel code
GOAL(PARALLEL: Launch code exploration agents simultaneously)
- `1`: 
BATCH 1 - Code Areas (LAUNCH IN PARALLEL):
Task(@explore, TASK →
  Area: src/ or app/
  Thoroughness: very thorough
  BEFORE: search_memories("src code architecture", 3)
  DO: Glob(**/*.php), Grep(class|function|namespace)
  EXTRACT: {path|files|classes|namespaces|patterns|deps}
  AFTER: store_memory(compact_json, "architecture", ["insight", "project-wide"])
→ END-TASK, 'OUTPUT({path:"src",files:N,classes:N,key_patterns:[]})') Task(@explore, TASK →
  Area: tests/
  Thoroughness: medium
  BEFORE: search_memories("tests structure", 3)
  DO: Glob(**/*Test.php), identify test framework
  EXTRACT: {path|test_files|coverage_areas|framework}
  AFTER: store_memory(compact_json, "architecture", ["insight", "module-specific"])
→ END-TASK, 'OUTPUT({path:"tests",files:N,framework:str})')

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
Task(@documentation-master, TASK →
  Docs batch: [{path1}, {path2}, {path3}]
  Read each doc via Read tool
  EXTRACT per doc: {name|type|key_concepts|related_to}
  AFTER: store_memory(compact_json, "learning", ["insight", "reusable"])
→ END-TASK, 'OUTPUT({docs_analyzed:3,topics:[]})') Task(@documentation-master, TASK →
  Docs batch: [{path4}, {path5}, {path6}]
  Read each doc via Read tool
  EXTRACT per doc: {name|type|key_concepts|related_to}
  AFTER: store_memory(compact_json, "learning", ["insight", "reusable"])
→ END-TASK, 'OUTPUT({docs_analyzed:3,topics:[]})')

- `4`: NOTE: Each doc → separate memory entry for precise vector search

# Phase3 parallel config
GOAL(PARALLEL: Config and build areas simultaneously)
- `1`: 
BATCH 2 - Config/Build (LAUNCH IN PARALLEL):
Task(@explore, TASK →
  Area: config/
  Thoroughness: quick
  DO: Glob(config/*.php), extract key names
  EXTRACT: {configs:[names],env_vars:[],services:[]}
  AFTER: store_memory(compact_json, "architecture", ["insight"])
→ END-TASK, 'OUTPUT({path:"config",configs:[]})') Task(@explore, TASK →
  Area: build/CI files
  Thoroughness: quick
  DO: Find .github/, docker*, Makefile, composer.json, package.json
  EXTRACT: {ci:bool,docker:bool,deps:{php:[],js:[]}}
  AFTER: store_memory(compact_json, "architecture", ["insight"])
→ END-TASK, 'OUTPUT({ci:bool,docker:bool,deps:{}})')


# Phase4 synthesis
GOAL(Synthesize all findings into project-wide architecture)
- `1`: mcp__vector-memory__search_memories('{query: "project structure architecture stack patterns", limit: 20, category: "architecture"}')
- `2`: STORE-AS($ALL_FINDINGS)
- `3`: mcp__vector-memory__store_memory({
                    content: "INIT-VECTOR SYNTHESIS|PROJECT:{type}|AREAS:{list}|STACK:{tech}|PATTERNS:{arch}|DEPS:{graph}",
                    category: "architecture",
                    tags: ["insight", "project-wide"]
                })

# Phase5 complete
GOAL(Report completion with metrics)
- `1`: mcp__vector-memory__get_memory_stats('{}')
- `2`: OUTPUT(=== INIT-VECTOR COMPLETE === Areas: {count} | Memories: {total} | Time: {elapsed} Parallel batches: 2 | Agents launched: {agent_count} ============================)

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

# Error recovery
Command-specific error handling (trait provides baseline tool error / MCP `failure` policy)
- MCP unavailable → abort, report
- Agent timeout → skip area, continue, report in summary
- Empty area → store minimal, proceed

# Quality gates
Validation checkpoints
- Gate 1: memory status checked (fresh vs augment)
- Gate 2: structure discovery `completed` (AREAS populated)
- Gate 3: parallel code exploration returned
- Gate 4: documentation indexed and analyzed
- Gate 5: config/build exploration returned
- Gate 6: synthesis stored to vector memory
- Gate 7: get_memory_stats confirms new entries

# Example fresh
SCENARIO(Fresh initialization with 8 docs)
- `1`: Memory: 0 entries → fresh init
- `2`: Structure scan: 5 areas (src, tests, config, .docs, build)
- `3a`: PARALLEL: ExploreMaster(src/) + ExploreMaster(tests/) → 2 agents
- `3b`: brain docs → 8 docs found → batch into 3+3+2
- `3b-parallel`: PARALLEL: 3x DocumentationMaster agents
- `3c`: PARALLEL: ExploreMaster(config/) + ExploreMaster(build/) → 2 agents
- `4`: Synthesis: search architecture memories → project-wide summary
- `5`: Complete: 15 memories, 7 agents (4 Explore + 3 DocMaster)

# Directive
PARALLEL agents! brain docs → DocumentationMaster! Dense storage! Predefined tags! Fast init!

</command>