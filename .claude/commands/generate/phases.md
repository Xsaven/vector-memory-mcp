---
name: generate:phases
description: "Generate Phase layer for PM hierarchy"
---

<command>
<meta>
<id>generate:phases</id>
<description>Generate Phase layer for PM hierarchy</description>
</meta>
<purpose>Iterative generation of strategic project phases (L1) via deep multi-agent research per phase.</purpose>
<guidelines>
<guideline id="role">
<text>Dynamic agent discovery via Bash(brain list:masters ). Iterative loop (ONE phase at a time). Sequential multi-agent research chain per phase. Brain synthesis with rich context (Memory IDs, .docs/ refs). Delegate execution to @agent-pm-master.</text>
</guideline>
<guideline id="workflow-thinking">
<text>Iterative generation: ONE phase at a time with deep sequential research. Each phase gets full multi-agent analysis before creation. Skills MUST be invoked explicitly at key validation points.</text>
</guideline>
<guideline id="when-to-use">
<text>IF user requests phase generation OR project planning → initiate iterative workflow. IF $ARGUMENTS provided → phase/topic filter. IF $ARGUMENTS empty → generate all phases.</text>
</guideline>
<guideline id="workflow-step0">
<text>STEP 0 - Preparation</text>
<example>
<phase name="action-1">Bash('brain list:masters ') → discover available agents</phase>
</example>
</guideline>
<guideline id="workflow-step1">
<text>STEP 1 - Get Parent Context (via pm-master)</text>
<example>
<phase name="task-1">Task(@agent-pm-master, "Analyze project context for Phase generation. EXPLICITLY use Skill(context-analyzer) to validate readiness before analysis. Read & extract: README, .docs/, GitHub Issues, ALL comments on planning Issues. Analyze: scope, stack, requirements, constraints, decisions, current status. Use Skill(quality-gate-checker) to validate extracted context completeness. Return structured summary with concise synthesis.")</phase>
</example>
</guideline>
<guideline id="workflow-step2">
<text>STEP 2 - Determine Phases</text>
<example>
<phase name="thinking-1">Use mcp__sequential-thinking__sequentialthinking tool: Analyze: project context from pm-master. Goal: Determine phase count (3-7) and initial names. Output: List of phase scopes</phase>
</example>
</guideline>
<guideline id="workflow-loop">
<text>FOR EACH phase in list</text>
</guideline>
<guideline id="workflow-step3">
<text>STEP 3 - Deep Research (Sequential Chain)</text>
<example>
<phase name="3.1">Check Existing Work: Task(@agent-vector-master, "Search for existing related work on '{phase_scope} {stack} strategic planning'. Category: 'code-solution, architecture, bug-fix'. Limit: 5. Use Skill(brain-philosophy) for cognitive framework during search. Return: Memory IDs + insights + reuse recommendations")</phase>
<phase name="3.2">Initial Code Scan (if code-analysis agents exist): FOR EACH code agent: Task(@agent-{code}, "Scan codebase for {phase_scope}. EXPLICITLY use Skill(architecture-introspector) for system awareness. Questions: existing components? implementation gaps? reuse/refactor/new assessment? Return: List + gaps + assessment")</phase>
<phase name="3.3">Best Practices: Task(@agent-web-research-master, "Research {year} phase breakdown best practices for {stack}. Focus: {phase_scope}. Use Skill(quality-gate-checker) to validate research quality. Return: Strategic patterns, anti-patterns")</phase>
<phase name="3.4">Initial Strategy: Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent context + existing work + code scan + web research. Goal: Initial phase strategy. Output: Objectives, scope boundaries</phase>
<phase name="3.5">Documentation: Task(@agent-documents-master, "Find relevant .docs/ for {phase_scope}. Use Skill(context-analyzer) to ensure document relevance. Return: File paths + key insights")</phase>
</example>
</guideline>
<guideline id="workflow-step4">
<text>STEP 4 - Synthesis (Brain)</text>
<example>
<phase name="action-1">Combine: existing work + code scan + web practices + sequential-thinking + docs</phase>
<phase name="action-2">Use Skill(quality-gate-checker) for synthesis validation.</phase>
</example>
</guideline>
<guideline id="workflow-step5">
<text>STEP 5 - Code Alignment (if code-analysis agents exist)</text>
<example>
<phase name="action-1">FOR EACH code agent: Task(@agent-{code}, "Verify synthesized strategy for {phase_scope}. EXPLICITLY use Skill(architecture-introspector) for dependency validation. Questions: reuse potential? refactoring needs? new development areas? conflicts? Return: Recommendations + requirements + considerations")</phase>
</example>
</guideline>
<guideline id="workflow-step6">
<text>STEP 6 - Final Specification (Brain)</text>
<example>
<phase name="spec-1">Create specification (NO water): Objective: {concise_no_fluff}</phase>
<phase name="spec-2">Context: {tech_stack} {project_scope}</phase>
<phase name="spec-3">Existing Status: Already Exists / Needs Refactoring / Build From Scratch</phase>
<phase name="spec-4">Previous Work: Memory #{id} + reuse recommendations + avoid approaches</phase>
<phase name="spec-5">Implementation Guidance: {textual_no_code}</phase>
<phase name="spec-6">Research References: Memory #{IDs}, .docs/{paths}, best practices</phase>
<phase name="spec-7">Breakdown Recommendations: {task_subdivision_strategy}</phase>
<phase name="validation-1">Validate specification with Skill(quality-gate-checker).</phase>
</example>
</guideline>
<guideline id="workflow-step7">
<text>STEP 7 - Create Issue</text>
<example>
<phase name="task-1">Task(@agent-pm-master, "Create Phase {N} Issue. Title: Phase {N} - {name}. Body: {synthesized_specification}. Labels: phase, {stack_tags}. EXPLICITLY use Skill(quality-gate-checker) before creation.")</phase>
</example>
</guideline>
<guideline id="workflow-step8">
<text>STEP 8 - Continue to Next Phase</text>
<example>
<phase name="action-1">Continue iteration loop</phase>
</example>
</guideline>
<guideline id="workflow-step9">
<text>STEP 9 - Summary</text>
<example>
<phase name="action-1">Report created phases with GitHub links</phase>
<phase name="validation-1">Use Skill(quality-gate-checker) for final validation.</phase>
</example>
</guideline>
<guideline id="numbering-rules">
<text>Format: "Phase {N} - {name}". N: 0, 1, 2... (zero-based). Title: Max 6 words.</text>
<example>Phase 0 - Project Foundation Setup</example>
</guideline>
<guideline id="quality-gates">
<text>Quality validation checkpoints</text>
<example>brain list:masters  executed</example>
<example>ONE phase at a time</example>
<example>Sequential research chain</example>
<example>Memory IDs + .docs/ refs present</example>
<example>Skills explicitly invoked at validation points</example>
<example>No code blocks, textual guidance only</example>
</guideline>
<guideline id="directive">
<text>Discover. Iterate. Research. Synthesize. Create. Repeat. EXPLICITLY use Skills.</text>
</guideline>
</guidelines>
<iron_rules>
<rule id="read-agents-first" severity="critical">
<text>Run Bash(brain list:masters ) first (dynamic discovery)</text>
<why>Dynamic agent discovery ensures flexibility</why>
<on_violation>Missing agent discovery compromises workflow</on_violation>
</rule>
<rule id="iterate-one-phase" severity="critical">
<text>Iterate ONE phase at a time (no batch)</text>
<why>Sequential processing ensures quality per phase</why>
<on_violation>Batch processing violates iteration principle</on_violation>
</rule>
<rule id="sequential-research" severity="critical">
<text>Sequential research chain (web → sequential-thinking → vector → docs)</text>
<why>Structured research ensures comprehensive analysis</why>
<on_violation>Missing research steps compromise quality</on_violation>
</rule>
<rule id="synthesize-with-context" severity="high">
<text>Synthesize with Memory IDs + .docs/ links</text>
<why>Rich context enables better decision-making</why>
<on_violation>Incomplete context reduces quality</on_violation>
</rule>
<rule id="explicit-skills" severity="high">
<text>EXPLICITLY instruct agents to use Skills</text>
<why>Skills provide specialized capabilities</why>
<on_violation>Missing skill invocation reduces effectiveness</on_violation>
</rule>
<rule id="no-batch-processing" severity="critical">
<text>No batch processing (violates iteration)</text>
<why>Iteration ensures individual attention per phase</why>
<on_violation>Use iterative approach</on_violation>
</rule>
<rule id="no-hardcoded-agents" severity="medium">
<text>No hardcoded agents (except pm-master)</text>
<why>Dynamic discovery maintains flexibility</why>
<on_violation>Use Bash(brain list:masters ) for discovery</on_violation>
</rule>
<rule id="delegate-github-ops" severity="high">
<text>No direct GitHub ops (delegate)</text>
<why>Separation of concerns via pm-master</why>
<on_violation>Delegate to pm-master</on_violation>
</rule>
</iron_rules>
</command>