<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Compilation\BrainCLI;
use BrainCore\Compilation\Operator;
use BrainCore\Compilation\Store;
use BrainCore\Compilation\Tools\BashTool;
use BrainCore\Compilation\Tools\TaskTool;

#[Meta('id', 'do')]
#[Meta('description', 'Multi-agent orchestration command for flexible task execution (sequential/parallel) with user approval gates')]
#[Purpose('Coordinates flexible agent execution (sequential by default, parallel when beneficial) with approval checkpoints and comprehensive vector memory integration. Agents communicate through vector memory for knowledge continuity. Accepts $ARGUMENTS task description. Zero distractions, atomic tasks only, strict plan adherence.')]
class DoCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {
        // Iron Rules - Zero Tolerance
        $this->rule('zero-distractions')->critical()
            ->text('ZERO distractions - implement ONLY specified task from $ARGUMENTS. NO creative additions, NO unapproved features, NO scope creep.')
            ->why('Ensures focused execution and prevents feature drift')
            ->onViolation('Abort immediately. Return to approved plan.');

        $this->rule('approval-gates-mandatory')->critical()
            ->text('User approval REQUIRED at Requirements Analysis gate and Execution Planning gate. NEVER proceed without explicit confirmation.')
            ->why('Maintains user control and prevents unauthorized execution')
            ->onViolation('STOP. Wait for user approval before continuing.');

        $this->rule('atomic-tasks-only')->critical()
            ->text('Each agent task MUST be small and focused: maximum 1-2 files per agent invocation. NO large multi-file changes.')
            ->why('Prevents complexity, improves reliability, enables precise tracking')
            ->onViolation('Break task into smaller pieces. Re-plan with atomic steps.');

        $this->rule('no-improvisation')->critical()
            ->text('Execute ONLY approved plan steps. NO improvisation, NO "while we\'re here" additions, NO proactive suggestions during execution.')
            ->why('Maintains plan integrity and predictability')
            ->onViolation('Revert to last approved checkpoint. Resume approved steps only.');

        $this->rule('execution-mode-flexible')->high()
            ->text('Execute agents sequentially BY DEFAULT. Allow parallel execution when: 1) tasks are independent (no file/context conflicts), 2) user explicitly requests parallel mode, 3) optimization benefits outweigh tracking complexity.')
            ->why('Balances safety with performance optimization')
            ->onViolation('Validate task independence before parallel execution. Fallback to sequential if conflicts detected.');

        $this->rule('vector-memory-mandatory')->high()
            ->text('ALL agents MUST search vector memory BEFORE task execution AND store learnings AFTER completion. Vector memory is the primary communication channel between sequential agents.')
            ->why('Enables knowledge sharing between agents, prevents duplicate work, maintains execution continuity across steps')
            ->onViolation('Include explicit vector memory instructions in agent Task() delegation.');

        $this->rule('conversation-context-awareness')->high()
            ->text('ALWAYS analyze conversation context BEFORE planning. User may have discussed requirements, constraints, preferences, or decisions in previous messages.')
            ->why('Prevents ignoring critical information already provided by user in conversation')
            ->onViolation('Review conversation history before proceeding with task analysis.');

        // Phase 0: Conversation Context Analysis
        $this->guideline('phase0-context-analysis')
            ->goal('Extract task insights from conversation history before planning')
            ->example()
            ->phase(Store::as('TASK_DESCRIPTION', 'User task from $ARGUMENTS'))
            ->phase('Analyze conversation context: requirements mentioned, constraints discussed, user preferences, prior decisions, related code/files referenced')
            ->phase(Store::as('CONVERSATION_CONTEXT', '{requirements, constraints, preferences, decisions, references}'))
            ->phase(Operator::if('conversation has relevant context', [
                'Integrate context into task understanding',
                'Note: Use conversation insights throughout all phases',
            ]))
            ->phase(Operator::output([
                '=== PHASE 0: CONTEXT ANALYSIS ===',
                'Task: {$TASK_DESCRIPTION}',
                'Context: {summary of relevant conversation info}',
            ]));

