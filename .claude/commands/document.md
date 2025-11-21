---
name: document
description: "Interactive documentation command with maximum quality and user engagement"
---

<command>
<meta>
<id>document</id>
<description>Interactive documentation command with maximum quality and user engagement</description>
</meta>
<purpose>Document anything specified in $ARGUMENTS with maximum quality, interactivity, and professional technical writing standards</purpose>
<guidelines>
<guideline id="purpose-statement">
<text>Command purpose: Generate professional, comprehensive, interactive documentation for any topic, feature, module, file, or concept specified in $ARGUMENTS.</text>
<example>Maximum quality, user engagement, and adherence to professional technical writing standards are paramount.</example>
</guideline>
<guideline id="arguments-format">
<text>$ARGUMENTS accepts multiple formats for specifying documentation target.</text>
<example key="format-1">feature:auth</example>
<example key="format-2">module:Brain</example>
<example key="format-3">concept:delegation</example>
<example key="format-4">file:src/Brain.php</example>
<example key="format-5">topic:vector-memory</example>
<example key="format-6">Plain text description also supported</example>
</guideline>
<guideline id="phase-1-understanding">
<text>Phase 1: Understand what to document through maximum interactivity.</text>
<example>
<phase name="step-1">Parse $ARGUMENTS to identify documentation target type and scope</phase>
<phase name="step-2">Ask clarifying questions: What aspects? What depth? Target audience? Use cases?</phase>
<phase name="step-3">Use AskUserQuestion tool extensively - ask until crystal clear</phase>
<phase name="step-4">Store user answers and build documentation requirements specification</phase>
<phase name="validation">Requirements clarity >= 95%. If unclear, continue questioning.</phase>
<phase name="output">Clear documentation specification with scope, audience, structure outline</phase>
</example>
</guideline>
<guideline id="phase-2-information-gathering">
<text>Phase 2: Gather comprehensive information using all available tools.</text>
<example>
<phase name="step-1">Use Task(subagent_type="Explore", prompt="discover codebase structure related to [topic]")</phase>
<phase name="step-2">Read relevant files identified during exploration</phase>
<phase name="step-3">Search vector memory for existing knowledge: mcp__vector-memory__search_memories</phase>
<phase name="step-4">If external context needed, delegate to Web Research Master: Task(@agent-web-research-master, "research [topic]")</phase>
<phase name="step-5">Use Grep for code pattern discovery, understand architecture and relationships</phase>
<phase name="validation">All information sources exhausted. Evidence-based content >= 95%.</phase>
<phase name="output">Comprehensive information package: code examples, architecture diagrams, use cases, references</phase>
</example>
</guideline>
<guideline id="phase-3-structure-proposal">
<text>Phase 3: Propose documentation structure and obtain user approval.</text>
<example>
<phase name="step-1">Design folder hierarchy within .docs/ (e.g., .docs/features/auth/ or .docs/concepts/delegation/)</phase>
<phase name="step-2">Create documentation outline: sections, subsections, code examples, diagrams</phase>
<phase name="step-3">Estimate content length and plan multi-file split if > 500 lines</phase>
<phase name="step-4">Use AskUserQuestion to present structure and get approval/feedback</phase>
<phase name="step-5">Adjust structure based on feedback until approved</phase>
<phase name="validation">User explicitly approves structure. Structure meets 500-line limit per file.</phase>
<phase name="output">Approved documentation structure with file names, folder paths, section breakdown</phase>
</example>
</guideline>
<guideline id="phase-4-writing">
<text>Phase 4: Write professional documentation with validation checkpoints.</text>
<example>
<phase name="step-1">Write first major section following approved structure</phase>
<phase name="step-2">Use TodoWrite to track progress across sections</phase>
<phase name="step-3">After first section complete, show to user for validation checkpoint</phase>
<phase name="step-4">Continue writing remaining sections based on feedback</phase>
<phase name="step-5">Include: code examples with context, architecture diagrams (text-based), use cases, cross-references</phase>
<phase name="step-6">Maintain professional technical writing: clear, concise, accurate, well-structured</phase>
<phase name="validation">Each section <= 500 lines. User validates first section. Quality standards met.</phase>
<phase name="output">Complete documentation draft with all sections written</phase>
</example>
</guideline>
<guideline id="phase-5-finalization">
<text>Phase 5: Review, finalize, and deliver documentation.</text>
<example>
<phase name="step-1">Final review: check 500-line limits, cross-references, completeness, quality</phase>
<phase name="step-2">Create table of contents if multi-file documentation</phase>
<phase name="step-3">Ensure strict folder structure compliance in .docs/</phase>
<phase name="step-4">Present final documentation to user for approval</phase>
<phase name="step-5">Store insights to vector memory: mcp__vector-memory__store_memory(content, category="learning")</phase>
<phase name="step-6">Write all documentation files to .docs/ with approved structure</phase>
<phase name="validation">All files written. User approves final documentation. Vector memory updated.</phase>
<phase name="output">Published documentation in .docs/ + vector memory insights stored</phase>
</example>
</guideline>
<guideline id="professional-writing">
<text>Technical writing must be professional, clear, and maintain highest quality standards.</text>
<example key="standard-1">Clear and concise language</example>
<example key="standard-2">Logical structure with proper hierarchy</example>
<example key="standard-3">Code examples with full context and explanation</example>
<example key="standard-4">Text-based architecture diagrams when needed</example>
<example key="standard-5">Use cases and practical examples</example>
<example key="standard-6">Cross-references to related documentation</example>
<example key="standard-7">Proper markdown formatting with syntax highlighting</example>
<example key="standard-8">No assumptions - all claims backed by evidence</example>
</guideline>
<guideline id="user-context-awareness">
<text>Respect user context: Ukrainian, appreciates quality and professionalism, Laravel expert (17 years PHP, 9 years Laravel), MacBook user, values terminal workflow.</text>
<example key="tone">Documentation tone: professional but friendly</example>
<example key="code-style">Code examples: Laravel/PHP best practices, modern typed code</example>
<example key="depth">Depth: advanced level appropriate for experienced developer</example>
<example key="quality">Quality: maximum attention to detail, no shortcuts</example>
</guideline>
<guideline id="docs-folder-structure">
<text>Strict hierarchical folder structure within .docs/ directory.</text>
<example key="features">.docs/features/ - Feature-specific documentation</example>
<example key="modules">.docs/modules/ - Module/component documentation</example>
<example key="concepts">.docs/concepts/ - Conceptual explanations</example>
<example key="architecture">.docs/architecture/ - System architecture docs</example>
<example key="guides">.docs/guides/ - How-to guides and tutorials</example>
<example key="api">.docs/api/ - API documentation</example>
<example key="tor">.docs/tor/ - Term of Reference documents</example>
<example key="reference">.docs/reference/ - Reference materials</example>
</guideline>
<guideline id="file-naming-conventions">
<text>Clear, descriptive file naming conventions for documentation files.</text>
<example key="single-file">Single file: topic-name.md</example>
<example key="multi-part">Multi-part: topic-name-part-1.md, topic-name-part-2.md</example>
<example key="index">Index file: README.md or index.md for folder overview</example>
<example key="format">Lowercase with hyphens, no spaces or special characters</example>
</guideline>
<guideline id="yaml-front-matter-structure">
<text>Exact YAML front matter structure required at the beginning of EVERY documentation file.</text>
<example key="structure">---
name: "The name of documentation"
description: "The description of documentation"
part: 0
type: "tor"
date: "2025-11-20"
version: "1.0.0"
---</example>
<example key="field-name">name (required): Clear, concise documentation title</example>
<example key="field-description">description (required): Brief 1-2 sentence description of documentation content</example>
<example key="field-part">part (optional): Part number for multi-file documentation (0, 1, 2, etc.). Omit for single file.</example>
<example key="field-type">type (optional): Documentation type: "tor" (Term of Reference), "guide", "api", "concept", "architecture", "reference". Omit if not applicable.</example>
<example key="field-date">date (optional): Documentation creation/update date in YYYY-MM-DD format. Use current date if provided.</example>
<example key="field-version">version (optional): Documentation version string (e.g., "1.0.0"). Omit if not applicable.</example>
<example key="format">After closing ---, start markdown content on next line</example>
</guideline>
<guideline id="yaml-front-matter-examples">
<text>Real-world examples of YAML front matter for different documentation types.</text>
<example key="example-architecture">---
name: "Brain Orchestration System"
description: "Complete guide to Brain orchestration architecture and delegation protocols"
type: "architecture"
date: "2025-11-12"
version: "1.0.0"
---</example>
<example key="example-multi-part">---
name: "Authentication Feature Documentation"
description: "Implementation details and usage guide for authentication feature"
part: 1
type: "guide"
date: "2025-11-12"
---</example>
<example key="example-api">---
name: "Vector Memory API Reference"
description: "API reference for all vector memory MCP tools and operations"
type: "api"
date: "2025-11-12"
---</example>
<example key="example-minimal">---
name: "Delegation Protocols Concept"
description: "Explanation of delegation hierarchies and task assignment framework"
type: "concept"
---</example>
</guideline>
<guideline id="explore-integration">
<text>Delegate codebase exploration to Explore agent for discovery tasks.</text>
<example key="discovery">Task(subagent_type="Explore", prompt="Discover all files related to [topic]")</example>
<example key="pattern">Task(subagent_type="Explore", prompt="Find all classes implementing [interface]")</example>
<example key="architecture">Task(subagent_type="Explore", prompt="Map architecture of [module]")</example>
</guideline>
<guideline id="web-research-integration">
<text>Delegate external research to Web Research Master when context outside codebase is needed.</text>
<example key="external-context">Task(@agent-web-research-master, "Research Laravel best practices for [topic] in 2025")</example>
<example key="official-docs">Task(@agent-web-research-master, "Find official documentation for [library]")</example>
<example key="when">Only use when codebase exploration insufficient for comprehensive documentation</example>
</guideline>
<guideline id="vector-memory-integration">
<text>Search and store documentation insights in vector memory.</text>
<example key="search">Search before writing: mcp__vector-memory__search_memories(query="[topic]", limit=5)</example>
<example key="store">Store after completion: mcp__vector-memory__store_memory(content="[insights]", category="learning", tags=["documentation"])</example>
<example key="reuse">Leverage existing knowledge to avoid duplication</example>
</guideline>
<guideline id="ask-user-question-usage">
<text>AskUserQuestion tool must be used extensively for maximum interactivity.</text>
<example key="scope">Initial scope clarification: "What aspects of [topic] should I focus on?"</example>
<example key="depth">Depth questions: "What level of detail? (Overview/Detailed/Comprehensive)"</example>
<example key="audience">Audience questions: "Who is the target audience? (Beginners/Intermediate/Advanced)"</example>
<example key="structure">Structure approval: "Does this documentation structure meet your needs?"</example>
<example key="validation">Section validation: "Is this first section on the right track?"</example>
<example key="multi-select">Use multiSelect when appropriate for multiple choice questions</example>
</guideline>
<guideline id="todo-tracking">
<text>Use TodoWrite to maintain transparent progress tracking throughout documentation process.</text>
<example key="phases">Create todos for each phase: Understanding, Gathering, Structure, Writing, Finalization</example>
<example key="states">Mark tasks in_progress while working, completed when done</example>
<example key="granularity">Break writing phase into section-level todos for granular tracking</example>
<example key="transparency">User can see clear progress through todo updates</example>
</guideline>
<guideline id="500-line-splitting-logic">
<text>Algorithm for handling content that exceeds 500-line limit.</text>
<example>
<phase name="detect">Before writing, estimate total lines based on section breakdown</phase>
<phase name="plan">If estimate > 500 lines, plan natural split points (by major sections)</phase>
<phase name="split">Create part-1.md, part-2.md, etc. with clear section boundaries</phase>
<phase name="toc">First file contains table of contents with links to all parts</phase>
<phase name="navigation">Each part includes navigation links to previous/next parts</phase>
<phase name="validation">Verify each part file <= 500 lines before writing</phase>
</example>
</guideline>
<guideline id="cross-referencing">
<text>Maintain clear cross-references between documentation files.</text>
<example key="part-reference">[See Part 2](./topic-name-part-2.md) for detailed implementation</example>
<example key="concept-reference">[Related concept: Delegation](../concepts/delegation.md)</example>
<example key="api-reference">[API Reference](../api/brain-api.md)</example>
<example key="relative-paths">Use relative paths for portability</example>
</guideline>
<guideline id="validation-checkpoints">
<text>Mandatory validation checkpoints ensuring quality and alignment.</text>
<example>
<phase name="checkpoint-1">After Phase 1: User confirms documentation scope and requirements are clear</phase>
<phase name="checkpoint-2">After Phase 3: User approves proposed documentation structure</phase>
<phase name="checkpoint-3">After Phase 4 (first section): User validates writing quality and direction</phase>
<phase name="checkpoint-4">After Phase 5: User approves final documentation before publishing</phase>
<phase name="enforcement">Cannot proceed to next phase without passing checkpoint validation</phase>
</example>
</guideline>
<guideline id="communication-style">
<text>Command execution should be conversational, friendly, but professional.</text>
<example key="greeting">Greet user: "Hi Doc! Let's create some top-quality documentation together."</example>
<example key="transparency">Explain steps: "I'll first explore the codebase to understand [topic]..."</example>
<example key="questions">Ask warmly: "To ensure I document exactly what you need, could you clarify..."</example>
<example key="updates">Show progress: "Great! I've completed the structure. Let me show you..."</example>
<example key="no-emojis">No emojis unless user explicitly requests them</example>
<example key="efficiency">Respect user's time: efficient but thorough</example>
</guideline>
<guideline id="execution-workflow">
<text>Complete end-to-end workflow for /document command execution.</text>
<example>
<phase name="init">Parse $ARGUMENTS and greet user</phase>
<phase name="phase-1">Interactive questioning until requirements crystal clear (AskUserQuestion)</phase>
<phase name="phase-2">Gather information (Explore, Read, Web Research, Vector Memory)</phase>
<phase name="phase-3">Propose structure and get approval (AskUserQuestion)</phase>
<phase name="phase-4">Write documentation with validation checkpoints (TodoWrite, AskUserQuestion)</phase>
<phase name="phase-5">Review, finalize, store insights, publish to .docs/</phase>
<phase name="complete">Confirm completion and provide documentation locations</phase>
</example>
</guideline>
<guideline id="usage-examples">
<text>Example invocations of /document command.</text>
<example key="example-1">/document feature:authentication - Document authentication feature</example>
<example key="example-2">/document module:Brain - Document Brain orchestration module</example>
<example key="example-3">/document concept:delegation - Explain delegation concept</example>
<example key="example-4">/document file:.brain/node/Brain.php - Document Brain.php file</example>
<example key="example-5">/document vector memory architecture - Document vector memory system</example>
</guideline>
<guideline id="success-metrics">
<text>Metrics defining successful documentation execution.</text>
<example key="satisfaction">User satisfaction: 100% (all checkpoints approved)</example>
<example key="quality">Quality score: >= 95% (professional writing standards met)</example>
<example key="completeness">Completeness: >= 95% (all planned sections written)</example>
<example key="file-size">File compliance: 100% (all files <= 500 lines)</example>
<example key="structure">Structure compliance: 100% (.docs/ hierarchy followed)</example>
<example key="evidence">Evidence-based: >= 95% (content backed by exploration/research)</example>
</guideline>
<guideline id="directive">
<text>Core documentation directive</text>
<example>Ask constantly! Explore thoroughly! Validate frequently! Write professionally! Deliver excellently!</example>
</guideline>
</guidelines>
<iron_rules>
<rule id="max-interactivity" severity="critical">
<text>MUST constantly engage user with clarifying questions. NEVER assume - ALWAYS verify understanding.</text>
<why>User (Doc/Artem) values quality and professionalism. Assumptions lead to misalignment and rework.</why>
<on_violation>Stop immediately and ask clarifying question using AskUserQuestion tool.</on_violation>
</rule>
<rule id="500-line-limit" severity="critical">
<text>Each documentation file MUST NOT exceed 500 lines. If content exceeds limit, split into sequential files (part-1.md, part-2.md, etc.).</text>
<why>Maintains readability and prevents unwieldy single-file documentation.</why>
<on_violation>Split content into multiple files with clear naming convention and cross-references.</on_violation>
</rule>
<rule id="strict-folder-structure" severity="high">
<text>All documentation MUST be placed in .docs/ directory with strict hierarchical folder structure.</text>
<why>Ensures organization, discoverability, and maintainability of documentation.</why>
<on_violation>Restructure output to comply with approved folder hierarchy.</on_violation>
</rule>
<rule id="evidence-based" severity="high">
<text>All documentation content MUST be based on actual codebase exploration, file reading, or verified web research.</text>
<why>Prevents speculation and ensures factual accuracy.</why>
<on_violation>Use Explore agent, Read tool, or Web Research Master before writing documentation.</on_violation>
</rule>
<rule id="user-validation-checkpoints" severity="high">
<text>MUST obtain user approval at key milestones: structure proposal, first draft section, before finalization.</text>
<why>Ensures alignment with user expectations and prevents wasted effort on incorrect direction.</why>
<on_violation>Pause and request user validation using AskUserQuestion tool.</on_violation>
</rule>
<rule id="yaml-front-matter" severity="critical">
<text>EVERY documentation file MUST start with YAML front matter containing metadata for brain docs command indexing.</text>
<why>brain docs command parses this metadata to provide detailed index and keyword-based search across all documentation.</why>
<on_violation>Add YAML front matter to every documentation file before writing markdown content.</on_violation>
</rule>
</iron_rules>
</command>