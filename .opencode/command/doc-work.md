---
description: "Interactive documentation command with maximum quality and user engagement"
---

<command>
<meta>
<id>doc:work</id>
<description>Interactive documentation command with maximum quality and user engagement</description>
</meta>
<execute>Document anything specified in $ARGUMENTS with maximum quality, interactivity, and professional technical writing standards</execute>
<provides>Documentation workflow command: discover → understand → propose → write → finalize. Interactive, evidence-based .docs/ management with Brain Docs CLI integration, vector memory, and YAML front matter enforcement.</provides>

# Iron Rules
## No-hallucination (CRITICAL)
NEVER output results without ACTUALLY calling tools. You CANNOT know task status or content without REAL tool calls. Fake results = CRITICAL VIOLATION.

## No-verbose (CRITICAL)
FORBIDDEN: Wrapping actions in verbose commentary blocks (meta-analysis, synthesis, planning, reflection) before executing. Act FIRST, explain AFTER.

## Max-interactivity (CRITICAL)
When $HAS_AUTO_APPROVE = false: MUST engage user with clarifying questions via AskUserQuestion tool. NEVER assume scope, depth, audience, or structure. Documentation is a COLLABORATIVE process — user defines WHAT, agent researches and writes HOW. When $HAS_AUTO_APPROVE = true: infer scope/depth/audience from $CLEAN_ARGS context. Skip clarifying questions. Proceed autonomously through all phases.
- **why**: Wrong assumptions about documentation scope = useless output + full rework. Interactive alignment is cheaper than rewrites. But -y flag means user trusts agent to make reasonable decisions autonomously.
- **on_violation**: If interactive: STOP and ask clarifying question. If auto-approve: infer from input and proceed.

## Discovery-before-creation (CRITICAL)
ALWAYS search existing docs via brain docs CLI BEFORE creating new files. Flow: brain docs "{keywords}" → found? → READ existing → UPDATE. Not found? → apply aggressive-docs-search (3+ keyword variations). Still not found → CREATE new. NEVER create duplicate documentation for same topic.
- **why**: Duplicate docs diverge over time. One source of truth per topic. Updating existing is faster and preserves history.
- **on_violation**: Run brain docs first. Found → update. Not found after 3+ searches → create new.

## Evidence-based (CRITICAL)
ALL documentation content MUST be based on: 1) actual codebase reading (Read tool, Explore agent), 2) vector memory search, 3) existing .docs/ content, 4) verified web research. NEVER write documentation from assumptions or "common knowledge". Every technical claim must be verifiable against source code.
- **why**: Documentation based on assumptions becomes lies when code changes. Evidence-based = always accurate.
- **on_violation**: Read source code FIRST. Verify every claim against actual implementation.

## 500-line-limit (CRITICAL)
Each documentation file MUST NOT exceed 500 lines. If content exceeds → split into {topic}-part-1.md, {topic}-part-2.md with cross-references and YAML front matter part: N field.
- **why**: Readability and token efficiency. Long files are unnavigable and expensive to load.
- **on_violation**: Split content. Add part field to YAML. Add cross-references between parts.

## Yaml-front-matter-mandatory (CRITICAL)
EVERY .docs/ file MUST start with YAML front matter. Required: name, description. Optional: part, type, date, version. brain docs CLI indexes ONLY files with valid YAML front matter — no front matter = invisible to search and discovery.
- **why**: brain docs CLI parses YAML for index and search. Docs without front matter are undiscoverable by other commands and agents.
- **on_violation**: Add YAML front matter before any markdown content. Verify with brain docs after writing.

## Text-first-code-last (CRITICAL)
Documentation is DESCRIPTION for humans. Minimize code to absolute minimum. Include code ONLY when: 1) text explanation would cost more tokens, AND 2) no other representation works. NEVER dump code blocks as documentation. Prefer: textual description > text-based diagram > minimal code snippet.
- **why**: Code in docs is expensive, hard to read, becomes stale faster than text. Text-first = maintainable, scannable, token-efficient.
- **on_violation**: Replace code with clear textual description. Keep only essential, minimal code examples.