        // Phase 1: Agent Discovery
        $this->guideline('phase1-agent-discovery')
            ->goal('Discover agents leveraging conversation context + vector memory')
            ->example()
            ->phase('mcp__vector-memory__search_memories(query: "similar: {$TASK_DESCRIPTION}", limit: 5, category: "code-solution,architecture")')
            ->phase(Store::as('PAST_SOLUTIONS', 'Past approaches'))
            ->phase(BashTool::describe(BrainCLI::LIST_MASTERS, 'brain list:masters'))
            ->phase(Store::as('AVAILABLE_AGENTS', 'Agents list'))
            ->phase('Match task to agents: $TASK_DESCRIPTION + $CONVERSATION_CONTEXT + $PAST_SOLUTIONS')
            ->phase(Store::as('RELEVANT_AGENTS', '[{agent, capability, rationale}, ...]'))
            ->phase(Operator::output([
                '=== PHASE 1: AGENT DISCOVERY ===',
                'Agents: {selected} | Context: {conversation insights applied}',
            ]));

        // Phase 2: Requirements Analysis + Approval Gate
        $this->guideline('phase2-requirements-analysis-approval')
            ->goal('Create requirements plan leveraging conversation + memory + GET USER APPROVAL')
            ->example()
            ->phase('mcp__vector-memory__search_memories(query: "patterns: {task_domain}", limit: 5, category: "learning,architecture")')
            ->phase(Store::as('IMPLEMENTATION_PATTERNS', 'Past patterns'))
            ->phase('Analyze: $TASK_DESCRIPTION + $CONVERSATION_CONTEXT + $PAST_SOLUTIONS + $IMPLEMENTATION_PATTERNS')
            ->phase('Determine needs: scan targets, web research (if non-trivial), docs scan (if architecture-related)')
            ->phase(Store::as('REQUIREMENTS_PLAN', '{scan_targets, web_research, docs_scan, conversation_insights, memory_learnings}'))
            ->phase(Operator::output([
                '',
                '=== PHASE 2: REQUIREMENTS ANALYSIS ===',
                'Context: {conversation insights} | Memory: {key learnings}',
                'Scanning: {targets} | Research: {status} | Docs: {status}',
                '',
                '⚠️  APPROVAL CHECKPOINT #1',
                '✅ approved/yes | ❌ no/modifications',
            ]))
            ->phase('WAIT for user approval')
            ->phase(Operator::verify('User approved'))
            ->phase(Operator::if('rejected', 'Modify plan → Re-present → WAIT'));

        // Phase 3: Material Gathering with Vector Storage
        $this->guideline('phase3-material-gathering')
            ->goal('Collect materials per plan and store to vector memory. NOTE: brain docs returns file index (Path, Name, Description, etc.), then Read relevant files')
            ->example()
            ->phase(Operator::forEach('scan_target in $REQUIREMENTS_PLAN.scan_targets', [
                TaskTool::describe('Delegate to agent for context extraction from {scan_target}'),
                Store::as('GATHERED_MATERIALS[{target}]', 'Extracted context'),
            ]))
            ->phase(Operator::if('$DOCS_SCAN_NEEDED === true', [
                TaskTool::describe('@agent-documentation-master: Use brain docs {keywords} to find relevant documentation, then Read files'),
                Store::as('DOCS_SCAN_FINDINGS', 'Documentation content from brain docs'),
            ]))
            ->phase(Operator::if('$WEB_RESEARCH_NEEDED === true', [
                TaskTool::describe('@agent-web-research-master: Research best practices for {$TASK_DESCRIPTION}'),
                Store::as('WEB_RESEARCH_FINDINGS', 'External knowledge'),
            ]))
            ->phase(Store::as('CONTEXT_PACKAGES', '{agent_name: {context, materials, task_domain}, ...}'))
            ->phase('Store gathered context: mcp__vector-memory__store_memory(content: "Context for {$TASK_DESCRIPTION}\\n\\nMaterials: {summary}", category: "tool-usage", tags: ["do-command", "context-gathering"])')
            ->phase(Operator::output([
                '=== PHASE 3: MATERIALS GATHERED ===',
                'Materials: {count} | Docs: {status} | Web: {status}',
                'Context stored to vector memory ✓',
            ]));

