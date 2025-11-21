---
name: lfc
description: "Execute task/subtask/plan/step via multi-agent orchestration (sequential + conversational)"
---

<command>
<meta>
<id>lfc</id>
<description>Execute task/subtask/plan/step via multi-agent orchestration (sequential + conversational)</description>
</meta>
<purpose>Sequential task executor with pm-master context-first approach. User dialog about CODE/ACTIONS, not workflow steps.</purpose>
<iron_rules>
<rule id="pm-master-first" severity="critical">
<text>pm-master FIRST (context before ANY search) - EXPLICITLY use Skill(delegation-enforcer)</text>
<why>Context-first approach ensures proper understanding before any action</why>
<on_violation>Trigger correction protocol</on_violation>
</rule>
<rule id="accept-valid-identifiers" severity="critical">
<text>Accept ANY valid task identifier (zeros VALID - zero-based indexing)</text>
<why>Zero-based indexing is valid system design</why>
<on_violation>NEVER reject Task-0.0 as "placeholder"</on_violation>
</rule>
<rule id="silent-research" severity="high">
<text>Research phase (1-7): execute silently, NO user questions about workflow</text>
<why>User focuses on CODE decisions, not workflow mechanics</why>
<on_violation>Do not ask about workflow progression</on_violation>
</rule>
<rule id="code-only-questions" severity="high">
<text>Plan phase (8): ask "Execute THIS code?" (WHAT not HOW). Execution phase (10): ask before each CODE action. Completion phase (11): ask about closing Issue</text>
<why>User dialog limited to actionable CODE decisions</why>
<on_violation>Never ask about workflow steps</on_violation>
</rule>
<rule id="sequential-thinking" severity="critical">
<text>EXPLICITLY use mcp__sequential-thinking__sequentialthinking 4× (analyze → strategize → refine → verify)</text>
<why>Structured reasoning ensures quality decision-making</why>
<on_violation>Missing reasoning phase compromises quality</on_violation>
</rule>
<rule id="web-research-optional" severity="medium">
<text>Web research optional (ASK user first)</text>
<why>User controls external research to manage time</why>
<on_violation>Never perform web research without explicit approval</on_violation>
</rule>
<rule id="no-parallel" severity="critical">
<text>NO parallel execution (sequential only)</text>
<why>Maintains control and predictability</why>
<on_violation>Execute tasks sequentially</on_violation>
</rule>
</iron_rules>
<guidelines>
<guideline id="when-to-use">
<text>Command invocation patterns</text>
<example>/lfc Task-X.Y (e.g., Task-0.0, Task-1.2)</example>
<example>/lfc Subtask-X.Y.Z (e.g., Subtask-0.0.0, Subtask-1.2.3)</example>
<example>/lfc Plan-X.Y.Z.P (e.g., Plan-0.1.2.0)</example>
<example>/lfc Step-X.Y.Z.P.S (e.g., Step-0.0.0.0.0)</example>
<example>Note: Zero-based indexing VALID. Any layer executable. IF $ARGUMENTS empty → ask which Issue.</example>
</guideline>
<guideline id="phase1-pm-master">
<text>Goal: pm-master reads context</text>
<example>
<phase name="logic-1">Task(@agent-pm-master, "Read Issue {$ARGUMENTS}. Extract: description, objectives, requirements, constraints, research refs (Memory IDs, .docs/ links), ALL comments chronological, related Issues. EXPLICITLY use Skill(quality-gate-checker) for validation.")</phase>
<phase name="output-1">Present: Task: {name}, Objective: {summary}, Context: {parent_info}</phase>
</example>
</guideline>
<guideline id="phase1-initial-analysis">
<text>Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Initial analysis (5–8 thoughts)</text>
<example>
<phase name="focus-1">objective, domains, knowledge needs, agents, unknowns</phase>
</example>
</guideline>
<guideline id="phase1-vector-docs-search">
<text>Goal: vector-master + documents-master search</text>
<example>
<phase name="vector-1">Task(@agent-vector-master, "Search: {task_scope}. Query: {specific}. Category: code-solution|architecture|bug-fix. Limit: 10. EXPLICITLY use Skill(context-analyzer) for relevance.")</phase>
<phase name="docs-1">Task(@agent-documents-master, "Find .docs/ for: {task_scope}. Extract: MANDATORY constraints, RECOMMENDED patterns, standards, testing reqs. EXPLICITLY use Skill(quality-gate-checker) for completeness.")</phase>
</example>
</guideline>
<guideline id="phase1-strategy">
<text>Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Strategy (6–10 thoughts)</text>
<example>
<phase name="focus-1">requirements, learnings, approach, agents, sequence, risks</phase>
</example>
</guideline>
<guideline id="phase1-agents-registry">
<text>Goal: Read agents registry</text>
<example>
<phase name="action-1">Bash('brain list:masters')</phase>
</example>
</guideline>
<guideline id="phase1-web-research">
<text>Goal: Optional web research (ASK first)</text>
<example>
<phase name="ask-1">Ask: "Research '{task_topic}' best practices 2025 online? (yes/no/specific-query)"</phase>
<phase name="execute-1">IF yes: Task(@agent-web-research-master, "Research: {task_topic}. EXPLICITLY use Skill(edge-case-handler) for quality validation.")</phase>
</example>
</guideline>
<guideline id="phase1-refine">
<text>Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Refine (6–10 thoughts)</text>
<example>
<phase name="focus-1">all knowledge integration, optimal sequence, plan, deliverables, verification</phase>
<phase name="note-1">STEPS 1-7 executed silently, NO workflow questions</phase>
</example>
</guideline>
<guideline id="phase2-show-plan">
<text>Goal: Show execution plan</text>
<example>
<phase name="present-1">**Task:** {name}</phase>
<phase name="present-2">**Goal:** {objective}</phase>
<phase name="present-3">**Code to Write/Modify:** 1. @agent-{name} → {specific_action}, File: {path}, Purpose: {what_code_does}</phase>
<phase name="present-4">**Constraints:** {from_.docs}</phase>
<phase name="present-5">**Success:** {criteria}</phase>
<phase name="ask-1">Ask: "Execute this plan? (yes/adjust/cancel)"</phase>
<phase name="branch-1">yes → STEP 10</phase>
<phase name="branch-2">adjust → rebuild → STEP 8</phase>
<phase name="branch-3">cancel → abort</phase>
</example>
</guideline>
<guideline id="phase3-execute">
<text>Goal: FOR EACH agent</text>
<example>
<phase name="show-1">Execute: @agent-{name} → {task}, File: {path}, Code: {what_will_be_coded}</phase>
<phase name="ask-1">Ask: "Proceed? (yes/skip/adjust/cancel)"</phase>
<phase name="execute-1">IF yes: Task(@agent-{name}, "{mission}. Context: {task}, requirements: {reqs}, constraints: {from_.docs}, previous: {results}. EXPLICITLY use ALL available Skills from personality banks.")</phase>
<phase name="show-2">Created/Modified: {files}, Status: {success|partial|blocked}, Summary: {done}</phase>
<phase name="continue-1">Continue OR STEP 11 if last</phase>
</example>
</guideline>
<guideline id="phase4-verify">
<text>Goal: EXPLICITLY use mcp__sequential-thinking__sequentialthinking: Verify (7–10 thoughts)</text>
<example>
<phase name="focus-1">requirements met, deliverables complete, gaps, quality, ready?</phase>
<phase name="show-1">Verification: Files created: {list}, Files modified: {list}, Requirements met: {yes|partial|blocked}, Issues: {list_or_none}</phase>
<phase name="ask-1">Ask: "Status: {status}. Decision? (complete/continue/address-gaps/cancel)"</phase>
</example>
</guideline>
<guideline id="phase4-store-meta">
<text>Goal: Store meta insights (Brain direct storage)</text>
<example>
<phase name="action-1">mcp__vector-memory__store_memory({content: "LFC execution pattern: Task {summary}, Orchestration {method}, Agents used {list}, Success patterns {outcomes}, Lessons {learnings}", category: "learning", tags: ["lfc-execution", "orchestration-pattern", "meta-insight"]})</phase>
<phase name="ask-1">Ask: "Memory stored. Close Issue? (yes/just-comment/keep-open)"</phase>
</example>
</guideline>
<guideline id="phase4-update-issue">
<text>Goal: Update Issue</text>
<example>
<phase name="action-1">Task(@agent-pm-master, "Close/Update Issue {$ARGUMENTS}. Summary: {deliverables}, Memory #{id}, agents {list}. State: {closed|open}. EXPLICITLY use Skill(quality-gate-checker) for validation.")</phase>
<phase name="present-1">Present: "Issue {status}. Complete!"</phase>
</example>
</guideline>
<guideline id="quality-gates">
<text>Quality validation checkpoints</text>
<example>pm-master first (context before search)</example>
<example>Zero-based identifiers accepted (Task-0.0 valid)</example>
<example>EXPLICITLY use mcp__sequential-thinking__sequentialthinking 4× phases</example>
<example>EXPLICITLY delegate agents to use Skills from personality banks</example>
<example>Silent research (STEPS 1-7)</example>
<example>User dialog: CODE only (8, 10, 11)</example>
<example>Sequential execution (NOT parallel)</example>
</guideline>
<guideline id="directive">
<text>Context first. Think silently. Show plan. Confirm code. Execute. Verify. Close. EXPLICITLY use Skills. Delegate to agents. Ask about CODE not workflow.</text>
</guideline>
</guidelines>
</command>