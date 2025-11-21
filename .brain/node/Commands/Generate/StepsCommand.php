<?php

declare(strict_types=1);

namespace BrainNode\Commands\Generate;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;

#[Meta('id', 'generate:steps')]
#[Meta('description', 'Generate Verification Step layer for PM hierarchy')]
#[Purpose('Iterative generation of atomic verification steps (L5) via deep multi-agent research per step.')]
class StepsCommand extends CommandArchetype
{
    protected function handle(): void
    {
        // Role
        $this->guideline('role')
            ->text('Dynamic agent discovery via Bash(brain list:masters). Read parent Plan Issue via @agent-pm-master. Iterative loop (ONE step at a time). Sequential multi-agent research chain per step. Brain synthesis with rich context (Memory IDs, .docs/ refs). Delegate execution to @agent-pm-master.');

        // Workflow Thinking
        $this->guideline('workflow-thinking')
            ->text('Iterative generation: ONE step at a time with deep sequential research. Each step gets full multi-agent analysis before creation. Skills MUST be invoked explicitly at key validation points.');

        // Iron Rules
        $this->rule('read-agents-first')->critical()
            ->text('Run Bash(brain list:masters) first (dynamic discovery)')
            ->why('Dynamic agent discovery ensures flexibility')
            ->onViolation('Missing agent discovery compromises workflow');

        $this->rule('read-parent-plan')->critical()
            ->text('Read parent Plan via pm-master')
            ->why('Context from parent plan essential for step generation')
            ->onViolation('Cannot generate steps without parent context');

        $this->rule('iterate-one-step')->critical()
            ->text('Iterate ONE step at a time (no batch)')
            ->why('Sequential processing ensures quality per step')
            ->onViolation('Batch processing violates iteration principle');

        $this->rule('sequential-research')->critical()
            ->text('Sequential research chain (web → sequential-thinking → code → vector → docs)')
            ->why('Structured research ensures comprehensive analysis')
            ->onViolation('Missing research steps compromise quality');

        $this->rule('synthesize-with-context')->high()
            ->text('Synthesize with Memory IDs + .docs/ links')
            ->why('Rich context enables better decision-making')
            ->onViolation('Incomplete context reduces quality');

        $this->rule('explicit-skills')->high()
            ->text('EXPLICITLY instruct agents to use Skills')
            ->why('Skills provide specialized capabilities')
            ->onViolation('Missing skill invocation reduces effectiveness');

        $this->rule('no-batch-processing')->critical()
            ->text('No batch processing (violates iteration)')
            ->why('Iteration ensures individual attention per step')
            ->onViolation('Use iterative approach');

        $this->rule('no-hardcoded-agents')->medium()
            ->text('No hardcoded agents (except pm-master)')
            ->why('Dynamic discovery maintains flexibility')
            ->onViolation('Use Bash(brain list:masters) for discovery');

        $this->rule('no-direct-github-ops')->high()
            ->text('No direct GitHub ops (delegate)')
            ->why('Separation of concerns via pm-master')
            ->onViolation('Delegate to pm-master');

        // When to Use - INCLUDES $ARGUMENTS
        $this->guideline('when-to-use')
            ->text('IF junior developer needs guidance → generate steps. IF complex unfamiliar implementation → steps transfer knowledge. IF critical path requires precision → steps reduce ambiguity. IF experienced developer + familiar tech → skip (overhead wastes time). IF $ARGUMENTS provided → plan/topic filter. IF $ARGUMENTS empty → all complex plans.');

        // Workflow Step 0
        $this->guideline('workflow-step0')
            ->text('STEP 0 - Preparation')
            ->example()
                ->phase('action-1', 'Bash(\'brain list:masters\') → discover available agents');

        // Workflow Step 1
        $this->guideline('workflow-step1')
            ->text('STEP 1 - Get Parent Context (via pm-master)')
            ->example()
                ->phase('task-1', 'Task(@agent-pm-master, "Analyze parent Plan context for Step generation. EXPLICITLY use Skill(context-analyzer) to validate readiness before analysis. Read & extract: Plan Issue, ALL comments, linked Issues, sub-issues. Analyze: scope, requirements, decisions, related context, current status. Use Skill(quality-gate-checker) to validate extracted context completeness. Return structured summary with synthesis.")');

        // Workflow Step 2
        $this->guideline('workflow-step2')
            ->text('STEP 2 - Determine Steps')
            ->example()
                ->phase('thinking-1', 'Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent Plan context. Goal: Determine step count (10-50) and names. Output: List of atomic step scopes (30min-2h each)');

        // Workflow Loop
        $this->guideline('workflow-loop')
            ->text('FOR EACH step in list');

        // Workflow Step 3
        $this->guideline('workflow-step3')
            ->text('STEP 3 - Deep Research (Sequential Chain)')
            ->example()
                ->phase('3.1', 'Check Existing Work: Task(@agent-vector-master, "Search for existing work on \'{step_scope} {stack} verification steps\'. Category: \'code-solution, architecture, bug-fix\'. Limit: 5. Use Skill(brain-philosophy) for cognitive framework during search. Return: Memory IDs + insights + reuse recommendations")')
                ->phase('3.2', 'Initial Code Scan (if code-analysis agents exist): FOR EACH code agent: Task(@agent-{code}, "Scan codebase for {step_scope}. EXPLICITLY use Skill(architecture-introspector) for system awareness. Questions: existing components? implementation gaps? reuse/refactor/new assessment? Return: List + gaps + assessment")')
                ->phase('3.3', 'Best Practices: Task(@agent-web-research-master, "Research {year} atomic step best practices for {stack}. Focus: {step_scope} within {plan} objectives. Use Skill(quality-gate-checker) to validate research quality. Return: Copy-paste patterns, verification criteria")')
                ->phase('3.4', 'Initial Strategy: Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent + existing work + code scan + web research. Goal: Initial step definition. Output: Atomic action, verification')
                ->phase('3.5', 'Documentation: Task(@agent-documents-master, "Find .docs/ for {step_scope}. Use Skill(context-analyzer) to ensure document relevance. Return: File paths + command patterns")');

        // Workflow Step 4
        $this->guideline('workflow-step4')
            ->text('STEP 4 - Synthesis (Brain)')
            ->example()
                ->phase('action-1', 'Combine: Plan context + existing work + code scan + web practices + sequential-thinking + docs')
                ->phase('action-2', 'Use Skill(quality-gate-checker) for synthesis validation.');

        // Workflow Step 5
        $this->guideline('workflow-step5')
            ->text('STEP 5 - Code Alignment (if code-analysis agents exist)')
            ->example()
                ->phase('action-1', 'FOR EACH code agent: Task(@agent-{code}, "Verify synthesized strategy for {step_scope}. EXPLICITLY use Skill(architecture-introspector) for dependency validation. Questions: reuse potential? refactoring needs? new development areas? conflicts? Return: Recommendations + requirements + considerations")');

        // Workflow Step 6 - DETAILED SPECIFICATION STRUCTURE
        $this->guideline('workflow-step6')
            ->text('STEP 6 - Final Specification (Brain)')
            ->example()
                ->phase('spec-1', 'Create specification (NO water): Objective: {concise_atomic_action_4_words_max}')
                ->phase('spec-2', 'Context: Parent Plan {phase}.{task}.{subtask}.{plan} - {scope}')
                ->phase('spec-3', 'Existing Status: Already Exists / Needs Refactoring / Build From Scratch')
                ->phase('spec-4', 'Previous Work: Memory #{id} + reuse recommendations + avoid approaches')
                ->phase('spec-5', 'Implementation Guidance: {textual_no_code_blocks} + Commands (reference patterns) + Verification')
                ->phase('spec-6', 'Research References: Memory #{IDs}, .docs/{paths}, practices')
                ->phase('spec-7', 'Breakdown Recommendations: N/A (deepest layer - execute directly)')
                ->phase('validation-1', 'Validate specification with Skill(quality-gate-checker).');

        // Workflow Step 7
        $this->guideline('workflow-step7')
            ->text('STEP 7 - Create Subissue')
            ->example()
                ->phase('task-1', 'Task(@agent-pm-master, "Create Step {phase}.{task}.{subtask}.{plan}.{N} Subissue. Title: Step {phase}.{task}.{subtask}.{plan}.{N} - {name} (MAX 4 WORDS). Body: {synthesized_specification} (MAX 30 sentences). Labels: step, {action_tags}. Parent: Plan {phase}.{task}.{subtask}.{plan} Issue. EXPLICITLY use Skill(quality-gate-checker) before creation.")');

        // Workflow Step 8
        $this->guideline('workflow-step8')
            ->text('STEP 8 - Continue to Next Step')
            ->example()
                ->phase('action-1', 'Continue iteration loop');

        // Workflow Step 9
        $this->guideline('workflow-step9')
            ->text('STEP 9 - Summary')
            ->example()
                ->phase('action-1', 'Report created steps with execution workflow and GitHub links')
                ->phase('validation-1', 'Use Skill(quality-gate-checker) for final validation.');

        // Numbering Rules
        $this->guideline('numbering-rules')
            ->text('Format: "Step {phase}.{task}.{subtask}.{plan}.{N} - {name}". N: {phase}.{task}.{subtask}.{plan}.0, .1, .2... (zero-based within plan). Title: Max 4 words (STRICTER than other layers). Description: Max 30 sentences (vs 50 for other layers).')
            ->example('Step 0.1.2.3.0 - Run Migration Command');

        // Quality Gates
        $this->guideline('quality-gates')
            ->text('Quality validation checkpoints')
            ->example('brain list:masters executed')
            ->example('Parent Plan read via pm-master')
            ->example('ONE step at a time')
            ->example('Sequential research chain')
            ->example('Code analysis included if available')
            ->example('Memory IDs + .docs/ refs present')
            ->example('Skills explicitly invoked at validation points')
            ->example('Title MAX 4 words')
            ->example('Description MAX 30 sentences')
            ->example('30min-2h atomic sizing')
            ->example('No code blocks');

        // Directive
        $this->guideline('directive')
            ->text('Discover. Read parent. Iterate. Research. Synthesize. Create. Repeat. EXPLICITLY use Skills.');
    }
}
