---
name: init-vector
description: "Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents"
---

<command>
<meta>
<id>init-vector</id>
<description>Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents</description>
</meta>
<purpose>Systematically scan and document entire project into vector memory. Sequential ExploreMaster agents explore logical areas, communicating through vector memory for continuity. Each agent searches memory before exploring, stores findings after. Enables comprehensive project knowledge base for all agents.</purpose>
<iron_rules>
<rule id="memory-first-mandatory" severity="critical">
<text>Every ExploreMaster MUST search vector memory BEFORE exploring to maintain continuity</text>
<why>Enables context sharing between sequential agents and prevents duplicate work</why>
<on_violation>Include explicit MANDATORY BEFORE: search_memories in delegation</on_violation>
</rule>
<rule id="memory-store-mandatory" severity="critical">
<text>Every ExploreMaster MUST store findings to vector memory AFTER exploring</text>
<why>Builds knowledge base for future agents and creates persistent project documentation</why>
<on_violation>Include explicit MANDATORY AFTER: store_memory in delegation</on_violation>
</rule>
<rule id="vector-memory-is-communication-channel" severity="critical">
<text>Vector memory is the PRIMARY communication channel between sequential agents</text>
<why>Agents pass context through memory, not through Brain. Short reports to Brain, detailed data to memory.</why>
<on_violation>Emphasize memory usage in delegation instructions</on_violation>
</rule>
<rule id="no-interactive-questions" severity="high">
<text>NO interactive questions - fully automated workflow</text>
<why>Automated initialization for project knowledge base</why>
<on_violation>Execute fully automated without user prompts</on_violation>
</rule>
<rule id="thoroughness-appropriate" severity="high">
<text>Use "medium" thoroughness for most areas, "very thorough" only for complex core areas</text>
<why>Balances comprehensive coverage with reasonable execution time</why>
<on_violation>Adjust thoroughness based on area complexity</on_violation>
</rule>
</iron_rules>
<guidelines>
<guideline id="phase1-memory-status">
GOAL(Check if vector memory is empty (first-time setup) or has existing data)
<example>
<phase name="1">mcp__vector-memory__get_memory_stats('{}')</phase>
<phase name="2">STORE-AS($ = '{total_memories, categories, age}')</phase>
<phase name="3">IF(total_memories === 0) → THEN → [STORE-AS($ = 'true') → OUTPUT(Vector memory empty - performing first-time initialization)] → END-IF</phase>
<phase name="4">IF(total_memories > 0) → THEN → [STORE-AS($ = 'false') → OUTPUT(Vector memory has {total_memories} entries - augmenting existing knowledge)] → END-IF</phase>
</example>
</guideline>
<guideline id="phase2-structure-scan">
GOAL(Get high-level project overview and identify logical areas to explore)
<example>
<phase name="1">OUTPUT(Scanning project structure...)</phase>
<phase name="2">Task(@agent-, 'TASK → [(MANDATORY BEFORE: mcp__vector-memory__search_memories(query: "project structure overview", limit: 5, category: "architecture") + IF(memory has recent structure) → THEN → Use cached structure, verify with quick scan → END-IF + IF(no cached structure) → THEN → Full structure discovery → END-IF + Explore root directory structure (quick scan) + Identify main directories: src/, tests/, docs/, config/, vendor/, node_modules/, etc. + Determine project type: Laravel, Node.js, React, etc. + Map logical exploration areas based on directory structure + Priority: src/ > tests/ > config/ > docs/ > other + MANDATORY AFTER: mcp__vector-memory__store_memory(content: "Project Structure:\\n{directory_tree}\\n\\nProject Type: {type}\\n\\nAreas: {areas_list}\\n\\nPriority: {priority_order}", category: "architecture", tags: ["init-vector", "project-structure", "overview"]))] → END-TASK', 'OUTPUT({structure, project_type, areas: [{name, path, priority, complexity}], directory_tree})', 'STORE-AS($)')</phase>
<phase name="3">OUTPUT(Structure discovered: {areas_count} areas identified)</phase>
</example>
</guideline>
<guideline id="phase3-area-exploration">
GOAL(Explore each project area sequentially with ExploreMaster agents communicating via vector memory)
<example>
<phase name="1">OUTPUT(Beginning sequential area exploration...)</phase>
<phase name="2">FOREACH(area in $PROJECT_STRUCTURE.areas) → [OUTPUT(━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━) → OUTPUT(Exploring: {area.name} ({area.path})) → OUTPUT(Priority: {area.priority} | Complexity: {area.complexity}) → Task(@agent-, 'INPUT(STORE-GET($))', 'TASK → [(━━━ MANDATORY BEFORE: SEARCH VECTOR MEMORY ━━━ + Execute: mcp__vector-memory__search_memories(query: "{area.name} {area.path} context previous findings", limit: 5, category: "architecture,code-solution") + Review: Analyze results for patterns, previous findings, related areas + Context: Use findings from previous agents to inform exploration +  + ━━━ EXPLORATION: {area.name} ━━━ + Thoroughness: IF(area.complexity === "high") → very thorough → ELSE → medium → END-IF + Path: {area.path} +  + TASK 1: File Discovery +   - Use Glob to discover all files in {area.path} +   - Identify file types and patterns +   - Map directory structure within area +  + TASK 2: Code Analysis +   - Use Grep to find: class definitions, function signatures, imports +   - Identify naming conventions and patterns +   - Extract key abstractions and components +  + TASK 3: Architecture Understanding +   - Analyze relationships between components +   - Identify design patterns and architectural styles +   - Document dependencies and workflows +  + TASK 4: Technology Stack +   - Identify frameworks, libraries, tools used +   - Extract version information from config files +   - Note key technologies and dependencies +  + ━━━ MANDATORY AFTER: STORE TO VECTOR MEMORY ━━━ + Execute: mcp__vector-memory__store_memory( +   content: "Area: {area.name} ({area.path})\\n\\n +     ## File Structure\\n{file_tree}\\n\\n +     ## Key Components\\n{components_list}\\n\\n +     ## Patterns & Conventions\\n{patterns}\\n\\n +     ## Technologies\\n{tech_stack}\\n\\n +     ## Architecture Notes\\n{architecture_insights}\\n\\n +     ## Dependencies\\n{dependencies}", +   category: "architecture", +   tags: ["init-vector", "{area.name}", "{area.path}", "exploration"] + ) +  + ━━━ BRIEF REPORT TO BRAIN ━━━ + Report: "Area {area.name} explored ✓ | Files: {count} | Components: {key_components} | Stored to memory")] → END-TASK', 'OUTPUT(Brief progress update)') → OUTPUT(✓ {area.name} complete) → REPORT(Progress: {completed}/{total} areas explored)] → END-FOREACH</phase>
<phase name="3">OUTPUT(All areas explored ✓)</phase>
</example>
</guideline>
<guideline id="phase4-relationships">
GOAL(Analyze relationships and dependencies between explored areas)
<example>
<phase name="1">OUTPUT(Analyzing cross-area relationships...)</phase>
<phase name="2">Task(@agent-, 'TASK → [(MANDATORY BEFORE: mcp__vector-memory__search_memories(query: "project areas components architecture", limit: 20, category: "architecture") + Review: All area exploration results from memory +  + TASK: Cross-Area Analysis +   - Identify imports/dependencies between areas +   - Map component relationships across boundaries +   - Analyze architectural patterns (layering, separation of concerns) +   - Document data flow and communication patterns +  + MANDATORY AFTER: mcp__vector-memory__store_memory( +   content: "Project-Wide Architecture\\n\\n +     ## Area Dependencies\\n{dependency_graph}\\n\\n +     ## Component Relationships\\n{relationships}\\n\\n +     ## Architectural Patterns\\n{patterns}\\n\\n +     ## Data Flow\\n{data_flow}", +   category: "architecture", +   tags: ["init-vector", "project-wide", "relationships", "architecture"] + ))] → END-TASK', 'OUTPUT(Brief summary)')</phase>
<phase name="3">OUTPUT(Relationships analyzed ✓)</phase>
</example>
</guideline>
<guideline id="phase5-completion">
GOAL(Report completion and store initialization summary)
<example>
<phase name="1">mcp__vector-memory__get_memory_stats('{}')</phase>
<phase name="2">STORE-AS($ = '{total_memories, categories, size}')</phase>
<phase name="3">STORE-AS($ = 'count($PROJECT_STRUCTURE.areas)')</phase>
<phase name="4">mcp__vector-memory__store_memory('{'."\n"
    .'                    content: "Vector Memory Initialization Complete\\n\\n'."\n"
    .'                        Timestamp: {current_date}\\n'."\n"
    .'                        Areas Explored: {$AREAS_EXPLORED}\\n'."\n"
    .'                        Project Type: {$PROJECT_STRUCTURE.project_type}\\n'."\n"
    .'                        Total Memories: {$FINAL_MEMORY_STATUS.total_memories}\\n'."\n"
    .'                        Categories: {$FINAL_MEMORY_STATUS.categories}\\n'."\n"
    .'                        Status: Comprehensive project knowledge base established",'."\n"
    .'                    category: "architecture",'."\n"
    .'                    tags: ["init-vector", "completion", "summary"]'."\n"
    .'                }')</phase>
