<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;

#[Meta('id', 'lfc')]
#[Meta('description', 'Execute task/subtask/plan/step via multi-agent orchestration (sequential + conversational)')]
#[Purpose('Sequential task executor with pm-master context-first approach. User dialog about CODE/ACTIONS, not workflow steps.')]
class LfcCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {
        // Iron Rules
        $this->rule('pm-master-first')->critical()
            ->text('pm-master FIRST (context before ANY search) - EXPLICITLY use Skill(delegation-enforcer)')
            ->why('Context-first approach ensures proper understanding before any action')
            ->onViolation('Trigger correction protocol');

        $this->rule('accept-valid-identifiers')->critical()
            ->text('Accept ANY valid task identifier (zeros VALID - zero-based indexing)')
            ->why('Zero-based indexing is valid system design')
            ->onViolation('NEVER reject Task-0.0 as "placeholder"');

        $this->rule('silent-research')->high()
            ->text('Research phase (1-7): execute silently, NO user questions about workflow')
            ->why('User focuses on CODE decisions, not workflow mechanics')
            ->onViolation('Do not ask about workflow progression');

        $this->rule('code-only-questions')->high()
            ->text('Plan phase (8): ask "Execute THIS code?" (WHAT not HOW). Execution phase (10): ask before each CODE action. Completion phase (11): ask about closing Issue')
            ->why('User dialog limited to actionable CODE decisions')
            ->onViolation('Never ask about workflow steps');

        $this->rule('sequential-thinking')->critical()
            ->text('EXPLICITLY use mcp__sequential-thinking__sequentialthinking 4× (analyze → strategize → refine → verify)')
            ->why('Structured reasoning ensures quality decision-making')
            ->onViolation('Missing reasoning phase compromises quality');

        $this->rule('web-research-optional')->medium()
            ->text('Web research optional (ASK user first)')
            ->why('User controls external research to manage time')
            ->onViolation('Never perform web research without explicit approval');

        $this->rule('no-parallel')->critical()
            ->text('NO parallel execution (sequential only)')
            ->why('Maintains control and predictability')
            ->onViolation('Execute tasks sequentially');

        // Usage Guidelines
        $this->guideline('when-to-use')
            ->text('Command invocation patterns')
            ->example('/lfc Task-X.Y (e.g., Task-0.0, Task-1.2)')
            ->example('/lfc Subtask-X.Y.Z (e.g., Subtask-0.0.0, Subtask-1.2.3)')
            ->example('/lfc Plan-X.Y.Z.P (e.g., Plan-0.1.2.0)')
            ->example('/lfc Step-X.Y.Z.P.S (e.g., Step-0.0.0.0.0)')
            ->example('Note: Zero-based indexing VALID. Any layer executable. IF $ARGUMENTS empty → ask which Issue.');

        // Workflow Phase 1 - Research (Silent)
        $this->guideline('phase1-pm-master')
            ->text('Goal: pm-master reads context')
            ->example()
                ->phase('logic-1', 'Task(@agent-pm-master, "Read Issue {$ARGUMENTS}. Extract: description, objectives, requirements, constraints, research refs (Memory IDs, .docs/ links), ALL comments chronological, related Issues. EXPLICITLY use Skill(quality-gate-checker) for validation.")')
                ->phase('output-1', 'Present: Task: {name}, Objective: {summary}, Context: {parent_info}');

        $this->guideline('phase1-initial-analysis')
            ->text('Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Initial analysis (5–8 thoughts)')
            ->example()
                ->phase('focus-1', 'objective, domains, knowledge needs, agents, unknowns');

        $this->guideline('phase1-vector-docs-search')
            ->text('Goal: vector-master + documents-master search')
            ->example()
                ->phase('vector-1', 'Task(@agent-vector-master, "Search: {task_scope}. Query: {specific}. Category: code-solution|architecture|bug-fix. Limit: 10. EXPLICITLY use Skill(context-analyzer) for relevance.")')
                ->phase('docs-1', 'Task(@agent-documents-master, "Find .docs/ for: {task_scope}. Extract: MANDATORY constraints, RECOMMENDED patterns, standards, testing reqs. EXPLICITLY use Skill(quality-gate-checker) for completeness.")');

        $this->guideline('phase1-strategy')
            ->text('Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Strategy (6–10 thoughts)')
            ->example()
                ->phase('focus-1', 'requirements, learnings, approach, agents, sequence, risks');

        $this->guideline('phase1-agents-registry')
            ->text('Goal: Read agents registry')
            ->example()
                ->phase('action-1', 'Bash(\'brain list:masters\')');

