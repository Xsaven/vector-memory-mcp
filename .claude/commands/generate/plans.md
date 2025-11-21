---
name: generate:plans
description: "Generate Implementation Plan layer for PM hierarchy"
---

<command>
<meta>
<id>generate:plans</id>
<description>Generate Implementation Plan layer for PM hierarchy</description>
</meta>
<purpose>Iterative generation of implementation plans (L4) via deep multi-agent research per plan.</purpose>
<guidelines>
<guideline id="role">
<text>Dynamic agent discovery via Bash(brain list:masters). Read parent Subtask Issue via @agent-pm-master. Iterative loop (ONE plan at a time). Sequential multi-agent research chain per plan. Brain synthesis with rich context (Memory IDs, .docs/ refs). Delegate execution to @agent-pm-master.</text>
</guideline>
<guideline id="workflow-thinking">
<text>Iterative generation: ONE plan at a time with deep sequential research. Each plan gets full multi-agent analysis before creation. Skills MUST be invoked explicitly at key validation points.</text>
</guideline>
<guideline id="when-to-use">
<text>IF complex technical implementation → generate plan. IF high-risk or mission-critical → plan reduces failure. IF straightforward CRUD → skip (overhead not justified). IF $ARGUMENTS provided → subtask/topic filter. IF $ARGUMENTS empty → all complex subtasks.</text>
</guideline>
<guideline id="workflow-step0">
<text>STEP 0 - Preparation</text>
<example>
<phase name="action-1">Bash('brain list:masters') → discover available agents</phase>
</example>
</guideline>
<guideline id="workflow-step1">
<text>STEP 1 - Get Parent Context (via pm-master)</text>
<example>
<phase name="task-1">Task(@agent-pm-master, "Analyze parent Subtask context for Plan generation. EXPLICITLY use Skill(context-analyzer) to validate readiness before analysis. Read & extract: Subtask Issue, ALL comments, linked Issues, sub-issues. Analyze: scope, requirements, decisions, related context, current status. Use Skill(quality-gate-checker) to validate extracted context completeness. Return structured summary with synthesis.")</phase>
</example>
</guideline>
<guideline id="workflow-step2">
<text>STEP 2 - Determine Plans</text>
<example>
<phase name="thinking-1">Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent Subtask context. Goal: Determine plan structure (architecture, deps, testing, risks, rollout). Output: Plan component scopes</phase>
</example>
</guideline>
<guideline id="workflow-loop">
<text>FOR EACH plan_component in list</text>
</guideline>
<guideline id="workflow-step3">
<text>STEP 3 - Deep Research (Sequential Chain)</text>
<example>
<phase name="3.1">Check Existing Work: Task(@agent-vector-master, "Search for existing work on '{plan_component} {stack} implementation plans'. Category: 'code-solution, architecture, bug-fix'. Limit: 5. Use Skill(brain-philosophy) for cognitive framework during search. Return: Memory IDs + insights + reuse recommendations")</phase>
<phase name="3.2">Initial Code Scan (if code-analysis agents exist): FOR EACH code agent: Task(@agent-{code}, "Scan codebase for {plan_component}. EXPLICITLY use Skill(architecture-introspector) for system awareness. Questions: existing components? implementation gaps? reuse/refactor/new assessment? Return: List + gaps + assessment")</phase>
<phase name="3.3">Best Practices: Task(@agent-web-research-master, "Research {year} implementation planning best practices for {stack}. Focus: {plan_component} within {subtask} objectives. Use Skill(quality-gate-checker) to validate research quality. Return: Architecture patterns, risk mitigation")</phase>
<phase name="3.4">Initial Strategy: Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent + existing work + code scan + web research. Goal: Initial plan component strategy. Output: Architecture decisions, dependencies</phase>
<phase name="3.5">Documentation: Task(@agent-documents-master, "Find .docs/ for {plan_component}. Use Skill(context-analyzer) to ensure document relevance. Return: File paths + architecture insights")</phase>
</example>
</guideline>
<guideline id="workflow-step4">
<text>STEP 4 - Synthesis (Brain)</text>
<example>
<phase name="action-1">Combine: Subtask context + existing work + code scan + web practices + sequential-thinking + docs</phase>
<phase name="action-2">Use Skill(quality-gate-checker) for synthesis validation.</phase>
</example>
</guideline>
<guideline id="workflow-step5">
<text>STEP 5 - Code Alignment (if code-analysis agents exist)</text>
<example>
<phase name="action-1">FOR EACH code agent: Task(@agent-{code}, "Verify synthesized strategy for {plan_component}. EXPLICITLY use Skill(architecture-introspector) for dependency validation. Questions: reuse potential? refactoring needs? new development areas? conflicts? Return: Recommendations + requirements + considerations")</phase>
</example>
</guideline>
<guideline id="workflow-step6">
<text>STEP 6 - Final Specification (Brain)</text>
<example>
<phase name="spec-1">Create specification (NO water): Objective: {concise_plan_component}</phase>
<phase name="spec-2">Context: Parent Subtask {phase}.{task}.{subtask} - {scope} + {constraints}</phase>
<phase name="spec-3">Existing Status: Already Exists / Needs Refactoring / Build From Scratch</phase>
<phase name="spec-4">Previous Work: Memory #{id} + reuse recommendations + avoid approaches</phase>
<phase name="spec-5">Implementation Guidance: {textual_no_code} + Architecture + Dependencies + Testing + Risks</phase>
<phase name="spec-6">Research References: Memory #{IDs}, .docs/{paths}, best practices</phase>
<phase name="spec-7">Breakdown Recommendations: single_action/complex → execute directly or subdivide into Steps</phase>
<phase name="validation-1">Validate specification with Skill(quality-gate-checker).</phase>
</example>
</guideline>
<guideline id="workflow-step7">
<text>STEP 7 - Create Subissue</text>
<example>
<phase name="task-1">Task(@agent-pm-master, "Create Plan {phase}.{task}.{subtask}.{N} Subissue. Title: Plan {phase}.{task}.{subtask}.{N} - {name}. Body: {synthesized_specification}. Labels: plan, {component_tags}. Parent: Subtask {phase}.{task}.{subtask} Issue. EXPLICITLY use Skill(quality-gate-checker) before creation.")</phase>
</example>
</guideline>
<guideline id="workflow-step8">
<text>STEP 8 - Continue to Next Plan Component</text>
<example>
<phase name="action-1">Continue iteration loop</phase>
</example>
</guideline>
<guideline id="workflow-step9">
<text>STEP 9 - Summary</text>
<example>
<phase name="action-1">Report created plan with approval workflow and GitHub links</phase>
<phase name="validation-1">Use Skill(quality-gate-checker) for final validation.</phase>
</example>
</guideline>
<guideline id="numbering-rules">
<text>Format: "Plan {phase}.{task}.{subtask}.{N} - {name}". N: {phase}.{task}.{subtask}.0, .1, .2... (zero-based within subtask). Title: Max 6 words.</text>
<example>Plan 0.1.2.0 - Define Database Schema</example>
</guideline>
<guideline id="quality-gates">
<text>Quality validation checkpoints</text>
<example>brain list:masters executed</example>
<example>Parent Subtask read via pm-master</example>
<example>ONE plan at a time</example>
<example>Sequential research chain</example>
<example>Code analysis included if available</example>
<example>Memory IDs + .docs/ refs present</example>
<example>Skills explicitly invoked at validation points</example>
<example>Architecture/deps/testing/risks covered</example>
<example>No code blocks</example>
</guideline>
<guideline id="directive">
<text>Discover. Read parent. Iterate. Research. Synthesize. Create. Repeat. EXPLICITLY use Skills.</text>
</guideline>
</guidelines>
<iron_rules>
<rule id="read-agents-first" severity="critical">
<text>Run Bash(brain list:masters) first (dynamic discovery)</text>
<why>Dynamic agent discovery ensures flexibility</why>
<on_violation>Missing agent discovery compromises workflow</on_violation>
</rule>
<rule id="read-parent-subtask" severity="critical">
<text>Read parent Subtask via pm-master</text>
<why>Context from parent subtask essential for plan generation</why>
<on_violation>Cannot generate plans without parent context</on_violation>
</rule>
<rule id="iterate-one-plan" severity="critical">
<text>Iterate ONE plan at a time (no batch)</text>
<why>Sequential processing ensures quality per plan</why>
<on_violation>Batch processing violates iteration principle</on_violation>
</rule>
<rule id="sequential-research" severity="critical">
<text>Sequential research chain (web → sequential-thinking → code → vector → docs)</text>
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
<why>Iteration ensures individual attention per plan</why>
<on_violation>Use iterative approach</on_violation>
</rule>
<rule id="no-hardcoded-agents" severity="medium">
<text>No hardcoded agents (except pm-master)</text>
<why>Dynamic discovery maintains flexibility</why>
<on_violation>Use Bash(brain list:masters) for discovery</on_violation>
</rule>
<rule id="no-direct-github-ops" severity="high">
<text>No direct GitHub ops (delegate)</text>
<why>Separation of concerns via pm-master</why>
<on_violation>Delegate to pm-master</on_violation>
</rule>
</iron_rules>
</command>