        // Phase 4: Execution Planning with Vector Memory + Approval Gate
        $this->guideline('phase4-execution-planning-approval')
            ->goal('Create atomic plan leveraging past execution patterns, analyze dependencies, and GET USER APPROVAL')
            ->example()
            ->phase('Search vector memory: mcp__vector-memory__search_memories(query: "execution approach for {task_type}", limit: 5, category: "code-solution")')
            ->phase(Store::as('EXECUTION_PATTERNS', 'Past successful execution approaches'))
            ->phase('Create plan: atomic steps (≤2 files each), logical order, informed by $EXECUTION_PATTERNS')
            ->phase('Analyze step dependencies: file conflicts, context dependencies, data flow')
            ->phase('Determine execution mode: sequential (default/safe) OR parallel (independent tasks/user request/optimization)')
            ->phase(Operator::if('parallel possible AND beneficial', [
                'Group independent steps into parallel batches',
                'Ensure NO file conflicts within groups',
                'Ensure NO context dependencies within groups',
                Store::as('EXECUTION_MODE', 'parallel'),
                Store::as('PARALLEL_GROUPS', '[[step1, step2], [step3], ...]'),
            ]))
            ->phase(Operator::if('NOT parallel OR dependencies detected', [
                Store::as('EXECUTION_MODE', 'sequential'),
            ]))
            ->phase(Store::as('EXECUTION_PLAN',
                '{steps: [{step_number, agent_name, task_description, file_scope: [≤2 files], memory_search_query, expected_outcome}, ...], total_steps: N, execution_mode: "sequential|parallel", parallel_groups: [...]}'))
            ->phase(Operator::verify('Each step has ≤ 2 files'))
            ->phase(Operator::verify('Parallel groups have NO conflicts'))
            ->phase(Operator::output([
                '',
                '=== PHASE 4: EXECUTION PLAN ===',
                'Task: {$TASK_DESCRIPTION} | Steps: {N} | Mode: {execution_mode}',
                'Learned from: {$EXECUTION_PATTERNS summary}',
                '',
                '{Step-by-step breakdown with files and memory search queries}',
                '{If parallel: show grouped batches}',
                '',
                '⚠️  APPROVAL CHECKPOINT #2',
                '✅ Type "approved" or "yes" to begin.',
                '❌ Type "no" or provide modifications.',
            ]))
            ->phase('WAIT for user approval')
            ->phase(Operator::verify('User confirmed approval'))
            ->phase(Operator::if('user rejected', [
                'Accept modifications → Update plan → Verify atomic + dependencies → Re-present → WAIT',
            ]));