<phase name="5">OUTPUT( ═══════════════════════════════════════════════════════════ VECTOR MEMORY INITIALIZATION COMPLETE ═══════════════════════════════════════════════════════════  Project: {$PROJECT_STRUCTURE.project_type} Areas Explored: {$AREAS_EXPLORED}  Memory Status:   Total Memories: {$FINAL_MEMORY_STATUS.total_memories}   Categories: {$FINAL_MEMORY_STATUS.categories}   Size: {$FINAL_MEMORY_STATUS.size}  Coverage:   ✓ Project structure and organization   ✓ File patterns and naming conventions   ✓ Component architecture and relationships   ✓ Technology stack and dependencies   ✓ Cross-area dependencies and data flow  Next Steps:   • All agents can now leverage project knowledge via vector memory   • Use mcp__vector-memory__search_memories() to query knowledge base   • Run /init-vector again to refresh/augment project knowledge  ═══════════════════════════════════════════════════════════)</phase>
</example>
</guideline>
<guideline id="memory-communication-pattern">
<text>Standard pattern for agent-to-agent communication via vector memory</text>
<example>
<phase name="1">
BEFORE EXPLORATION:
(Execute: mcp__vector-memory__search_memories(query: "{relevant_context}", limit: 5) + Review: Previous findings and context + Apply: Insights from previous agents)
</phase>
<phase name="2">
DURING EXPLORATION:
(Focus: Execute exploration tasks + Discover: File structure, code patterns, architecture + Analyze: Components, relationships, technologies)
</phase>
<phase name="3">
AFTER EXPLORATION:
(Document: Comprehensive findings + Execute: mcp__vector-memory__store_memory(content: "{detailed_findings}", category: "architecture", tags: [...]) + Report: Brief progress to Brain)
</phase>
<phase name="4">CRITICAL: Vector memory is the knowledge base. Your findings enable next agents!</phase>
</example>
</guideline>
<guideline id="exploration-areas-template">
<text>Standard areas to explore in typical projects</text>
<example>
<phase name="1">
Core Source Code:
(src/ or app/ - main application code + Priority: HIGH + Thoroughness: very thorough)
</phase>
<phase name="2">
Tests:
(tests/ or __tests__/ - test suites + Priority: HIGH + Thoroughness: medium)
</phase>
<phase name="3">
Configuration:
(config/ - application configuration + Priority: MEDIUM + Thoroughness: medium)
</phase>
<phase name="4">
Documentation:
(.docs/ - project documentation + Priority: MEDIUM + Thoroughness: medium)
</phase>
<phase name="5">
Build & Deploy:
(build scripts, CI/CD configs + Priority: LOW + Thoroughness: quick)
</phase>
<phase name="6">
Dependencies:
(package.json, composer.json, etc. + Priority: LOW + Thoroughness: quick)
</phase>
<phase name="7">Note: Areas dynamically determined based on actual project structure</phase>
</example>
</guideline>
<guideline id="error-handling">
<text>Graceful error handling during initialization</text>
<example>
<phase name="1">IF(vector memory unavailable) → THEN → [Report: "Vector memory MCP unavailable - cannot initialize" → Suggest: Check MCP server status → Abort: Cannot proceed without memory access] → END-IF</phase>
<phase name="2">IF(area exploration fails) → THEN → [Log: "Area {name} exploration failed: {error}" → Continue: Proceed with next area → Store: Partial results to memory → Report: Failed areas in completion summary] → END-IF</phase>
<phase name="3">IF(ExploreMaster timeout) → THEN → [Log: "Area {name} exploration timeout" → Skip: Move to next area → Report: Timeout in summary] → END-IF</phase>
<phase name="4">IF(empty project or no files) → THEN → [Report: "Project appears empty or inaccessible" → Store: Basic structure info → Complete: Mark as initialized with minimal data] → END-IF</phase>
</example>
</guideline>
<guideline id="quality-metrics">
<text>Quality targets for initialization</text>
<example key="coverage">Areas explored: 100% of discovered directories</example>
<example key="memory-ops">Memory stores: 1 per area + 1 project-wide + 1 completion</example>
<example key="memory-reads">Agent memory searches: 1 per area exploration</example>
<example key="thoroughness">Thoroughness: medium for most, very thorough for src/</example>
<example key="performance">Execution time: ≤ 5 minutes for typical project</example>
</guideline>
<guideline id="parallel-execution-note">
<text>Future optimization: parallel exploration for independent areas</text>
<example>
<phase name="1">Current: Sequential execution (one area at a time)</phase>
<phase name="2">Rationale: Ensures memory continuity and prevents race conditions</phase>
<phase name="3">Future: Independent areas (e.g., src/, tests/, docs/) could run in parallel</phase>
<phase name="4">Optimization: 3-5 ExploreMaster::call() in single response for independent areas</phase>
<phase name="5">Note: Requires careful coordination to prevent memory conflicts</phase>
</example>
</guideline>
<guideline id="example-1-fresh-init">
SCENARIO(First-time initialization with empty vector memory)
<example>
<phase name="input">/init-vector (no arguments)</phase>
<phase name="phase1">Memory status: 0 entries → IS_FRESH_INIT = true</phase>
<phase name="phase2">Structure scan: discovered 5 areas (src/, tests/, config/, docs/, build/)</phase>
<phase name="phase3">
Sequential exploration:
(Area 1: src/ (very thorough) → 150 files, 85 classes → stored to memory + Area 2: tests/ (medium) → 45 files, 30 test classes → stored to memory + Area 3: config/ (medium) → 12 files, configs → stored to memory + Area 4: docs/ (medium) → 8 files, documentation → stored to memory + Area 5: build/ (quick) → 3 files, build scripts → stored to memory)
</phase>
<phase name="phase4">Relationships: 23 cross-area dependencies → stored to memory</phase>
<phase name="phase5">Completion: 157 total memories, 5 areas explored ✓</phase>
</example>
</guideline>
<guideline id="example-2-augment-existing">
SCENARIO(Re-running initialization with existing vector memory)
<example>
<phase name="input">/init-vector</phase>
<phase name="phase1">Memory status: 157 entries → IS_FRESH_INIT = false → augmenting</phase>
<phase name="phase2">Structure scan: agent searches memory, finds cached structure, verifies with quick scan</phase>
<phase name="phase3">Sequential exploration: each agent searches memory first, finds previous findings, augments with new discoveries</phase>
<phase name="phase4">Relationships: updates existing relationship data</phase>
<phase name="phase5">Completion: 203 total memories (+46), refreshed knowledge base ✓</phase>
</example>
</guideline>
<guideline id="example-3-area-specific">
SCENARIO(Single area exploration demonstrating memory communication)
<example>
<phase name="area">src/ (complexity: high, priority: HIGH)</phase>
<phase name="before">Agent searches: "src code structure patterns" → finds 3 related memories from docs/ and config/</phase>
<phase name="during">
Agent explores src/ thoroughly:
(Glob: discovers 150 files (*.php, *.ts, etc.) + Grep: finds 85 class definitions, 200+ functions + Analyzes: MVC pattern, service layer, repositories + Identifies: Laravel framework, dependency injection)
</phase>
<phase name="after">Agent stores: comprehensive src/ findings to memory with tags</phase>
<phase name="report">Brief: "src/ explored ✓ | 150 files | 85 classes | Stored to memory"</phase>
</example>
</guideline>
<guideline id="directive">
<text>Initialize vector memory! Sequential exploration! Memory-first communication! Brief reports! Comprehensive documentation! Build knowledge base!</text>
</guideline>
</guidelines>
</command>