        $this->guideline('phase1-web-research')
            ->text('Goal: Optional web research (ASK first)')
            ->example()
                ->phase('ask-1', 'Ask: "Research \'{task_topic}\' best practices 2025 online? (yes/no/specific-query)"')
                ->phase('execute-1', 'IF yes: Task(@agent-web-research-master, "Research: {task_topic}. EXPLICITLY use Skill(edge-case-handler) for quality validation.")');

        $this->guideline('phase1-refine')
            ->text('Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Refine (6–10 thoughts)')
            ->example()
                ->phase('focus-1', 'all knowledge integration, optimal sequence, plan, deliverables, verification')
                ->phase('note-1', 'STEPS 1-7 executed silently, NO workflow questions');

        // Workflow Phase 2 - Plan Presentation
        $this->guideline('phase2-show-plan')
            ->text('Goal: Show execution plan')
            ->example()
                ->phase('present-1', '**Task:** {name}')
                ->phase('present-2', '**Goal:** {objective}')
                ->phase('present-3', '**Code to Write/Modify:** 1. @agent-{name} → {specific_action}, File: {path}, Purpose: {what_code_does}')
                ->phase('present-4', '**Constraints:** {from_.docs}')
                ->phase('present-5', '**Success:** {criteria}')
                ->phase('ask-1', 'Ask: "Execute this plan? (yes/adjust/cancel)"')
                ->phase('branch-1', 'yes → STEP 10')
                ->phase('branch-2', 'adjust → rebuild → STEP 8')
                ->phase('branch-3', 'cancel → abort');

        // Workflow Phase 3 - Execution
        $this->guideline('phase3-execute')
            ->text('Goal: FOR EACH agent')
            ->example()
                ->phase('show-1', 'Execute: @agent-{name} → {task}, File: {path}, Code: {what_will_be_coded}')
                ->phase('ask-1', 'Ask: "Proceed? (yes/skip/adjust/cancel)"')
                ->phase('execute-1', 'IF yes: Task(@agent-{name}, "{mission}. Context: {task}, requirements: {reqs}, constraints: {from_.docs}, previous: {results}. EXPLICITLY use ALL available Skills from personality banks.")')
                ->phase('show-2', 'Created/Modified: {files}, Status: {success|partial|blocked}, Summary: {done}')
                ->phase('continue-1', 'Continue OR STEP 11 if last');

        // Workflow Phase 4 - Completion
        $this->guideline('phase4-verify')
            ->text('Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Verify (7–10 thoughts)')
            ->example()
                ->phase('focus-1', 'requirements met, deliverables complete, gaps, quality, ready?')
                ->phase('show-1', 'Verification: Files created: {list}, Files modified: {list}, Requirements met: {yes|partial|blocked}, Issues: {list_or_none}')
                ->phase('ask-1', 'Ask: "Status: {status}. Decision? (complete/continue/address-gaps/cancel)"');

        $this->guideline('phase4-store-meta')
            ->text('Goal: Store meta insights (Brain direct storage)')
            ->example()
                ->phase('action-1', 'mcp__vector-memory__store_memory({content: "LFC execution pattern: Task {summary}, Orchestration {method}, Agents used {list}, Success patterns {outcomes}, Lessons {learnings}", category: "learning", tags: ["lfc-execution", "orchestration-pattern", "meta-insight"]})')
                ->phase('ask-1', 'Ask: "Memory stored. Close Issue? (yes/just-comment/keep-open)"');

        $this->guideline('phase4-update-issue')
            ->text('Goal: Update Issue')
            ->example()
                ->phase('action-1', 'Task(@agent-pm-master, "Close/Update Issue {$ARGUMENTS}. Summary: {deliverables}, Memory #{id}, agents {list}. State: {closed|open}. EXPLICITLY use Skill(quality-gate-checker) for validation.")')
                ->phase('present-1', 'Present: "Issue {status}. Complete!"');

        // Quality Gates
        $this->guideline('quality-gates')
            ->text('Quality validation checkpoints')
            ->example('pm-master first (context before search)')
            ->example('Zero-based identifiers accepted (Task-0.0 valid)')
            ->example('EXPLICITLY use mcp__sequential-thinking__sequentialthinking 4× phases')
            ->example('EXPLICITLY delegate agents to use Skills from personality banks')
            ->example('Silent research (STEPS 1-7)')
            ->example('User dialog: CODE only (8, 10, 11)')
            ->example('Sequential execution (NOT parallel)');

        // Directive
        $this->guideline('directive')
            ->text('Context first. Think silently. Show plan. Confirm code. Execute. Verify. Close. EXPLICITLY use Skills. Delegate to agents. Ask about CODE not workflow.');
    }
}
