---
name: "task:create"
description: "Create task from description with analysis and estimation"
---

<command>
<meta>
<id>task:create</id>
<description>Create task from description with analysis and estimation</description>
</meta>
<execute>Creates task(s) from user description provided via $ARGUMENTS. Analyzes relevant materials, searches vector memory for similar past work, estimates time, gets mandatory user approval, creates task(s), and recommends decomposition if estimate >5-8 hours. Golden rule: each task 5-8 hours max.</execute>
<provides>Task creation specialist that analyzes user descriptions, researches context, estimates effort, and creates well-structured tasks after user approval.</provides>

# Iron Rules
## Analyze-arguments (CRITICAL)
MUST analyze an input task thoroughly before creating any task
- **why**: User description requires deep understanding to create accurate task specification
- **on_violation**: Parse and analyze an input task first, extract scope, requirements, and context

## Search-memory-first (CRITICAL)
MUST search vector memory for similar past work before analysis
- **why**: Prevents duplicate work and leverages existing insights
- **on_violation**: Execute mcp__vector-memory__search_memories('{query: "{task_domain}", limit: 5}')

## Estimate-required (CRITICAL)
MUST provide time estimate for the task
- **why**: Estimates enable planning and identify tasks needing decomposition
- **on_violation**: Add estimate in hours before presenting task

## Mandatory-user-approval (CRITICAL)
EVERY operation MUST have explicit user approval BEFORE execution. Present plan → WAIT for approval → Execute. NO auto-execution. EXCEPTION: If $HAS_Y_FLAG is true, auto-approve.
- **why**: User maintains control. No surprises. Flag -y enables automated execution.
- **on_violation**: STOP. Wait for explicit user approval (unless $HAS_Y_FLAG is true).

## Max-task-estimate (HIGH)
If estimate >5-8 hours, MUST strongly recommend /task:decompose
- **why**: Large tasks should be decomposed for better manageability and tracking
- **on_violation**: Warn user and recommend decomposition after task creation

## Create-only-no-execution (CRITICAL)
This command ONLY creates tasks. NEVER execute the task after creation, regardless of size or complexity.
- **why**: Task creation and task execution are separate concerns. User decides when to execute via /task:next or /do commands.
- **on_violation**: STOP immediately. Return created task ID and let user decide next action.

## Comment-with-context (CRITICAL)
MUST add initial comment with useful links: memory IDs from research, relevant file paths from codebase exploration, related task IDs.
- **why**: Comments preserve critical context for future execution. Without links, executor loses valuable research done during creation.
- **on_violation**: Add comment with: Memory refs (IDs from PRIOR_WORK), File refs (paths from CODEBASE_CONTEXT), Related tasks (from EXISTING_TASKS).

## Simple-task-heuristics (HIGH)
FLAG simple tasks early: short descriptions (<140 chars) without architecture/API hints or broad scope keywords ("architecture", "integration", "multi-module").
- **why**: Lightweight tasks can skip heavy agent orchestration, reducing latency.
- **on_violation**: When a task seems simple but exceeds scope hints, keep research thorough.

## Deep-research-mandatory (CRITICAL)
MUST perform comprehensive research BEFORE formulating task unless $SIMPLE_TASK is true. Simple tasks may limit research to duplicates/memory. OTHER tasks require existing tasks, vector memory, codebase (if code-related), documentation.
- **why**: Quality task creation requires full context for complex work while letting simple requests stay fast. Skipping research leads to duplicates, missed dependencies, and poor estimates.
- **on_violation**: STOP. Execute required research steps (existing tasks, memory, codebase exploration) before proceeding to analysis.

## Check-existing-tasks (CRITICAL)
MUST search existing tasks for duplicates or related work before creating new task.
- **why**: Prevents duplicate tasks, identifies potential parent tasks, reveals blocked/blocking relationships.
- **on_violation**: Execute mcp__vector-task__task_list('{query: "{objective}", limit: 10}') and analyze results.

## Mandatory-agent-delegation (CRITICAL)
ALL non-trivial research steps (existing tasks, vector memory, codebase, documentation) MUST be delegated to specialized agents. SIMPLE_TASK may limit agent work to duplicates/memory, but anything beyond that requires delegation.
- **why**: Direct execution consumes command context. Agents have dedicated context for deep research and return concise structured reports.
- **on_violation**: STOP. Delegate to: vector-master (tasks/memory), explore (codebase), documentation-master (docs) when complexity requires it. Never use direct MCP/Glob/Grep for deep research.


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($TASK_DESCRIPTION = {task description extracted from $RAW_INPUT})

# Role
Task creation specialist that analyzes user descriptions, researches context, estimates effort, and creates well-structured tasks after user approval.