        // Phase 5: Flexible Execution with MANDATORY Vector Memory Integration
        $this->guideline('phase5-flexible-execution')
            ->goal('Execute plan with optimal mode (sequential OR parallel) with agents communicating through vector memory')
            ->example()
            ->phase('Initialize: current_step = 1')
            ->phase(Operator::if('$EXECUTION_PLAN.execution_mode === "sequential"', [
                'SEQUENTIAL MODE: Execute steps one-by-one',
                Operator::forEach('step in $EXECUTION_PLAN.steps', [
                    Operator::output([
                        '▶️  Step {current_step}/{total_steps}: @agent-{step.agent_name}',
                        '📝 {step.task_description} | 📁 {step.file_scope}',
                    ]),
                    'Delegate via Task() with MANDATORY vector memory instructions:',
                    '',
                    '  📥 BEFORE: You MUST execute: mcp__vector-memory__search_memories(query: "{step.memory_search_query}", limit: 5, category: "code-solution,learning") and review results',
                    '  🔧 DURING: Execute task: {step.task_description} | Context: {$CONTEXT_PACKAGES} | Files: {step.file_scope} (ATOMIC - no expansion)',
                    '  📤 AFTER: You MUST execute: mcp__vector-memory__store_memory(content: "Step {N}: {outcome}\\n\\nApproach: {what_worked}\\n\\nLearnings: {insights}", category: "code-solution", tags: ["do-command", "step-{N}"])',
                    '',
                    TaskTool::describe('Task(@agent-{name}, {task_with_MANDATORY_memory_instructions})'),
                    Store::as('STEP_RESULTS[{N}]', 'Result with memory trace'),
                    Operator::verify('Step completed AND memory stored'),
                    Operator::output(['✅ Step {N} complete | Memory updated ✓']),
                    'current_step++',
                ]),
            ]))
            ->phase(Operator::if('$EXECUTION_PLAN.execution_mode === "parallel"', [
                'PARALLEL MODE: Execute independent steps concurrently in batches',
                Operator::forEach('group in $EXECUTION_PLAN.parallel_groups', [
                    Operator::output(['🚀 Parallel Batch {batch_number}: {count} steps']),
                    'Launch ALL steps in group CONCURRENTLY via multiple Task() calls in single message:',
                    '',
                    Operator::forEach('step in group', [
                        '  📥 BEFORE: mcp__vector-memory__search_memories(query: "{step.memory_search_query}", limit: 5)',
                        '  🔧 DURING: Execute task: {step.task_description} | Context: {$CONTEXT_PACKAGES} | Files: {step.file_scope}',
                        '  📤 AFTER: mcp__vector-memory__store_memory(content: "Step {N}: {outcome}\\n\\n{insights}", category: "code-solution", tags: ["do-command", "step-{N}"])',
                        '',
                        TaskTool::describe('Task(@agent-{name}, {task_with_memory_instructions})'),
                    ]),
                    '',
                    'WAIT for ALL tasks in batch to complete',
                    Operator::verify('All batch steps completed AND memory stored'),
                    Store::as('BATCH_RESULTS[{batch}]', 'All results from parallel batch'),
                    Operator::output(['✅ Batch {batch} complete ({count} steps) | Memory updated ✓']),
                ]),
            ]))
            ->phase(Operator::if('step fails', [
                'mcp__vector-memory__store_memory(content: "Failure at step {N}: {error}", category: "debugging", tags: ["do-command", "failure"])',
                'Offer: Retry / Skip / Abort → WAIT',
            ]));

        // Phase 6: Completion with Vector Memory Storage
        $this->guideline('phase6-completion-report')
            ->goal('Report results and store comprehensive learnings to vector memory')
            ->example()
            ->phase(Store::as('COMPLETION_SUMMARY', '{completed_steps, files_modified, outcomes, learnings}'))
            ->phase('Store final summary: mcp__vector-memory__store_memory(content: "Completed: {$TASK_DESCRIPTION}\\n\\nApproach: {summary}\\n\\nSteps: {outcomes}\\n\\nLearnings: {insights}\\n\\nFiles: {list}", category: "code-solution", tags: ["do-command", "completed"])')
            ->phase(Operator::output([
                '',
                '=== EXECUTION COMPLETE ===',
                'Task: {$TASK_DESCRIPTION} | Status: {SUCCESS/PARTIAL/FAILED}',
                '✓ Steps: {completed}/{total} | 📁 Files: {count} | 💾 Learnings stored to memory',
                '{step_outcomes}',
            ]))
            ->phase(Operator::if('partial', [
                'Store partial state → List remaining → Suggest resumption',
            ]));

        // Agent Vector Memory Instructions Template
        $this->guideline('agent-memory-instructions')
            ->text('MANDATORY vector memory pattern for ALL agents')
            ->example()
            ->phase('BEFORE TASK:')
            ->do([
                'Execute: mcp__vector-memory__search_memories(query: "{relevant}", limit: 5)',
                'Review: Analyze results for patterns, solutions, learnings',
                'Apply: Incorporate insights into approach',
            ])
            ->phase('DURING TASK:')
            ->do([
                'Focus: Execute ONLY assigned task within file scope',
                'Atomic: Respect 1-2 file limit strictly',
            ])
            ->phase('AFTER TASK:')
            ->do([
                'Document: Summarize what was done, how it worked, key insights',
                'Execute: mcp__vector-memory__store_memory(content: "{what+how+insights}", category: "{appropriate}", tags: [...])',
                'Verify: Confirm storage successful',
            ])
            ->phase('CRITICAL: Vector memory is the communication channel between agents. Your learnings enable the next agent!');

