---
name: generate:tasks
description: "Generate Task layer for PM hierarchy"
---

<command>
<meta>
<id>generate:tasks</id>
<description>Generate Task layer for PM hierarchy</description>
</meta>
<purpose>Iterative generation of tactical tasks (L2) via deep multi-agent research per task.</purpose>
<guidelines>
<guideline id="role">
<text>Dynamic agent discovery via Bash(brain list:masters). Read parent Phase Issue(s) via @agent-pm-master. Iterative loop (ONE task at a time). Sequential multi-agent research chain per task. Brain synthesis with rich context (Memory IDs, .docs/ refs). Delegate execution to @agent-pm-master.</text>
</guideline>
<guideline id="when-to-use">
<text>IF user requests task generation OR phase drill-down → initiate iterative workflow. IF $ARGUMENTS provided → phase/topic filter. IF $ARGUMENTS empty → all open Phases.</text>
</guideline>
<guideline id="workflow-thinking">
<text>Iterative generation: ONE task at a time with deep sequential research. Each task gets full multi-agent analysis before creation. Skills MUST be invoked explicitly at key validation points.</text>
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
<phase name="task-1">Task(@agent-pm-master, "Analyze parent Phase context for Task generation. EXPLICITLY use Skill(context-analyzer) to validate readiness before analysis. Read & extract: Phase Issue(s), ALL comments, linked Issues, sub-issues. Analyze: scope, requirements, decisions, related context, current status. Use Skill(quality-gate-checker) to validate extracted context completeness. Return structured summary with synthesis.")</phase>
</example>
</guideline>
<guideline id="workflow-step2">
<text>STEP 2 - Determine Tasks</text>
<example>
<phase name="thinking-1">Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent Phase context. Goal: Determine task count (5-15) and names. Output: List of task scopes</phase>
</example>
</guideline>
<guideline id="workflow-loop">
<text>FOR EACH task in list</text>
</guideline>
<guideline id="workflow-step3">
<text>STEP 3 - Deep Research (Sequential Chain)</text>
<example>
<phase name="3.1">Check Existing Work: Task(@agent-vector-master, "Search for existing work on '{task_scope} {stack} task implementations'. Category: 'code-solution, architecture, bug-fix'. Limit: 5. Use Skill(brain-philosophy) for cognitive framework during search. Return: Memory IDs + insights + reuse recommendations")</phase>
<phase name="3.2">Initial Code Scan (if code-analysis agents exist): FOR EACH code agent: Task(@agent-{code}, "Scan codebase for {task_scope}. EXPLICITLY use Skill(architecture-introspector) for system awareness. Questions: existing components? implementation gaps? reuse/refactor/new assessment? Return: List + gaps + assessment")</phase>
<phase name="3.3">Best Practices: Task(@agent-web-research-master, "Research {year} task breakdown best practices for {stack}. Focus: {task_scope} within {phase} objectives. Use Skill(quality-gate-checker) to validate research quality. Return: Tactical patterns, sizing guidance")</phase>
<phase name="3.4">Initial Strategy: Use mcp__sequential-thinking__sequentialthinking tool: Analyze: parent + existing work + code scan + web research. Goal: Initial task breakdown. Output: 1-5 day sizing, category</phase>
<phase name="3.5">Documentation: Task(@agent-documents-master, "Find .docs/ for {task_scope}. Use Skill(context-analyzer) to ensure document relevance. Return: File paths + insights")</phase>
</example>
</guideline>
<guideline id="workflow-step4">
<text>STEP 4 - Synthesis (Brain)</text>
<example>
<phase name="action-1">Combine: Phase context + existing work + code scan + web practices + sequential-thinking + docs</phase>
<phase name="action-2">Use Skill(quality-gate-checker) for synthesis validation.</phase>
</example>
</guideline>
<guideline id="workflow-step5">
<text>STEP 5 - Code Alignment (if code-analysis agents exist)</text>
<example>
<phase name="action-1">FOR EACH code agent: Task(@agent-{code}, "Verify synthesized strategy for {task_scope}. EXPLICITLY use Skill(architecture-introspector) for dependency validation. Questions: reuse potential? refactoring needs? new development areas? conflicts? Return: Recommendations + requirements + considerations")</phase>
</example>
</guideline>
<guideline id="workflow-step6">
<text>STEP 6 - Final Specification (Brain)</text>
<example>
<phase name="spec-1">Create specification (NO water): Objective: {concise_no_fluff}</phase>
<phase name="spec-2">Context: Parent Phase {N} - {scope} + {constraints}</phase>
<phase name="spec-3">Existing Status: Already Exists / Needs Refactoring / Build From Scratch</phase>
<phase name="spec-4">Previous Work: Memory #{id} + reuse recommendations + avoid approaches</phase>
<phase name="spec-5">Implementation Guidance: {textual_no_code}</phase>
<phase name="spec-6">Research References: Memory #{IDs}, .docs/{paths}, best practices</phase>
<phase name="spec-7">Breakdown Recommendations: atomic/normal → implement directly or subdivide</phase>
<phase name="validation-1">Validate specification with Skill(quality-gate-checker).</phase>
</example>
</guideline>
<guideline id="workflow-step7">
<text>STEP 7 - Create Subissue</text>
<example>
<phase name="task-1">Task(@agent-pm-master, "Create Task {phase}.{N} Subissue. Title: Task {phase}.{N} - {name}. Body: {synthesized_specification}. Labels: task, {category_tags}. Parent: Phase {phase} Issue. EXPLICITLY use Skill(quality-gate-checker) before creation.")</phase>
</example>
</guideline>
<guideline id="workflow-step8">
<text>STEP 8 - Continue to Next Task</text>
<example>
<phase name="action-1">Continue iteration loop</phase>
</example>
</guideline>
<guideline id="workflow-step9">
<text>STEP 9 - Summary</text>
<example>
<phase name="action-1">Report created tasks with category balance and GitHub links</phase>
<phase name="validation-1">Use Skill(quality-gate-checker) for final validation.</phase>
</example>
</guideline>
<guideline id="numbering-rules">
<text>Format: "Task {phase}.{N} - {name}". N: {phase}.0, .1, .2... (zero-based within phase). Title: Max 6 words.</text>
<example>Task 0.0 - Setup Development Environment</example>
</guideline>
<guideline id="quality-gates">
<text>Quality validation checkpoints</text>
<example>brain list:masters executed</example>
<example>Parent Phase read via pm-master</example>
<example>ONE task at a time</example>
<example>Sequential research chain</example>
<example>Code analysis included if available</example>
<example>Memory IDs + .docs/ refs present</example>
<example>Skills explicitly invoked at validation points</example>
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
<rule id="read-parent-via-pm" severity="critical">
<text>Read parent Phase via pm-master</text>
<why>Context from parent phase essential for task generation</why>
<on_violation>Cannot generate tasks without parent context</on_violation>
</rule>
<rule id="iterate-one-at-time" severity="critical">
<text>Iterate ONE task at a time (no batch)</text>
<why>Sequential processing ensures quality per task</why>
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
<why>Iteration ensures individual attention per task</why>
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