# Workflow step0
STEP 0 - Parse an input task and Understand
- `1`: STORE-AS($HAS_Y_FLAG = {true if $RAW_INPUT contains "-y" or "--yes"})
- `parse`: TASK → STORE-GET($TASK_DESCRIPTION)
- `action-1`: Extract: primary objective, scope, requirements from user description
- `action-2`: Identify: implicit constraints, technical domain, affected areas
- `action-3`: Determine: task type (feature, bugfix, refactor, research, docs)
- `output`: STORE-AS($TASK_SCOPE = parsed objective, domain, requirements, type) → STORE-AS($TASK_TEXT = full original user description from $TASK_DESCRIPTION)

# Workflow step1
STEP 1 - Search Existing Tasks for Duplicates/Related Work (MANDATORY)
- `delegate`: [DELEGATE] @agent-vector-master: 'Search existing tasks for potential duplicates or related work. Task objective: {STORE-GET($TASK_SCOPE)}. Search by: 1) objective keywords, 2) domain terms, 3) `pending` tasks. Analyze: duplicates, potential parent tasks, dependencies (blocked-by, blocks). Return: structured report with task IDs, relationships, recommendation (create new / update existing / make subtask).'
- `decision`: IF(duplicate task found in agent report) →
  STOP. Inform user about existing task ID and ask: update existing or create new?
→ ELSE →
  Continue to next step
→ END-IF
- `output`: STORE-AS($EXISTING_TASKS = agent report: related task IDs, potential parent, dependencies)

# Workflow step2
STEP 2 - Deep Search Vector Memory for Prior Knowledge (MANDATORY)
- `delegate`: [DELEGATE] @agent-vector-master: 'Deep multi-probe search for prior knowledge related to task. Task context: {STORE-GET($TASK_SCOPE)}. Search categories: code-solution, architecture, bug-fix, learning. Use decomposed queries: 1) domain + objective, 2) implementation patterns, 3) known bugs/errors, 4) lessons learned. Return: structured report with memory IDs, key insights, reusable patterns, approaches to avoid, past mistakes.'
- `output`: STORE-AS($PRIOR_WORK = agent report: memory IDs, insights, recommendations, warnings)

# Workflow step3
STEP 3 - Codebase Exploration (MANDATORY for code-related tasks)
- `decision`: IF(task is code-related (feature, bugfix, refactor)) →
  TASK →
  [DELEGATE] @agent-explore: 'Comprehensive scan for {domain}. Find: existing implementations, related components, patterns used, dependencies, test coverage. Return: relevant files with paths, architecture notes, integration points'
  Wait for Explore agent to complete
→ END-TASK
→ ELSE →
  SKIP(Task is not code-related (research, docs))
→ END-IF
- `output`: STORE-AS($CODEBASE_CONTEXT = relevant files, patterns, dependencies, integration points)

# Workflow step4
STEP 4 - Documentation Research (if relevant)
- `decision`: IF(task involves architecture, API, or external integrations) →
  [DELEGATE] @agent-documentation-master: 'Research documentation for task context. Domain: {STORE-GET($TASK_SCOPE)}. Search: 1) project .docs/ via brain docs command, 2) relevant package docs if external deps. Return: structured report with doc paths, API specs, architectural decisions, relevant sections.'
→ ELSE →
  SKIP(Documentation scan not needed for this task type)
→ END-IF
- `output`: STORE-AS($DOC_CONTEXT = agent report: documentation references, API specs, architectural decisions)

# Workflow step5
STEP 5 - Task Analysis via Sequential Thinking
- `thinking`: mcp__sequential-thinking__sequentialthinking('{'."\\n"
    .'                    thought: "Analyzing task scope, complexity, and requirements for: STORE-GET($TASK_SCOPE)",'."\\n"
    .'                    thoughtNumber: 1,'."\\n"
    .'                    totalThoughts: 4,'."\\n"
    .'                    nextThoughtNeeded: true'."\\n"
    .'                }')
- `analyze-1`: Assess complexity: simple (1-2h), moderate (2-4h), complex (4-6h), major (6-8h), decompose (>8h)
- `analyze-2`: Identify: dependencies, blockers, prerequisites from STORE-GET($EXISTING_TASKS) and STORE-GET($CODEBASE_CONTEXT)
- `analyze-3`: Determine: priority based on urgency and impact
- `analyze-4`: Extract: acceptance criteria from requirements
- `output`: STORE-AS($ANALYSIS = complexity, estimate, priority, dependencies, criteria)

# Workflow step6
STEP 6 - Formulate Task Specification with Context Links
- `title`: Create concise title (max 10 words) capturing objective
- `content`: Write detailed description with: objective, context, acceptance criteria, implementation hints
- `priority`: Assign: critical | high | medium | low
- `tags`: Add relevant tags: [category, domain, stack]
- `estimate`: Set time estimate in hours
- `comment`: Build initial comment with research context: → - Memory refs: list memory IDs from STORE-GET($PRIOR_WORK) (format: "Related memories: #ID1, #ID2") → - File refs: list key file paths from STORE-GET($CODEBASE_CONTEXT) (format: "Key files: path1, path2") → - Task refs: list related task IDs from STORE-GET($EXISTING_TASKS) (format: "Related tasks: #ID1, #ID2") → - Doc refs: list doc paths from STORE-GET($DOC_CONTEXT) if available
- `output`: STORE-AS($TASK_SPEC = {title, content, priority, tags, estimate, comment})