        // Error Handling
        $this->guideline('error-handling')
            ->text('Graceful error handling with recovery options')
            ->example()
            ->phase()->if('no agents available', [
                'Report: "No agents found via brain list:masters"',
                'Suggest: Run /init-agents first',
                'Abort command',
            ])
            ->phase()->if('user rejects requirements plan', [
                'Accept modifications',
                'Rebuild requirements plan',
                'Re-submit for approval',
            ])
            ->phase()->if('user rejects execution plan', [
                'Accept modifications',
                'Rebuild execution plan',
                'Verify atomic task constraints',
                'Re-submit for approval',
            ])
            ->phase()->if('agent execution fails', [
                'Log: "Step {N} failed: {error}"',
                'Offer options:',
                '  1. Retry current step',
                '  2. Skip and continue',
                '  3. Abort remaining steps',
                'WAIT for user decision',
            ])
            ->phase()->if('documentation scan fails', [
                'Log: "brain docs command failed or no documentation found"',
                'Proceed without documentation context',
                'Note: "Documentation context unavailable"',
            ])
            ->phase()->if('web research timeout', [
                'Log: "Web research timed out - continuing without external knowledge"',
                'Proceed with local context only',
            ])
            ->phase()->if('context gathering fails', [
                'Log: "Failed to gather {context_type}"',
                'Proceed with available context',
                'Warn: "Limited context may affect quality"',
            ]);

        // Constraints and Validation
        $this->guideline('constraints-validation')
            ->text('Enforcement of critical constraints throughout execution')
            ->example()
            ->phase('Before Requirements Analysis: Verify $ARGUMENTS is not empty')
            ->phase('Before Phase 2 → Phase 3 transition: Verify user approval received')
            ->phase('Before Phase 4 → Phase 5 transition: Verify user approval received')
            ->phase('During Execution Planning: Verify each step has ≤ 2 files in scope')
            ->phase('During Execution: Verify dependencies respected (sequential: step order, parallel: no conflicts)')
            ->phase('Throughout: NO unapproved steps allowed')
            ->phase(Operator::verify([
                'approval_checkpoints_passed = 2',
                'all_tasks_atomic = true (≤ 2 files each)',
                'execution_mode = sequential OR parallel (validated)',
                'improvisation_count = 0',
            ]));

        // Examples
        $this->guideline('example-0-conversation-context')
            ->scenario('Task with conversation context')
            ->example()
            ->phase('conversation', 'User: "I want to use Redis for caching" → "Prefer atomic commits" → "Follow PSR-12"')
            ->phase('input', '$ARGUMENTS = "Add caching to product catalog"')
            ->phase('phase0', 'Context: Redis requirement, atomic commits preference, PSR-12 standard')
            ->phase('phase1-6', 'Execute with conversation insights: Redis driver, atomic steps, PSR-12 formatting');

        $this->guideline('example-1-simple-task')
            ->scenario('Simple single-agent task')
            ->example()
            ->phase('input', '$ARGUMENTS = "Fix authentication bug in LoginController.php"')
            ->phase('phases', 'Discovery → Requirements (approved) → Gather → Plan (approved) → Execute → Complete: 1/1 ✓');

        $this->guideline('example-2-multi-step-task')
            ->scenario('Complex multi-agent task with web research')
            ->example()
            ->phase('input', '$ARGUMENTS = "Add Laravel rate limiting to API endpoints"')
            ->phase('phase1', 'Agents: @agent-web-research-master, @agent-code-master, @agent-documentation-master')
            ->phase('phase2', 'Requirements Plan: Research Laravel rate limiting, scan API routes, identify endpoints')
            ->phase('approval1', 'User approves (including web research)')
            ->phase('phase3', 'Gather: Web research findings, routes/api.php, middleware list')
            ->phase('phase4', 'Execution Plan:')
            ->do([
                'Step 1: @agent-code-master - Create RateLimitMiddleware.php',
                'Step 2: @agent-code-master - Update app/Http/Kernel.php',
                'Step 3: @agent-code-master - Apply middleware to routes/api.php',
                'Step 4: @agent-documentation-master - Update API docs',
            ])
            ->phase('approval2', 'User approves execution plan')
            ->phase('phase5', 'Sequential execution: Steps 1→2→3→4')
            ->phase('phase6', 'Report: 4/4 steps complete - rate limiting implemented ✓');

