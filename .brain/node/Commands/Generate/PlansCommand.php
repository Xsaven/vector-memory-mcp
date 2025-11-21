<?php

declare(strict_types=1);

namespace BrainNode\Commands\Generate;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;

#[Meta('id', 'generate:plans')]
#[Meta('description', 'Generate Implementation Plan layer for PM hierarchy')]
#[Purpose('Iterative generation of implementation plans (L4) via deep multi-agent research per plan.')]
class PlansCommand extends CommandArchetype
{
    protected function handle(): void
    {
        // Role
        $this->guideline('role')
            ->text('Dynamic agent discovery via Bash(brain list:masters). Read parent Subtask Issue via @agent-pm-master. Iterative loop (ONE plan at a time). Sequential multi-agent research chain per plan. Brain synthesis with rich context (Memory IDs, .docs/ refs). Delegate execution to @agent-pm-master.');

        // Workflow Thinking
        $this->guideline('workflow-thinking')
            ->text('Iterative generation: ONE plan at a time with deep sequential research. Each plan gets full multi-agent analysis before creation. Skills MUST be invoked explicitly at key validation points.');

        // Iron Rules
        $this->rule('read-agents-first')->critical()
            ->text('Run Bash(brain list:masters) first (dynamic discovery)')
            ->why('Dynamic agent discovery ensures flexibility')
            ->onViolation('Missing agent discovery compromises workflow');

        $this->rule('read-parent-subtask')->critical()
            ->text('Read parent Subtask via pm-master')
            ->why('Context from parent subtask essential for plan generation')
            ->onViolation('Cannot generate plans without parent context');

        $this->rule('iterate-one-plan')->critical()
            ->text('Iterate ONE plan at a time (no batch)')
            ->why('Sequential processing ensures quality per plan')
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
            ->why('Iteration ensures individual attention per plan')
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
            ->text('IF complex technical implementation → generate plan. IF high-risk or mission-critical → plan reduces failure. IF straightforward CRUD → skip (overhead not justified). IF $ARGUMENTS provided → subtask/topic filter. IF $ARGUMENTS empty → all complex subtasks.');

        // Workflow Step 0
        $this->guideline('workflow-step0')
            ->text('STEP 0 - Preparation')
            ->example()
                ->phase('action-1', 'Bash(\'brain list:masters\') → discover available agents');

        // Workflow Step 1
        $this->guideline('workflow-step1')
            ->text('STEP 1 - Get Parent Context (via pm-master)')
            ->example()
                ->phase('task-1', 'Task(@agent-pm-master, "Analyze parent Subtask context for Plan generation. EXPLICITLY use Skill(context-analyzer) to validate readiness before analysis. Read & extract: Subtask Issue, ALL comments, linked Issues, sub-issues. Analyze: scope, requirements, decisions, related context, current status. Use Skill(quality-gate-checker) to validate extracted context completeness. Return structured summary with synthesis.")');

        // Workflow Step 2
        $this->guideline('workflow-step2')
            ->text('STEP 2 - Determine Plans')
            ->example()
                ->phase('thinking-1', 'Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent Subtask context. Goal: Determine plan structure (architecture, deps, testing, risks, rollout). Output: Plan component scopes');

        // Workflow Loop
        $this->guideline('workflow-loop')
            ->text('FOR EACH plan_component in list');

        // Workflow Step 3
        $this->guideline('workflow-step3')
            ->text('STEP 3 - Deep Research (Sequential Chain)')
            ->example()
                ->phase('3.1', 'Check Existing Work: Task(@agent-vector-master, "Search for existing work on \'{plan_component} {stack} implementation plans\'. Category: \'code-solution, architecture, bug-fix\'. Limit: 5. Use Skill(brain-philosophy) for cognitive framework during search. Return: Memory IDs + insights + reuse recommendations")')
                ->phase('3.2', 'Initial Code Scan (if code-analysis agents exist): FOR EACH code agent: Task(@agent-{code}, "Scan codebase for {plan_component}. EXPLICITLY use Skill(architecture-introspector) for system awareness. Questions: existing components? implementation gaps? reuse/refactor/new assessment? Return: List + gaps + assessment")')
                ->phase('3.3', 'Best Practices: Task(@agent-web-research-master, "Research {year} implementation planning best practices for {stack}. Focus: {plan_component} within {subtask} objectives. Use Skill(quality-gate-checker) to validate research quality. Return: Architecture patterns, risk mitigation")')
                ->phase('3.4', 'Initial Strategy: Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent + existing work + code scan + web research. Goal: Initial plan component strategy. Output: Architecture decisions, dependencies')
                ->phase('3.5', 'Documentation: Task(@agent-documents-master, "Find .docs/ for {plan_component}. Use Skill(context-analyzer) to ensure document relevance. Return: File paths + architecture insights")');

        // Workflow Step 4
        $this->guideline('workflow-step4')
            ->text('STEP 4 - Synthesis (Brain)')
            ->example()
                ->phase('action-1', 'Combine: Subtask context + existing work + code scan + web practices + sequential-thinking + docs')
                ->phase('action-2', 'Use Skill(quality-gate-checker) for synthesis validation.');

        // Workflow Step 5
        $this->guideline('workflow-step5')
            ->text('STEP 5 - Code Alignment (if code-analysis agents exist)')
            ->example()
                ->phase('action-1', 'FOR EACH code agent: Task(@agent-{code}, "Verify synthesized strategy for {plan_component}. EXPLICITLY use Skill(architecture-introspector) for dependency validation. Questions: reuse potential? refactoring needs? new development areas? conflicts? Return: Recommendations + requirements + considerations")');

        // Workflow Step 6 - DETAILED SPECIFICATION STRUCTURE
        $this->guideline('workflow-step6')
            ->text('STEP 6 - Final Specification (Brain)')
            ->example()
                ->phase('spec-1', 'Create specification (NO water): Objective: {concise_plan_component}')
                ->phase('spec-2', 'Context: Parent Subtask {phase}.{task}.{subtask} - {scope} + {constraints}')
                ->phase('spec-3', 'Existing Status: Already Exists / Needs Refactoring / Build From Scratch')
                ->phase('spec-4', 'Previous Work: Memory #{id} + reuse recommendations + avoid approaches')
                ->phase('spec-5', 'Implementation Guidance: {textual_no_code} + Architecture + Dependencies + Testing + Risks')
                ->phase('spec-6', 'Research References: Memory #{IDs}, .docs/{paths}, best practices')
                ->phase('spec-7', 'Breakdown Recommendations: single_action/complex → execute directly or subdivide into Steps')
                ->phase('validation-1', 'Validate specification with Skill(quality-gate-checker).');

        // Workflow Step 7
        $this->guideline('workflow-step7')
            ->text('STEP 7 - Create Subissue')
            ->example()
                ->phase('task-1', 'Task(@agent-pm-master, "Create Plan {phase}.{task}.{subtask}.{N} Subissue. Title: Plan {phase}.{task}.{subtask}.{N} - {name}. Body: {synthesized_specification}. Labels: plan, {component_tags}. Parent: Subtask {phase}.{task}.{subtask} Issue. EXPLICITLY use Skill(quality-gate-checker) before creation.")');

        // Workflow Step 8
        $this->guideline('workflow-step8')
            ->text('STEP 8 - Continue to Next Plan Component')
            ->example()
                ->phase('action-1', 'Continue iteration loop');

        // Workflow Step 9
        $this->guideline('workflow-step9')
            ->text('STEP 9 - Summary')
            ->example()
                ->phase('action-1', 'Report created plan with approval workflow and GitHub links')
                ->phase('validation-1', 'Use Skill(quality-gate-checker) for final validation.');

        // Numbering Rules
        $this->guideline('numbering-rules')
            ->text('Format: "Plan {phase}.{task}.{subtask}.{N} - {name}". N: {phase}.{task}.{subtask}.0, .1, .2... (zero-based within subtask). Title: Max 6 words.')
            ->example('Plan 0.1.2.0 - Define Database Schema');

        // Quality Gates
        $this->guideline('quality-gates')
            ->text('Quality validation checkpoints')
            ->example('brain list:masters executed')
            ->example('Parent Subtask read via pm-master')
            ->example('ONE plan at a time')
            ->example('Sequential research chain')
            ->example('Code analysis included if available')
            ->example('Memory IDs + .docs/ refs present')
            ->example('Skills explicitly invoked at validation points')
            ->example('Architecture/deps/testing/risks covered')
            ->example('No code blocks');

        // Directive
        $this->guideline('directive')
            ->text('Discover. Read parent. Iterate. Research. Synthesize. Create. Repeat. EXPLICITLY use Skills.');
    }
}