# Workflow step7
STEP 7 - Present Task for User Approval (MANDATORY GATE)
- `present-1`: Display task specification:
- `present-2`:   Title: {title}
- `present-3`:   Priority: {priority}
- `present-4`:   Estimate: {estimate} hours
- `present-5`:   Tags: {tags}
- `present-6`:   Content: {content preview}
- `present-7`:   Related Tasks: STORE-GET($EXISTING_TASKS)
- `present-8`:   Prior Work: Memory IDs from STORE-GET($PRIOR_WORK)
- `present-9`:   Codebase Context: STORE-GET($CODEBASE_CONTEXT)
- `warning`: IF(estimate > 8 hours) →
  WARN: Estimate exceeds 8h. Strongly recommend running /task:decompose {task_id} after creation.
→ END-IF
- `prompt`: Ask: "Create this task? (yes/no/modify)"
- `gate`: VALIDATE(User response is YES, APPROVE, CONFIRM, or Y) → FAILS → Wait for explicit approval. Allow modifications if requested.

# Workflow step8
STEP 8 - Create Task After Approval (with context links in comment)
- `create`: mcp__vector-task__task_create('{'."\\n"
    .'                    title: "STORE-GET($TASK_SPEC).title",'."\\n"
    .'                    content: "STORE-GET($TASK_SPEC).content",'."\\n"
    .'                    priority: "STORE-GET($TASK_SPEC).priority",'."\\n"
    .'                    tags: STORE-GET($TASK_SPEC).tags,'."\\n"
    .'                    comment: "STORE-GET($TASK_SPEC).comment"'."\\n"
    .'                }')
- `capture`: STORE-AS($CREATED_TASK_ID = task ID from response)

# Workflow step9
STEP 9 - Post-Creation Summary (END - NO EXECUTION)
- `confirm`: Report: Task created with ID: STORE-GET($CREATED_TASK_ID)
- `decompose-check`: IF(estimate > 8 hours) →
  STRONGLY RECOMMEND: Run /task:decompose STORE-GET($CREATED_TASK_ID) to break down this large task
→ END-IF
- `next-steps`: Suggest: /task:next to start working, /task:list to view all tasks
- `stop`: STOP HERE. Do NOT execute the task. Return control to user.

# Workflow step10
STEP 10 - Store Task Creation Insight
- `store`: mcp__vector-memory__store_memory('{'."\\n"
    .'                    content: "Created task: {title}. Domain: {domain}. Approach: {key insights from analysis}. Estimate: {hours}h.",'."\\n"
    .'                    category: "tool-usage",'."\\n"
    .'                    tags: ["task-creation", "{domain}"]'."\\n"
    .'                }')

# Task format
Required task specification structure
- Concise, action-oriented (max 10 words)
- Detailed with: objective, context, acceptance criteria, hints
- critical | high | medium | low
- [category, domain, stack-tags]
- 1-8 hours (>8h needs decomposition)

# Estimation rules
Task estimation guidelines
- 1-2h: Config changes, simple edits, minor fixes
- 2-4h: Small features, multi-file changes, tests
- 4-6h: Moderate features, refactoring, integrations
- 6-8h: Complex features, architectural changes
- >8h: MUST recommend /task:decompose

# Priority rules
Priority assignment criteria
- Blockers, security issues, data integrity, production bugs
- Key features, deadlines, dependencies for other work
- Standard features, improvements, optimizations
- Nice-to-have, cosmetic, documentation, cleanup

# Quality gates
ALL checkpoints MUST pass before task creation
- Step 0: STORE-GET($TASK_TEXT) fully parsed - objective, domain, type extracted
- Step 1: Existing tasks searched - duplicates checked, dependencies identified
- Step 2: Vector memory searched - code-solution, architecture, bug-fix, learning categories
- Step 3: Codebase explored (if code-related) - relevant files, patterns, dependencies found
- Step 4: Documentation reviewed (if architecture/API) - specs, decisions documented
- Step 5: Sequential thinking analysis `completed` - complexity, estimate, priority determined
- Step 6: Task spec complete - title, content, priority, tags, estimate, comment with context links
- Step 7: User approval explicitly received - YES/APPROVE/CONFIRM
- Step 8: Task created with comment containing memory IDs, file paths, related task IDs
- Step 9: STOP after creation - do NOT execute task

# Comment format
Initial task comment structure for context preservation
- Related memories: #42, #58, #73 (insights about {domain})
- Key files: src/Services/Auth.php:45, app/Models/User.php
- Related tasks: #12 (blocked-by), #15 (related)
- Docs: .docs/architecture/auth-flow.md
- Notes: {any critical insights from research}

</command>