        $this->guideline('example-3-approval-rejection')
            ->scenario('User rejects execution plan, requests modifications')
            ->example()
            ->phase('input', '$ARGUMENTS = "Refactor UserService to use repository pattern"')
            ->phase('phase1-4', 'Standard flow through execution planning')
            ->phase('approval2', 'User responds: "No, split Step 3 into smaller pieces"')
            ->phase('revision', 'Rebuild execution plan with Step 3 split into 3a, 3b, 3c')
            ->phase('re-approval', 'Re-present plan → User approves')
            ->phase('phase5', 'Execute revised plan')
            ->phase('phase6', 'Report: Completed with revised plan ✓');

        $this->guideline('example-4-documentation-scan')
            ->scenario('Task requiring project documentation context')
            ->example()
            ->phase('input', '$ARGUMENTS = "Implement feature based on architecture described in documentation"')
            ->phase('phase1', 'Agent Discovery: Selected @agent-documentation-master, @agent-code-master')
            ->phase('phase2', 'Requirements Plan: Search documentation via brain docs, identify feature requirements')
            ->phase('approval1', 'User approves (including documentation scan)')
            ->phase('phase3', 'Gather: Documentation results from brain docs, related code files')
            ->phase('phase4', 'Execution Plan:')
            ->do([
                'Step 1: @agent-code-master - Create FeatureService.php based on docs',
                'Step 2: @agent-code-master - Integrate with existing architecture',
            ])
            ->phase('approval2', 'User approves execution plan')
            ->phase('phase5', 'Sequential execution: Steps 1→2')
            ->phase('phase6', 'Report: 2/2 steps complete - feature implemented per documentation ✓');

        $this->guideline('example-5-parallel-execution')
            ->scenario('Parallel execution for independent tasks')
            ->example()
            ->phase('input', '$ARGUMENTS = "Add validation to UserController, ProductController, and OrderController"')
            ->phase('phase1', 'Agent Discovery: Selected @agent-code-master (for all 3 tasks)')
            ->phase('phase2', 'Requirements Plan: Scan controllers, identify validation needs')
            ->phase('approval1', 'User approves requirements plan')
            ->phase('phase3', 'Gather: Controller files, validation rules patterns')
            ->phase('phase4', 'Execution Plan:')
            ->do([
                'Mode: PARALLEL (tasks are independent, no file conflicts)',
                'Batch 1 (parallel):',
                '  Step 1: @agent-code-master - Add validation to UserController.php',
                '  Step 2: @agent-code-master - Add validation to ProductController.php',
                '  Step 3: @agent-code-master - Add validation to OrderController.php',
            ])
            ->phase('approval2', 'User approves parallel execution plan')
            ->phase('phase5', 'Parallel execution: Batch 1 (3 steps concurrently)')
            ->phase('phase6', 'Report: 3/3 steps complete (parallel) - validation implemented ✓');

        // Response Format
        $this->guideline('response-format')
            ->text('Structured output format for each phase')
            ->example()
            ->phase('Phase headers with === markers')
            ->phase('Bullet-point plans with clear structure')
            ->phase('Approval checkpoints with ⚠️  and clear instructions')
            ->phase('Progress indicators: ▶️ ✅ ❌ 📋 📁 ⏱️')
            ->phase('File scope explicitly listed for each step')
            ->phase('No extraneous commentary during execution')
            ->phase('Clear status indicators for completion');

        // Directive
        $this->guideline('directive')
            ->text('Execute ONLY specified task! Get approvals at checkpoints! Atomic tasks ONLY! Flexible execution (sequential by default, parallel when beneficial)! Vector memory MANDATORY for ALL agents! NO improvisation! Zero distractions! Strict plan adherence!');
    }
}