## Validation-checkpoints (HIGH)
When $HAS_AUTO_APPROVE = false: obtain user approval at 3 checkpoints: 1) structure proposal (before writing), 2) first section `draft` (validate tone/depth/style), 3) final documentation (before saving). Between checkpoints: proceed autonomously. When $HAS_AUTO_APPROVE = true: skip ALL checkpoints, proceed through all phases to completion. Still SHOW summary of what was done at end.
- **why**: 3 checkpoints = balance between user control and flow. -y flag = user trusts agent to complete full cycle without interruption.
- **on_violation**: If interactive: pause at checkpoint, AskUserQuestion. If auto-approve: skip, continue to next phase.

## Auto-approve-mode (CRITICAL)
$HAS_AUTO_APPROVE = true → AUTONOMOUS MODE. Skip ALL checkpoints (structure approval, first section review, final approval). Infer scope/depth/audience from $CLEAN_ARGS. Choose reasonable defaults: depth=detailed, audience=developer, structure=standard for TARGET_TYPE. Proceed through all phases to completion. Write files directly. Show summary at end. $HAS_AUTO_APPROVE = false → INTERACTIVE MODE. Full checkpoint flow, clarifying questions, user drives decisions.
- **why**: User explicitly chose autonomous mode via -y flag. Documentation command must support pipeline usage (e.g., after task execution, auto-create docs). Every pause breaks automation flow.
- **on_violation**: If auto-approve: remove the question, use defaults, continue. If interactive: ask and wait.

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

## External-docs-via-context7 (HIGH)
When documenting features that use external packages/libraries: resolve library via mcp__context7__resolve-library-id('{libraryName: "{package}"}') then query docs via mcp__context7__query-docs('{libraryId: "{resolved_id}", query: "{specific_topic}"}'). Use Context7 for KNOWN packages (composer/npm dependencies). Use web-research-master for broader context or unknown sources. NEVER guess external API behavior — verify against official docs.
- **why**: Documentation referencing external packages must be accurate. Guessing package behavior = docs become lies on first version bump. Context7 provides indexed, version-aware library docs.
- **on_violation**: Identify external dependencies from codebase research. Resolve and query via Context7 before writing docs about them.


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
STORE-AS($DOC_TARGET = {documentation target extracted from $CLEAN_ARGS: feature name, module, concept, topic})
STORE-AS($TARGET_TYPE = {detect from $CLEAN_ARGS prefix or context: feature|module|concept|architecture|guide|api|reference|topic})

# Arguments format
Input formats: feature:X, module:X, concept:X, architecture:X, guide:X, api:X, topic:X, or plain text description.
- feature:focus-mode → TARGET_TYPE=feature, DOC_TARGET=focus-mode
- deployment guide → TARGET_TYPE=guide, DOC_TARGET=deployment
- how task delegation works → TARGET_TYPE=concept, DOC_TARGET=task delegation

# Docs folder structure
GOAL(Organize documentation with clear hierarchy within .docs/)
Root: .docs/ — all project documentation. Subdirectories by content type:
- .docs/features/ — feature descriptions and usage
- .docs/modules/ — internal module documentation
- .docs/concepts/ — conceptual explanations
- .docs/architecture/ — system design and flows
- .docs/guides/ — how-to guides and tutorials
- .docs/api/ — API specifications and contracts
- .docs/tor/ — terms of reference / requirements
- .docs/reference/ — reference material and lookups

# Yaml structure
GOAL(Consistent YAML front matter for brain docs indexing)
Required: name (string), description (string). Optional: part (int), type (string), date (YYYY-MM-DD), version (semver).
Type values: guide | api | concept | architecture | reference | tor
- ---
name: "Feature Name"
description: "Brief description"
type: "guide"
date: "2026-02-19"
version: "1.0.0"
---
- ---
name: "Feature Name (Part 1: Overview)"
description: "Architecture and key concepts"
part: 1
type: "guide"
date: "2026-02-19"
version: "1.0.0"
---

