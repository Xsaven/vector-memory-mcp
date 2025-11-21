<?php

declare(strict_types=1);

namespace BrainNode\Commands\Generate;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;

#[Meta('id', 'generate:phases')]
#[Meta('description', 'Generate Phase layer for PM hierarchy')]
#[Purpose('Iterative generation of strategic project phases (L1) via deep multi-agent research per phase.')]
class PhasesCommand extends CommandArchetype
{
    protected function handle(): void
    {
        // Role
        $this->guideline('role')
            ->text('Dynamic agent discovery via Bash(brain list:masters ). Iterative loop (ONE phase at a time). Sequential multi-agent research chain per phase. Brain synthesis with rich context (Memory IDs, .docs/ refs). Delegate execution to @agent-pm-master.');

        // Workflow Thinking
        $this->guideline('workflow-thinking')
            ->text('Iterative generation: ONE phase at a time with deep sequential research. Each phase gets full multi-agent analysis before creation. Skills MUST be invoked explicitly at key validation points.');

        // Iron Rules
        $this->rule('read-agents-first')->critical()
            ->text('Run Bash(brain list:masters ) first (dynamic discovery)')
            ->why('Dynamic agent discovery ensures flexibility')
            ->onViolation('Missing agent discovery compromises workflow');

        $this->rule('iterate-one-phase')->critical()
            ->text('Iterate ONE phase at a time (no batch)')
            ->why('Sequential processing ensures quality per phase')
            ->onViolation('Batch processing violates iteration principle');

        $this->rule('sequential-research')->critical()
            ->text('Sequential research chain (web → sequential-thinking → vector → docs)')
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
            ->why('Iteration ensures individual attention per phase')
            ->onViolation('Use iterative approach');

        $this->rule('no-hardcoded-agents')->medium()
            ->text('No hardcoded agents (except pm-master)')
            ->why('Dynamic discovery maintains flexibility')
            ->onViolation('Use Bash(brain list:masters ) for discovery');

        $this->rule('delegate-github-ops')->high()
            ->text('No direct GitHub ops (delegate)')
            ->why('Separation of concerns via pm-master')
            ->onViolation('Delegate to pm-master');

        // When to Use - INCLUDES $ARGUMENTS
        $this->guideline('when-to-use')
            ->text('IF user requests phase generation OR project planning → initiate iterative workflow. IF $ARGUMENTS provided → phase/topic filter. IF $ARGUMENTS empty → generate all phases.');

        // Workflow
        $this->guideline('workflow-step0')
            ->text('STEP 0 - Preparation')
            ->example()
                ->phase('action-1', 'Bash(\'brain list:masters \') → discover available agents');

        $this->guideline('workflow-step1')
            ->text('STEP 1 - Get Parent Context (via pm-master)')
            ->example()
                ->phase('task-1', 'Task(@agent-pm-master, "Analyze project context for Phase generation. EXPLICITLY use Skill(context-analyzer) to validate readiness before analysis. Read & extract: README, .docs/, GitHub Issues, ALL comments on planning Issues. Analyze: scope, stack, requirements, constraints, decisions, current status. Use Skill(quality-gate-checker) to validate extracted context completeness. Return structured summary with concise synthesis.")');

        $this->guideline('workflow-step2')
            ->text('STEP 2 - Determine Phases')
            ->example()
                ->phase('thinking-1', 'Use mcp__sequential-thinking__sequentialthinking tool: Analyze: project context from pm-master. Goal: Determine phase count (3-7) and initial names. Output: List of phase scopes');

        $this->guideline('workflow-loop')
            ->text('FOR EACH phase in list');

        $this->guideline('workflow-step3')
            ->text('STEP 3 - Deep Research (Sequential Chain)')
            ->example()
                ->phase('3.1', 'Check Existing Work: Task(@agent-vector-master, "Search for existing related work on \'{phase_scope} {stack} strategic planning\'. Category: \'code-solution, architecture, bug-fix\'. Limit: 5. Use Skill(brain-philosophy) for cognitive framework during search. Return: Memory IDs + insights + reuse recommendations")')
                ->phase('3.2', 'Initial Code Scan (if code-analysis agents exist): FOR EACH code agent: Task(@agent-{code}, "Scan codebase for {phase_scope}. EXPLICITLY use Skill(architecture-introspector) for system awareness. Questions: existing components? implementation gaps? reuse/refactor/new assessment? Return: List + gaps + assessment")')
                ->phase('3.3', 'Best Practices: Task(@agent-web-research-master, "Research {year} phase breakdown best practices for {stack}. Focus: {phase_scope}. Use Skill(quality-gate-checker) to validate research quality. Return: Strategic patterns, anti-patterns")')
                ->phase('3.4', 'Initial Strategy: Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent context + existing work + code scan + web research. Goal: Initial phase strategy. Output: Objectives, scope boundaries')
                ->phase('3.5', 'Documentation: Task(@agent-documents-master, "Find relevant .docs/ for {phase_scope}. Use Skill(context-analyzer) to ensure document relevance. Return: File paths + key insights")');

        $this->guideline('workflow-step4')
            ->text('STEP 4 - Synthesis (Brain)')
            ->example()
                ->phase('action-1', 'Combine: existing work + code scan + web practices + sequential-thinking + docs')
                ->phase('action-2', 'Use Skill(quality-gate-checker) for synthesis validation.');

        $this->guideline('workflow-step5')
            ->text('STEP 5 - Code Alignment (if code-analysis agents exist)')
            ->example()
                ->phase('action-1', 'FOR EACH code agent: Task(@agent-{code}, "Verify synthesized strategy for {phase_scope}. EXPLICITLY use Skill(architecture-introspector) for dependency validation. Questions: reuse potential? refactoring needs? new development areas? conflicts? Return: Recommendations + requirements + considerations")');

        $this->guideline('workflow-step6')
            ->text('STEP 6 - Final Specification (Brain)')
            ->example()
                ->phase('spec-1', 'Create specification (NO water): Objective: {concise_no_fluff}')
                ->phase('spec-2', 'Context: {tech_stack} {project_scope}')
                ->phase('spec-3', 'Existing Status: Already Exists / Needs Refactoring / Build From Scratch')
                ->phase('spec-4', 'Previous Work: Memory #{id} + reuse recommendations + avoid approaches')
                ->phase('spec-5', 'Implementation Guidance: {textual_no_code}')
                ->phase('spec-6', 'Research References: Memory #{IDs}, .docs/{paths}, best practices')
                ->phase('spec-7', 'Breakdown Recommendations: {task_subdivision_strategy}')
                ->phase('validation-1', 'Validate specification with Skill(quality-gate-checker).');

        $this->guideline('workflow-step7')
            ->text('STEP 7 - Create Issue')
            ->example()
                ->phase('task-1', 'Task(@agent-pm-master, "Create Phase {N} Issue. Title: Phase {N} - {name}. Body: {synthesized_specification}. Labels: phase, {stack_tags}. EXPLICITLY use Skill(quality-gate-checker) before creation.")');

        $this->guideline('workflow-step8')
            ->text('STEP 8 - Continue to Next Phase')
            ->example()
                ->phase('action-1', 'Continue iteration loop');

        $this->guideline('workflow-step9')
            ->text('STEP 9 - Summary')
            ->example()
                ->phase('action-1', 'Report created phases with GitHub links')
                ->phase('validation-1', 'Use Skill(quality-gate-checker) for final validation.');

        // Numbering Rules
        $this->guideline('numbering-rules')
            ->text('Format: "Phase {N} - {name}". N: 0, 1, 2... (zero-based). Title: Max 6 words.')
            ->example('Phase 0 - Project Foundation Setup');

        // Quality Gates
        $this->guideline('quality-gates')
            ->text('Quality validation checkpoints')
            ->example('brain list:masters  executed')
            ->example('ONE phase at a time')
            ->example('Sequential research chain')
            ->example('Memory IDs + .docs/ refs present')
            ->example('Skills explicitly invoked at validation points')
            ->example('No code blocks, textual guidance only');

        // Directive
        $this->guideline('directive')
            ->text('Discover. Iterate. Research. Synthesize. Create. Repeat. EXPLICITLY use Skills.');
    }
}
