---
name: do
description: "Multi-agent orchestration command for flexible task execution (sequential/parallel) with user approval gates"
---

<command>
<meta>
<id>do</id>
<description>Multi-agent orchestration command for flexible task execution (sequential/parallel) with user approval gates</description>
</meta>
<purpose>Coordinates flexible agent execution (sequential by default, parallel when beneficial) with approval checkpoints and comprehensive vector memory integration. Agents communicate through vector memory for knowledge continuity. Accepts $ARGUMENTS task description. Zero distractions, atomic tasks only, strict plan adherence.</purpose>
<iron_rules>
<rule id="zero-distractions" severity="critical">
<text>ZERO distractions - implement ONLY specified task from $ARGUMENTS. NO creative additions, NO unapproved features, NO scope creep.</text>
<why>Ensures focused execution and prevents feature drift</why>
<on_violation>Abort immediately. Return to approved plan.</on_violation>
</rule>
<rule id="approval-gates-mandatory" severity="critical">
<text>User approval REQUIRED at Requirements Analysis gate and Execution Planning gate. NEVER proceed without explicit confirmation.</text>
<why>Maintains user control and prevents unauthorized execution</why>
<on_violation>STOP. Wait for user approval before continuing.</on_violation>
</rule>
<rule id="atomic-tasks-only" severity="critical">
<text>Each agent task MUST be small and focused: maximum 1-2 files per agent invocation. NO large multi-file changes.</text>
<why>Prevents complexity, improves reliability, enables precise tracking</why>
<on_violation>Break task into smaller pieces. Re-plan with atomic steps.</on_violation>
</rule>
<rule id="no-improvisation" severity="critical">
<text>Execute ONLY approved plan steps. NO improvisation, NO "while we're here" additions, NO proactive suggestions during execution.</text>
<why>Maintains plan integrity and predictability</why>
<on_violation>Revert to last approved checkpoint. Resume approved steps only.</on_violation>
</rule>
<rule id="execution-mode-flexible" severity="high">
<text>Execute agents sequentially BY DEFAULT. Allow parallel execution when: 1) tasks are independent (no file/context conflicts), 2) user explicitly requests parallel mode, 3) optimization benefits outweigh tracking complexity.</text>
<why>Balances safety with performance optimization</why>
<on_violation>Validate task independence before parallel execution. Fallback to sequential if conflicts detected.</on_violation>
</rule>
<rule id="vector-memory-mandatory" severity="high">
<text>ALL agents MUST search vector memory BEFORE task execution AND store learnings AFTER completion. Vector memory is the primary communication channel between sequential agents.</text>
<why>Enables knowledge sharing between agents, prevents duplicate work, maintains execution continuity across steps</why>
<on_violation>Include explicit vector memory instructions in agent Task() delegation.</on_violation>
</rule>
<rule id="conversation-context-awareness" severity="high">
<text>ALWAYS analyze conversation context BEFORE planning. User may have discussed requirements, constraints, preferences, or decisions in previous messages.</text>
<why>Prevents ignoring critical information already provided by user in conversation</why>
<on_violation>Review conversation history before proceeding with task analysis.</on_violation>
</rule>
</iron_rules>
<guidelines>
<guideline id="phase0-context-analysis">
GOAL(Extract task insights from conversation history before planning)
<example>
<phase name="1">STORE-AS($ = 'User task from $ARGUMENTS')</phase>
<phase name="2">Analyze conversation context: requirements mentioned, constraints discussed, user preferences, prior decisions, related code/files referenced</phase>
<phase name="3">STORE-AS($ = '{requirements, constraints, preferences, decisions, references}')</phase>
<phase name="4">IF(conversation has relevant context) → THEN → [Integrate context into task understanding → Note: Use conversation insights throughout all phases] → END-IF</phase>
<phase name="5">OUTPUT(=== PHASE 0: CONTEXT ANALYSIS === Task: {$TASK_DESCRIPTION} Context: {summary of relevant conversation info})</phase>
</example>
</guideline>
<guideline id="phase1-agent-discovery">
GOAL(Discover agents leveraging conversation context + vector memory)
<example>
<phase name="1">mcp__vector-memory__search_memories(query: "similar: {$TASK_DESCRIPTION}", limit: 5, category: "code-solution,architecture")</phase>
<phase name="2">STORE-AS($ = 'Past approaches')</phase>
<phase name="3">Bash(brain list:masters) → [brain list:masters] → END-Bash</phase>
<phase name="4">STORE-AS($ = 'Agents list')</phase>
<phase name="5">Match task to agents: $TASK_DESCRIPTION + $CONVERSATION_CONTEXT + $PAST_SOLUTIONS</phase>
<phase name="6">STORE-AS($ = '[{agent, capability, rationale}, ...]')</phase>
<phase name="7">OUTPUT(=== PHASE 1: AGENT DISCOVERY === Agents: {selected} | Context: {conversation insights applied})</phase>
</example>
</guideline>
<guideline id="phase2-requirements-analysis-approval">
GOAL(Create requirements plan leveraging conversation + memory + GET USER APPROVAL)
<example>
<phase name="1">mcp__vector-memory__search_memories(query: "patterns: {task_domain}", limit: 5, category: "learning,architecture")</phase>
<phase name="2">STORE-AS($ = 'Past patterns')</phase>
<phase name="3">Analyze: $TASK_DESCRIPTION + $CONVERSATION_CONTEXT + $PAST_SOLUTIONS + $IMPLEMENTATION_PATTERNS</phase>
<phase name="4">Determine needs: scan targets, web research (if non-trivial), docs scan (if architecture-related)</phase>
<phase name="5">STORE-AS($ = '{scan_targets, web_research, docs_scan, conversation_insights, memory_learnings}')</phase>
<phase name="6">OUTPUT( === PHASE 2: REQUIREMENTS ANALYSIS === Context: {conversation insights} | Memory: {key learnings} Scanning: {targets} | Research: {status} | Docs: {status}  ⚠️  APPROVAL CHECKPOINT #1 ✅ approved/yes | ❌ no/modifications)</phase>
<phase name="7">WAIT for user approval</phase>
<phase name="8">VERIFY-SUCCESS(User approved)</phase>
<phase name="9">IF(rejected) → THEN → [Modify plan → Re-present → WAIT] → END-IF</phase>
</example>
</guideline>
<guideline id="phase3-material-gathering">
GOAL(Collect materials per plan and store to vector memory. NOTE: brain docs returns file index (Path, Name, Description, etc.), then Read relevant files)
<example>
<phase name="1">FOREACH(scan_target in $REQUIREMENTS_PLAN.scan_targets) → [Task(Delegate to agent for context extraction from {scan_target}) → STORE-AS($ = 'Extracted context')] → END-FOREACH</phase>
<phase name="2">IF($DOCS_SCAN_NEEDED === true) → THEN → [Task(@agent-documentation-master: Use brain docs {keywords} to find relevant documentation, then Read files) → STORE-AS($ = 'Documentation content from brain docs')] → END-IF</phase>
<phase name="3">IF($WEB_RESEARCH_NEEDED === true) → THEN → [Task(@agent-web-research-master: Research best practices for {$TASK_DESCRIPTION}) → STORE-AS($ = 'External knowledge')] → END-IF</phase>
<phase name="4">STORE-AS($ = '{agent_name: {context, materials, task_domain}, ...}')</phase>
<phase name="5">Store gathered context: mcp__vector-memory__store_memory(content: "Context for {$TASK_DESCRIPTION}\n\nMaterials: {summary}", category: "tool-usage", tags: ["do-command", "context-gathering"])</phase>
<phase name="6">OUTPUT(=== PHASE 3: MATERIALS GATHERED === Materials: {count} | Docs: {status} | Web: {status} Context stored to vector memory ✓)</phase>
</example>
</guideline>
<guideline id="phase4-execution-planning-approval">
GOAL(Create atomic plan leveraging past execution patterns, analyze dependencies, and GET USER APPROVAL)
<example>
<phase name="1">Search vector memory: mcp__vector-memory__search_memories(query: "execution approach for {task_type}", limit: 5, category: "code-solution")</phase>
<phase name="2">STORE-AS($ = 'Past successful execution approaches')</phase>
<phase name="3">Create plan: atomic steps (≤2 files each), logical order, informed by $EXECUTION_PATTERNS</phase>
<phase name="4">Analyze step dependencies: file conflicts, context dependencies, data flow</phase>
<phase name="5">Determine execution mode: sequential (default/safe) OR parallel (independent tasks/user request/optimization)</phase>
<phase name="6">IF(parallel possible AND beneficial) → THEN → [Group independent steps into parallel batches → Ensure NO file conflicts within groups → Ensure NO context dependencies within groups → STORE-AS($ = 'parallel') → STORE-AS($ = '[[step1, step2], [step3], ...]')] → END-IF</phase>
<phase name="7">IF(NOT parallel OR dependencies detected) → THEN → [STORE-AS($ = 'sequential')] → END-IF</phase>
<phase name="8">STORE-AS($ = '{steps: [{step_number, agent_name, task_description, file_scope: [≤2 files], memory_search_query, expected_outcome}, ...], total_steps: N, execution_mode: "sequential|parallel", parallel_groups: [...]}')</phase>
<phase name="9">VERIFY-SUCCESS(Each step has ≤ 2 files)</phase>
<phase name="10">VERIFY-SUCCESS(Parallel groups have NO conflicts)</phase>
<phase name="11">OUTPUT( === PHASE 4: EXECUTION PLAN === Task: {$TASK_DESCRIPTION} | Steps: {N} | Mode: {execution_mode} Learned from: {$EXECUTION_PATTERNS summary}  {Step-by-step breakdown with files and memory search queries} {If parallel: show grouped batches}  ⚠️  APPROVAL CHECKPOINT #2 ✅ Type "approved" or "yes" to begin. ❌ Type "no" or provide modifications.)</phase>
<phase name="12">WAIT for user approval</phase>
<phase name="13">VERIFY-SUCCESS(User confirmed approval)</phase>
<phase name="14">IF(user rejected) → THEN → [Accept modifications → Update plan → Verify atomic + dependencies → Re-present → WAIT] → END-IF</phase>
</example>
</guideline>
<guideline id="phase5-flexible-execution">
GOAL(Execute plan with optimal mode (sequential OR parallel) with agents communicating through vector memory)
<example>
<phase name="1">Initialize: current_step = 1</phase>
<phase name="2">IF($EXECUTION_PLAN.execution_mode === "sequential") → THEN → [SEQUENTIAL MODE: Execute steps one-by-one → FOREACH(step in $EXECUTION_PLAN.steps) → [OUTPUT(▶️  Step {current_step}/{total_steps}: @agent-{step.agent_name} 📝 {step.task_description} | 📁 {step.file_scope}) → Delegate via Task() with MANDATORY vector memory instructions: →   📥 BEFORE: You MUST execute: mcp__vector-memory__search_memories(query: "{step.memory_search_query}", limit: 5, category: "code-solution,learning") and review results →   🔧 DURING: Execute task: {step.task_description} | Context: {$CONTEXT_PACKAGES} | Files: {step.file_scope} (ATOMIC - no expansion) →   📤 AFTER: You MUST execute: mcp__vector-memory__store_memory(content: "Step {N}: {outcome}\n\nApproach: {what_worked}\n\nLearnings: {insights}", category: "code-solution", tags: ["do-command", "step-{N}"]) → Task(Task(@agent-{name}, {task_with_MANDATORY_memory_instructions})) → STORE-AS($ = 'Result with memory trace') → VERIFY-SUCCESS(Step completed AND memory stored) → OUTPUT(✅ Step {N} complete | Memory updated ✓) → current_step++] → END-FOREACH] → END-IF</phase>
<phase name="3">IF($EXECUTION_PLAN.execution_mode === "parallel") → THEN → [PARALLEL MODE: Execute independent steps concurrently in batches → FOREACH(group in $EXECUTION_PLAN.parallel_groups) → [OUTPUT(🚀 Parallel Batch {batch_number}: {count} steps) → Launch ALL steps in group CONCURRENTLY via multiple Task() calls in single message: → FOREACH(step in group) → [  📥 BEFORE: mcp__vector-memory__search_memories(query: "{step.memory_search_query}", limit: 5) →   🔧 DURING: Execute task: {step.task_description} | Context: {$CONTEXT_PACKAGES} | Files: {step.file_scope} →   📤 AFTER: mcp__vector-memory__store_memory(content: "Step {N}: {outcome}\n\n{insights}", category: "code-solution", tags: ["do-command", "step-{N}"]) → Task(Task(@agent-{name}, {task_with_memory_instructions}))] → END-FOREACH → WAIT for ALL tasks in batch to complete → VERIFY-SUCCESS(All batch steps completed AND memory stored) → STORE-AS($ = 'All results from parallel batch') → OUTPUT(✅ Batch {batch} complete ({count} steps) | Memory updated ✓)] → END-FOREACH] → END-IF</phase>
<phase name="4">IF(step fails) → THEN → [mcp__vector-memory__store_memory(content: "Failure at step {N}: {error}", category: "debugging", tags: ["do-command", "failure"]) → Offer: Retry / Skip / Abort → WAIT] → END-IF</phase>
</example>
</guideline>
<guideline id="phase6-completion-report">
GOAL(Report results and store comprehensive learnings to vector memory)
<example>
<phase name="1">STORE-AS($ = '{completed_steps, files_modified, outcomes, learnings}')</phase>
<phase name="2">Store final summary: mcp__vector-memory__store_memory(content: "Completed: {$TASK_DESCRIPTION}\n\nApproach: {summary}\n\nSteps: {outcomes}\n\nLearnings: {insights}\n\nFiles: {list}", category: "code-solution", tags: ["do-command", "completed"])</phase>
<phase name="3">OUTPUT( === EXECUTION COMPLETE === Task: {$TASK_DESCRIPTION} | Status: {SUCCESS/PARTIAL/FAILED} ✓ Steps: {completed}/{total} | 📁 Files: {count} | 💾 Learnings stored to memory {step_outcomes})</phase>
<phase name="4">IF(partial) → THEN → [Store partial state → List remaining → Suggest resumption] → END-IF</phase>
</example>
</guideline>
<guideline id="agent-memory-instructions">
<text>MANDATORY vector memory pattern for ALL agents</text>
<example>
<phase name="1">
BEFORE TASK:
(Execute: mcp__vector-memory__search_memories(query: "{relevant}", limit: 5) + Review: Analyze results for patterns, solutions, learnings + Apply: Incorporate insights into approach)
</phase>
<phase name="2">
DURING TASK:
(Focus: Execute ONLY assigned task within file scope + Atomic: Respect 1-2 file limit strictly)
</phase>
<phase name="3">
AFTER TASK:
(Document: Summarize what was done, how it worked, key insights + Execute: mcp__vector-memory__store_memory(content: "{what+how+insights}", category: "{appropriate}", tags: [...]) + Verify: Confirm storage successful)
</phase>
<phase name="4">CRITICAL: Vector memory is the communication channel between agents. Your learnings enable the next agent!</phase>
</example>
</guideline>
<guideline id="error-handling">
<text>Graceful error handling with recovery options</text>
<example>
<phase name="1">IF(no agents available) → THEN → [Report: "No agents found via brain list:masters" → Suggest: Run /init-agents first → Abort command] → END-IF</phase>
<phase name="2">IF(user rejects requirements plan) → THEN → [Accept modifications → Rebuild requirements plan → Re-submit for approval] → END-IF</phase>
<phase name="3">IF(user rejects execution plan) → THEN → [Accept modifications → Rebuild execution plan → Verify atomic task constraints → Re-submit for approval] → END-IF</phase>
<phase name="4">IF(agent execution fails) → THEN → [Log: "Step {N} failed: {error}" → Offer options: →   1. Retry current step →   2. Skip and continue →   3. Abort remaining steps → WAIT for user decision] → END-IF</phase>
<phase name="5">IF(documentation scan fails) → THEN → [Log: "brain docs command failed or no documentation found" → Proceed without documentation context → Note: "Documentation context unavailable"] → END-IF</phase>
<phase name="6">IF(web research timeout) → THEN → [Log: "Web research timed out - continuing without external knowledge" → Proceed with local context only] → END-IF</phase>
<phase name="7">IF(context gathering fails) → THEN → [Log: "Failed to gather {context_type}" → Proceed with available context → Warn: "Limited context may affect quality"] → END-IF</phase>
</example>
</guideline>
<guideline id="constraints-validation">
<text>Enforcement of critical constraints throughout execution</text>
<example>
<phase name="1">Before Requirements Analysis: Verify $ARGUMENTS is not empty</phase>
<phase name="2">Before Phase 2 → Phase 3 transition: Verify user approval received</phase>
<phase name="3">Before Phase 4 → Phase 5 transition: Verify user approval received</phase>
<phase name="4">During Execution Planning: Verify each step has ≤ 2 files in scope</phase>
<phase name="5">During Execution: Verify dependencies respected (sequential: step order, parallel: no conflicts)</phase>
<phase name="6">Throughout: NO unapproved steps allowed</phase>
<phase name="7">VERIFY-SUCCESS(approval_checkpoints_passed = 2 all_tasks_atomic = true (≤ 2 files each) execution_mode = sequential OR parallel (validated) improvisation_count = 0)</phase>
</example>
</guideline>
<guideline id="example-0-conversation-context">
SCENARIO(Task with conversation context)
<example>
<phase name="conversation">User: "I want to use Redis for caching" → "Prefer atomic commits" → "Follow PSR-12"</phase>
<phase name="input">$ARGUMENTS = "Add caching to product catalog"</phase>
<phase name="phase0">Context: Redis requirement, atomic commits preference, PSR-12 standard</phase>
<phase name="phase1-6">Execute with conversation insights: Redis driver, atomic steps, PSR-12 formatting</phase>
</example>
</guideline>
<guideline id="example-1-simple-task">
SCENARIO(Simple single-agent task)
<example>
<phase name="input">$ARGUMENTS = "Fix authentication bug in LoginController.php"</phase>
<phase name="phases">Discovery → Requirements (approved) → Gather → Plan (approved) → Execute → Complete: 1/1 ✓</phase>
</example>
</guideline>
<guideline id="example-2-multi-step-task">
SCENARIO(Complex multi-agent task with web research)
<example>
<phase name="input">$ARGUMENTS = "Add Laravel rate limiting to API endpoints"</phase>
<phase name="phase1">Agents: @agent-web-research-master, @agent-code-master, @agent-documentation-master</phase>
<phase name="phase2">Requirements Plan: Research Laravel rate limiting, scan API routes, identify endpoints</phase>
<phase name="approval1">User approves (including web research)</phase>
<phase name="phase3">Gather: Web research findings, routes/api.php, middleware list</phase>
<phase name="phase4">
Execution Plan:
(Step 1: @agent-code-master - Create RateLimitMiddleware.php + Step 2: @agent-code-master - Update app/Http/Kernel.php + Step 3: @agent-code-master - Apply middleware to routes/api.php + Step 4: @agent-documentation-master - Update API docs)
</phase>
<phase name="approval2">User approves execution plan</phase>
<phase name="phase5">Sequential execution: Steps 1→2→3→4</phase>
<phase name="phase6">Report: 4/4 steps complete - rate limiting implemented ✓</phase>
</example>
</guideline>
<guideline id="example-3-approval-rejection">
SCENARIO(User rejects execution plan, requests modifications)
<example>
<phase name="input">$ARGUMENTS = "Refactor UserService to use repository pattern"</phase>
<phase name="phase1-4">Standard flow through execution planning</phase>
<phase name="approval2">User responds: "No, split Step 3 into smaller pieces"</phase>
<phase name="revision">Rebuild execution plan with Step 3 split into 3a, 3b, 3c</phase>
<phase name="re-approval">Re-present plan → User approves</phase>
<phase name="phase5">Execute revised plan</phase>
<phase name="phase6">Report: Completed with revised plan ✓</phase>
</example>
</guideline>
<guideline id="example-4-documentation-scan">
SCENARIO(Task requiring project documentation context)
<example>
<phase name="input">$ARGUMENTS = "Implement feature based on architecture described in documentation"</phase>
<phase name="phase1">Agent Discovery: Selected @agent-documentation-master, @agent-code-master</phase>
<phase name="phase2">Requirements Plan: Search documentation via brain docs, identify feature requirements</phase>
<phase name="approval1">User approves (including documentation scan)</phase>
<phase name="phase3">Gather: Documentation results from brain docs, related code files</phase>
<phase name="phase4">
Execution Plan:
(Step 1: @agent-code-master - Create FeatureService.php based on docs + Step 2: @agent-code-master - Integrate with existing architecture)
</phase>
<phase name="approval2">User approves execution plan</phase>
<phase name="phase5">Sequential execution: Steps 1→2</phase>
<phase name="phase6">Report: 2/2 steps complete - feature implemented per documentation ✓</phase>
</example>
</guideline>
<guideline id="example-5-parallel-execution">
SCENARIO(Parallel execution for independent tasks)
<example>
<phase name="input">$ARGUMENTS = "Add validation to UserController, ProductController, and OrderController"</phase>
<phase name="phase1">Agent Discovery: Selected @agent-code-master (for all 3 tasks)</phase>
<phase name="phase2">Requirements Plan: Scan controllers, identify validation needs</phase>
<phase name="approval1">User approves requirements plan</phase>
<phase name="phase3">Gather: Controller files, validation rules patterns</phase>
<phase name="phase4">
Execution Plan:
(Mode: PARALLEL (tasks are independent, no file conflicts) + Batch 1 (parallel): +   Step 1: @agent-code-master - Add validation to UserController.php +   Step 2: @agent-code-master - Add validation to ProductController.php +   Step 3: @agent-code-master - Add validation to OrderController.php)
</phase>
<phase name="approval2">User approves parallel execution plan</phase>
<phase name="phase5">Parallel execution: Batch 1 (3 steps concurrently)</phase>
<phase name="phase6">Report: 3/3 steps complete (parallel) - validation implemented ✓</phase>
</example>
</guideline>
<guideline id="response-format">
<text>Structured output format for each phase</text>
<example>
<phase name="1">Phase headers with === markers</phase>
<phase name="2">Bullet-point plans with clear structure</phase>
<phase name="3">Approval checkpoints with ⚠️  and clear instructions</phase>
<phase name="4">Progress indicators: ▶️ ✅ ❌ 📋 📁 ⏱️</phase>
<phase name="5">File scope explicitly listed for each step</phase>
<phase name="6">No extraneous commentary during execution</phase>
<phase name="7">Clear status indicators for completion</phase>
</example>
</guideline>
<guideline id="directive">
<text>Execute ONLY specified task! Get approvals at checkpoints! Atomic tasks ONLY! Flexible execution (sequential by default, parallel when beneficial)! Vector memory MANDATORY for ALL agents! NO improvisation! Zero distractions! Strict plan adherence!</text>
</guideline>
</guidelines>
</command>