# Workflow
GOAL(Documentation workflow: capture → discover → research → propose → write → finalize)
- `1`: === PHASE 1: CAPTURE & DISCOVER ===
- `2`: STORE-AS($RAW_INPUT = $ARGUMENTS)
- `3`: STORE-AS($HAS_AUTO_APPROVE = {true if -y or --yes in RAW_INPUT})
- `4`: STORE-AS($CLEAN_ARGS = {RAW_INPUT with flags removed})
- `5`: STORE-AS($DOC_TARGET = {extract target from CLEAN_ARGS})
- `6`: STORE-AS($TARGET_TYPE = {detect type from CLEAN_ARGS})
- `7`: Bash('brain docs {DOC_TARGET keywords}') → STORE-AS($EXISTING_DOCS)
- `8`: IF(STORE-GET($EXISTING_DOCS) is empty) →
  Apply aggressive-docs-search: 3+ keyword variations (split CamelCase, strip suffixes, domain words)
  Bash('brain docs {variation_1}')
  Bash('brain docs {variation_2}')
  Bash('brain docs {variation_3}')
→ END-IF
- `9`: IF(STORE-GET($EXISTING_DOCS) found) →
  Read('{existing doc paths}') → STORE-AS($CURRENT_CONTENT)
  STORE-AS($DOC_MODE = update)
  Show: "Found existing docs: {paths}. Mode: UPDATE."
→ END-IF
- `10`: IF(STORE-GET($EXISTING_DOCS) NOT found after all searches) →
  STORE-AS($DOC_MODE = create)
  Show: "No existing docs for {DOC_TARGET}. Mode: CREATE."
→ END-IF
- `11`: mcp__vector-memory__search_memories('{query: "$DOC_TARGET", limit: 5}') → STORE-AS($MEMORY_CONTEXT)
- `12`: mcp__vector-memory__search_memories('{query: "$DOC_TARGET architecture design", limit: 3}') → append to STORE-GET($MEMORY_CONTEXT)
- `13`: IF(NOT $HAS_AUTO_APPROVE) →
  AskUserQuestion: What aspects to cover? Depth (overview/detailed/reference)? Target audience (developer/user/admin)?
  STORE-AS($USER_REQUIREMENTS = {user answers: aspects, depth, audience, special_requests})
→ END-IF
- `14`: IF($HAS_AUTO_APPROVE) →
  STORE-AS($USER_REQUIREMENTS = {inferred from $CLEAN_ARGS context: depth=detailed, audience=developer, aspects=all relevant})
  Auto-inferred scope from input. Proceeding autonomously.
→ END-IF
- `15`: === PHASE 2: EVIDENCE GATHERING ===
- `16`: [DELEGATE] @explore: 'CODEBASE RESEARCH for documenting {$DOC_TARGET}: 1) Find all related source files (classes, traits, interfaces, configs). 2) Identify public API: method signatures, parameters, return types. 3) Find existing tests (test behavior = specification). 4) Check inline comments, PHPDoc, README fragments. 5) Map dependencies and relationships. Return: {source_files: [], public_api: [], config_options: [], test_files: [], inline_docs: [], dependencies: []}.' → STORE-AS($CODEBASE_RESEARCH)
- `17`: IF($CODEBASE_RESEARCH reveals external packages/libraries) →
  For each significant dependency: resolve library ID
  mcp__context7__resolve-library-id('{libraryName: "{package_name}"}') → STORE-AS($LIBRARY_ID)
  mcp__context7__query-docs('{libraryId: "$LIBRARY_ID", query: "{relevant_topic}"}') → append to STORE-GET($CODEBASE_RESEARCH)
→ END-IF
- `18`: IF(external context needed beyond package docs (architecture patterns, industry practices)) →
  [DELEGATE] @web-research-master: 'Research {$DOC_TARGET} context: best practices, standard approaches, related documentation patterns' → STORE-AS($EXTERNAL_CONTEXT)
→ END-IF
- `19`: === PHASE 3: STRUCTURE PROPOSAL ===
- `20`: STORE-AS($DOC_PLAN = {proposed_path, sections_outline, estimated_lines, split_plan})
- `21`: IF(NOT $HAS_AUTO_APPROVE) →
  Present to user (CHECKPOINT 1):
    Path: .docs/{TARGET_TYPE}/{doc-name}.md
    Sections: {outline with estimated line counts per section}
    Split plan: {if estimated > 500 lines: part-1 = sections A-C, part-2 = sections D-F}
    Evidence: "{N} source files, {N} tests, {N} memory insights found"
    Mode: {DOC_MODE} (create new / update existing)
  AskUserQuestion → WAIT for explicit approval or changes
  IF(user requests changes) → Revise plan, re-propose
→ END-IF
- `22`: IF($HAS_AUTO_APPROVE) →
  Auto-approved structure. Proceeding to writing.
→ END-IF
- `23`: === PHASE 4: WRITING ===
- `24`: Write YAML front matter + first major section
- `25`: IF(NOT $HAS_AUTO_APPROVE) →
  Present first section to user (CHECKPOINT 2)
  AskUserQuestion → WAIT for feedback on tone, depth, style
  IF(approved) → Continue writing remaining sections
  IF(changes needed) → Apply feedback, show revised, then continue
→ END-IF
- `26`: IF($HAS_AUTO_APPROVE) → Continue writing all sections without pause
- `27`: Complete all sections. Enforce at each section:
- `28`:   - Evidence-based claims (cite source files)
- `29`:   - Text-first, minimal code
- `30`:   - Proper heading hierarchy
- `31`:   - Cross-references to related docs
- `32`:   - Running line count (split if approaching 500)
- `33`: === PHASE 5: FINALIZATION ===
- `34`: Final review checklist:
- `35`:   - YAML front matter present and valid
- `36`:   - Line count ≤ 500 per file
- `37`:   - All cross-references valid
- `38`:   - No secrets or PII
- `39`:   - Code examples minimal and accurate
- `40`: IF(STORE-GET($DOC_MODE) === "update") →
  Diff: show what changed vs original
→ END-IF
- `41`: IF(NOT $HAS_AUTO_APPROVE) →
  Present final to user (CHECKPOINT 3)
  AskUserQuestion → WAIT for final approval
→ END-IF
- `42`: IF($HAS_AUTO_APPROVE) → Self-review passed. Writing files.
- `43`: IF(approved OR $HAS_AUTO_APPROVE) →
  Write files to .docs/
  Bash('brain docs {DOC_TARGET keywords}') → verify files indexed by brain docs
  IF(NOT indexed) → Check YAML front matter format. Fix and retry.
  mcp__vector-memory__store_memory('{content: "Documentation {created|updated}: {DOC_TARGET}. Path: {file_paths}. Sections: {section_names}. Based on: {source_files}.", category: "project-context", tags: ["insight", "reusable"]}')
  RESULT: Documentation {DOC_MODE}d at {paths}. Indexed in brain docs.
→ END-IF

# Writing standards
GOAL(Professional technical writing for .docs/)
Language: clear, concise, jargon-free where possible. Match depth to declared audience.
Structure: logical heading hierarchy (# → ## → ###). Each section = one concept. No orphan sections.
Code examples: MINIMAL. Only when text would cost more tokens or be less clear. Always: language tag, context comment, what it demonstrates.
Diagrams: text-based (ASCII, Mermaid) when visual adds value. Never images (not indexable, not diffable).
Cross-references: relative paths ([See X](../concepts/x.md)). Verify targets exist.
Consistency: follow existing .docs/ style if any docs already exist. Match tone, format, heading style.

# File naming
Lowercase, hyphens, descriptive, no spaces.
- Single file: feature-name.md
- Multi-part: feature-name-part-1.md, feature-name-part-2.md
- Topic split: feature-name-overview.md, feature-name-api.md

# Error handling
- `1`: IF(brain docs CLI unavailable) →
  Fallback: Glob(".docs/**/*.md") + Read YAML headers manually
→ END-IF
- `2`: IF(no source code found for topic) →
  AskUserQuestion: is this conceptual-only or should match code? Conceptual → proceed with user input. Code-based → verify topic name and search again.
→ END-IF
- `3`: IF(user rejects structure at checkpoint) →
  Revise based on specific feedback. Re-propose. Max 2 revisions, then AskUserQuestion for precise direction.
→ END-IF
- `4`: IF(content exceeds 500 lines mid-writing) →
  STOP writing. Propose split plan. Get approval. Split and continue.
→ END-IF
- `5`: IF(existing doc found but format is wrong) →
  Propose migration: fix YAML front matter, restructure to standards. AskUserQuestion before changing.
→ END-IF

</command>