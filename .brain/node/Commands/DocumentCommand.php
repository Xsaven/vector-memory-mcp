<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Purpose;
use BrainCore\Attributes\Meta;

#[Meta('id', 'document')]
#[Meta('description', 'Interactive documentation command with maximum quality and user engagement')]
#[Purpose('Document anything specified in $ARGUMENTS with maximum quality, interactivity, and professional technical writing standards')]
class DocumentCommand extends CommandArchetype
{
    /**
     * Handle the documentation command logic.
     */
    protected function handle(): void
    {
        // =========================================================================
        // PURPOSE
        // =========================================================================
        $this->guideline('purpose-statement')
            ->text('Command purpose: Generate professional, comprehensive, interactive documentation for any topic, feature, module, file, or concept specified in $ARGUMENTS.')
            ->example('Maximum quality, user engagement, and adherence to professional technical writing standards are paramount.');

        // =========================================================================
        // IRON RULES
        // =========================================================================
        $this->rule('max-interactivity')->critical()
            ->text('MUST constantly engage user with clarifying questions. NEVER assume - ALWAYS verify understanding.')
            ->why('User (Doc/Artem) values quality and professionalism. Assumptions lead to misalignment and rework.')
            ->onViolation('Stop immediately and ask clarifying question using AskUserQuestion tool.');

        $this->rule('500-line-limit')->critical()
            ->text('Each documentation file MUST NOT exceed 500 lines. If content exceeds limit, split into sequential files (part-1.md, part-2.md, etc.).')
            ->why('Maintains readability and prevents unwieldy single-file documentation.')
            ->onViolation('Split content into multiple files with clear naming convention and cross-references.');

        $this->rule('strict-folder-structure')->high()
            ->text('All documentation MUST be placed in .docs/ directory with strict hierarchical folder structure.')
            ->why('Ensures organization, discoverability, and maintainability of documentation.')
            ->onViolation('Restructure output to comply with approved folder hierarchy.');

        $this->rule('evidence-based')->high()
            ->text('All documentation content MUST be based on actual codebase exploration, file reading, or verified web research.')
            ->why('Prevents speculation and ensures factual accuracy.')
            ->onViolation('Use Explore agent, Read tool, or Web Research Master before writing documentation.');

        $this->rule('user-validation-checkpoints')->high()
            ->text('MUST obtain user approval at key milestones: structure proposal, first draft section, before finalization.')
            ->why('Ensures alignment with user expectations and prevents wasted effort on incorrect direction.')
            ->onViolation('Pause and request user validation using AskUserQuestion tool.');

        // =========================================================================
        // ARGUMENTS FORMAT
        // =========================================================================
        $this->guideline('arguments-format')
            ->text('$ARGUMENTS accepts multiple formats for specifying documentation target.')
            ->example('feature:auth')->key('format-1')
            ->example('module:Brain')->key('format-2')
            ->example('concept:delegation')->key('format-3')
            ->example('file:src/Brain.php')->key('format-4')
            ->example('topic:vector-memory')->key('format-5')
            ->example('Plain text description also supported')->key('format-6');

        // =========================================================================
        // WORKFLOW PHASES
        // =========================================================================
        $this->guideline('phase-1-understanding')
            ->text('Phase 1: Understand what to document through maximum interactivity.')
            ->example()
            ->phase('step-1', 'Parse $ARGUMENTS to identify documentation target type and scope')
            ->phase('step-2', 'Ask clarifying questions: What aspects? What depth? Target audience? Use cases?')
            ->phase('step-3', 'Use AskUserQuestion tool extensively - ask until crystal clear')
            ->phase('step-4', 'Store user answers and build documentation requirements specification')
            ->phase('validation', 'Requirements clarity >= 95%. If unclear, continue questioning.')
            ->phase('output', 'Clear documentation specification with scope, audience, structure outline');

        $this->guideline('phase-2-information-gathering')
            ->text('Phase 2: Gather comprehensive information using all available tools.')
            ->example()
            ->phase('step-1', 'Use Task(subagent_type="Explore", prompt="discover codebase structure related to [topic]")')
            ->phase('step-2', 'Read relevant files identified during exploration')
            ->phase('step-3', 'Search vector memory for existing knowledge: mcp__vector-memory__search_memories')
            ->phase('step-4', 'If external context needed, delegate to Web Research Master: Task(@agent-web-research-master, "research [topic]")')
            ->phase('step-5', 'Use Grep for code pattern discovery, understand architecture and relationships')
            ->phase('validation', 'All information sources exhausted. Evidence-based content >= 95%.')
            ->phase('output', 'Comprehensive information package: code examples, architecture diagrams, use cases, references');

        $this->guideline('phase-3-structure-proposal')
            ->text('Phase 3: Propose documentation structure and obtain user approval.')
            ->example()
            ->phase('step-1', 'Design folder hierarchy within .docs/ (e.g., .docs/features/auth/ or .docs/concepts/delegation/)')
            ->phase('step-2', 'Create documentation outline: sections, subsections, code examples, diagrams')
            ->phase('step-3', 'Estimate content length and plan multi-file split if > 500 lines')
            ->phase('step-4', 'Use AskUserQuestion to present structure and get approval/feedback')
            ->phase('step-5', 'Adjust structure based on feedback until approved')
            ->phase('validation', 'User explicitly approves structure. Structure meets 500-line limit per file.')
            ->phase('output', 'Approved documentation structure with file names, folder paths, section breakdown');

        $this->guideline('phase-4-writing')
            ->text('Phase 4: Write professional documentation with validation checkpoints.')
            ->example()
            ->phase('step-1', 'Write first major section following approved structure')
            ->phase('step-2', 'Use TodoWrite to track progress across sections')
            ->phase('step-3', 'After first section complete, show to user for validation checkpoint')
            ->phase('step-4', 'Continue writing remaining sections based on feedback')
            ->phase('step-5', 'Include: code examples with context, architecture diagrams (text-based), use cases, cross-references')
            ->phase('step-6', 'Maintain professional technical writing: clear, concise, accurate, well-structured')
            ->phase('validation', 'Each section <= 500 lines. User validates first section. Quality standards met.')
            ->phase('output', 'Complete documentation draft with all sections written');

        $this->guideline('phase-5-finalization')
            ->text('Phase 5: Review, finalize, and deliver documentation.')
            ->example()
            ->phase('step-1', 'Final review: check 500-line limits, cross-references, completeness, quality')
            ->phase('step-2', 'Create table of contents if multi-file documentation')
            ->phase('step-3', 'Ensure strict folder structure compliance in .docs/')
            ->phase('step-4', 'Present final documentation to user for approval')
            ->phase('step-5', 'Store insights to vector memory: mcp__vector-memory__store_memory(content, category="learning")')
            ->phase('step-6', 'Write all documentation files to .docs/ with approved structure')
            ->phase('validation', 'All files written. User approves final documentation. Vector memory updated.')
            ->phase('output', 'Published documentation in .docs/ + vector memory insights stored');

        // =========================================================================
        // QUALITY STANDARDS
        // =========================================================================
        $this->guideline('professional-writing')
            ->text('Technical writing must be professional, clear, and maintain highest quality standards.')
            ->example('Clear and concise language')->key('standard-1')
            ->example('Logical structure with proper hierarchy')->key('standard-2')
            ->example('Code examples with full context and explanation')->key('standard-3')
            ->example('Text-based architecture diagrams when needed')->key('standard-4')
            ->example('Use cases and practical examples')->key('standard-5')
            ->example('Cross-references to related documentation')->key('standard-6')
            ->example('Proper markdown formatting with syntax highlighting')->key('standard-7')
            ->example('No assumptions - all claims backed by evidence')->key('standard-8');

        $this->guideline('user-context-awareness')
            ->text('Respect user context: Ukrainian, appreciates quality and professionalism, Laravel expert (17 years PHP, 9 years Laravel), MacBook user, values terminal workflow.')
            ->example('Documentation tone: professional but friendly')->key('tone')
            ->example('Code examples: Laravel/PHP best practices, modern typed code')->key('code-style')
            ->example('Depth: advanced level appropriate for experienced developer')->key('depth')
            ->example('Quality: maximum attention to detail, no shortcuts')->key('quality');

        // =========================================================================
        // FOLDER STRUCTURE GUIDELINES
        // =========================================================================
        $this->guideline('docs-folder-structure')
            ->text('Strict hierarchical folder structure within .docs/ directory.')
            ->example('.docs/features/ - Feature-specific documentation')->key('features')
            ->example('.docs/modules/ - Module/component documentation')->key('modules')
            ->example('.docs/concepts/ - Conceptual explanations')->key('concepts')
            ->example('.docs/architecture/ - System architecture docs')->key('architecture')
            ->example('.docs/guides/ - How-to guides and tutorials')->key('guides')
            ->example('.docs/api/ - API documentation')->key('api')
            ->example('.docs/tor/ - Term of Reference documents')->key('tor')
            ->example('.docs/reference/ - Reference materials')->key('reference');

        $this->guideline('file-naming-conventions')
            ->text('Clear, descriptive file naming conventions for documentation files.')
            ->example('Single file: topic-name.md')->key('single-file')
            ->example('Multi-part: topic-name-part-1.md, topic-name-part-2.md')->key('multi-part')
            ->example('Index file: README.md or index.md for folder overview')->key('index')
            ->example('Lowercase with hyphens, no spaces or special characters')->key('format');

        // =========================================================================
        // YAML FRONT MATTER (CRITICAL)
        // =========================================================================
        $this->rule('yaml-front-matter')->critical()
            ->text('EVERY documentation file MUST start with YAML front matter containing metadata for brain docs command indexing.')
            ->why('brain docs command parses this metadata to provide detailed index and keyword-based search across all documentation.')
            ->onViolation('Add YAML front matter to every documentation file before writing markdown content.');

        $this->guideline('yaml-front-matter-structure')
            ->text('Exact YAML front matter structure required at the beginning of EVERY documentation file.')
            ->example('---
name: "The name of documentation"
description: "The description of documentation"
part: 0
type: "tor"
date: "2025-11-20"
version: "1.0.0"
---')->key('structure')
            ->example('name (required): Clear, concise documentation title')->key('field-name')
            ->example('description (required): Brief 1-2 sentence description of documentation content')->key('field-description')
            ->example('part (optional): Part number for multi-file documentation (0, 1, 2, etc.). Omit for single file.')->key('field-part')
            ->example('type (optional): Documentation type: "tor" (Term of Reference), "guide", "api", "concept", "architecture", "reference". Omit if not applicable.')->key('field-type')
            ->example('date (optional): Documentation creation/update date in YYYY-MM-DD format. Use current date if provided.')->key('field-date')
            ->example('version (optional): Documentation version string (e.g., "1.0.0"). Omit if not applicable.')->key('field-version')
            ->example('After closing ---, start markdown content on next line')->key('format');

        $this->guideline('yaml-front-matter-examples')
            ->text('Real-world examples of YAML front matter for different documentation types.')
            ->example('---
name: "Brain Orchestration System"
description: "Complete guide to Brain orchestration architecture and delegation protocols"
type: "architecture"
date: "2025-11-12"
version: "1.0.0"
---')->key('example-architecture')
            ->example('---
name: "Authentication Feature Documentation"
description: "Implementation details and usage guide for authentication feature"
part: 1
type: "guide"
date: "2025-11-12"
---')->key('example-multi-part')
            ->example('---
name: "Vector Memory API Reference"
description: "API reference for all vector memory MCP tools and operations"
type: "api"
date: "2025-11-12"
---')->key('example-api')
            ->example('---
name: "Delegation Protocols Concept"
description: "Explanation of delegation hierarchies and task assignment framework"
type: "concept"
---')->key('example-minimal');

        // =========================================================================
        // TOOL INTEGRATION
        // =========================================================================
        $this->guideline('explore-integration')
            ->text('Delegate codebase exploration to Explore agent for discovery tasks.')
            ->example('Task(subagent_type="Explore", prompt="Discover all files related to [topic]")')->key('discovery')
            ->example('Task(subagent_type="Explore", prompt="Find all classes implementing [interface]")')->key('pattern')
            ->example('Task(subagent_type="Explore", prompt="Map architecture of [module]")')->key('architecture');

        $this->guideline('web-research-integration')
            ->text('Delegate external research to Web Research Master when context outside codebase is needed.')
            ->example('Task(@agent-web-research-master, "Research Laravel best practices for [topic] in 2025")')->key('external-context')
            ->example('Task(@agent-web-research-master, "Find official documentation for [library]")')->key('official-docs')
            ->example('Only use when codebase exploration insufficient for comprehensive documentation')->key('when');

        $this->guideline('vector-memory-integration')
            ->text('Search and store documentation insights in vector memory.')
            ->example('Search before writing: mcp__vector-memory__search_memories(query="[topic]", limit=5)')->key('search')
            ->example('Store after completion: mcp__vector-memory__store_memory(content="[insights]", category="learning", tags=["documentation"])')->key('store')
            ->example('Leverage existing knowledge to avoid duplication')->key('reuse');

        $this->guideline('ask-user-question-usage')
            ->text('AskUserQuestion tool must be used extensively for maximum interactivity.')
            ->example('Initial scope clarification: "What aspects of [topic] should I focus on?"')->key('scope')
            ->example('Depth questions: "What level of detail? (Overview/Detailed/Comprehensive)"')->key('depth')
            ->example('Audience questions: "Who is the target audience? (Beginners/Intermediate/Advanced)"')->key('audience')
            ->example('Structure approval: "Does this documentation structure meet your needs?"')->key('structure')
            ->example('Section validation: "Is this first section on the right track?"')->key('validation')
            ->example('Use multiSelect when appropriate for multiple choice questions')->key('multi-select');

        // =========================================================================
        // PROGRESS TRACKING
        // =========================================================================
        $this->guideline('todo-tracking')
            ->text('Use TodoWrite to maintain transparent progress tracking throughout documentation process.')
            ->example('Create todos for each phase: Understanding, Gathering, Structure, Writing, Finalization')->key('phases')
            ->example('Mark tasks in_progress while working, completed when done')->key('states')
            ->example('Break writing phase into section-level todos for granular tracking')->key('granularity')
            ->example('User can see clear progress through todo updates')->key('transparency');

        // =========================================================================
        // FILE SIZE MANAGEMENT
        // =========================================================================
        $this->guideline('500-line-splitting-logic')
            ->text('Algorithm for handling content that exceeds 500-line limit.')
            ->example()
            ->phase('detect', 'Before writing, estimate total lines based on section breakdown')
            ->phase('plan', 'If estimate > 500 lines, plan natural split points (by major sections)')
            ->phase('split', 'Create part-1.md, part-2.md, etc. with clear section boundaries')
            ->phase('toc', 'First file contains table of contents with links to all parts')
            ->phase('navigation', 'Each part includes navigation links to previous/next parts')
            ->phase('validation', 'Verify each part file <= 500 lines before writing');

        $this->guideline('cross-referencing')
            ->text('Maintain clear cross-references between documentation files.')
            ->example('[See Part 2](./topic-name-part-2.md) for detailed implementation')->key('part-reference')
            ->example('[Related concept: Delegation](../concepts/delegation.md)')->key('concept-reference')
            ->example('[API Reference](../api/brain-api.md)')->key('api-reference')
            ->example('Use relative paths for portability')->key('relative-paths');

        // =========================================================================
        // VALIDATION GATES
        // =========================================================================
        $this->guideline('validation-checkpoints')
            ->text('Mandatory validation checkpoints ensuring quality and alignment.')
            ->example()
            ->phase('checkpoint-1', 'After Phase 1: User confirms documentation scope and requirements are clear')
            ->phase('checkpoint-2', 'After Phase 3: User approves proposed documentation structure')
            ->phase('checkpoint-3', 'After Phase 4 (first section): User validates writing quality and direction')
            ->phase('checkpoint-4', 'After Phase 5: User approves final documentation before publishing')
            ->phase('enforcement', 'Cannot proceed to next phase without passing checkpoint validation');

        // =========================================================================
        // CONVERSATIONAL STYLE
        // =========================================================================
        $this->guideline('communication-style')
            ->text('Command execution should be conversational, friendly, but professional.')
            ->example('Greet user: "Hi Doc! Let\'s create some top-quality documentation together."')->key('greeting')
            ->example('Explain steps: "I\'ll first explore the codebase to understand [topic]..."')->key('transparency')
            ->example('Ask warmly: "To ensure I document exactly what you need, could you clarify..."')->key('questions')
            ->example('Show progress: "Great! I\'ve completed the structure. Let me show you..."')->key('updates')
            ->example('No emojis unless user explicitly requests them')->key('no-emojis')
            ->example('Respect user\'s time: efficient but thorough')->key('efficiency');

        // =========================================================================
        // EXECUTION WORKFLOW SUMMARY
        // =========================================================================
        $this->guideline('execution-workflow')
            ->text('Complete end-to-end workflow for /document command execution.')
            ->example()
            ->phase('init', 'Parse $ARGUMENTS and greet user')
            ->phase('phase-1', 'Interactive questioning until requirements crystal clear (AskUserQuestion)')
            ->phase('phase-2', 'Gather information (Explore, Read, Web Research, Vector Memory)')
            ->phase('phase-3', 'Propose structure and get approval (AskUserQuestion)')
            ->phase('phase-4', 'Write documentation with validation checkpoints (TodoWrite, AskUserQuestion)')
            ->phase('phase-5', 'Review, finalize, store insights, publish to .docs/')
            ->phase('complete', 'Confirm completion and provide documentation locations');

        // =========================================================================
        // EXAMPLES
        // =========================================================================
        $this->guideline('usage-examples')
            ->text('Example invocations of /document command.')
            ->example('/document feature:authentication - Document authentication feature')->key('example-1')
            ->example('/document module:Brain - Document Brain orchestration module')->key('example-2')
            ->example('/document concept:delegation - Explain delegation concept')->key('example-3')
            ->example('/document file:.brain/node/Brain.php - Document Brain.php file')->key('example-4')
            ->example('/document vector memory architecture - Document vector memory system')->key('example-5');

        // =========================================================================
        // SUCCESS METRICS
        // =========================================================================
        $this->guideline('success-metrics')
            ->text('Metrics defining successful documentation execution.')
            ->example('User satisfaction: 100% (all checkpoints approved)')->key('satisfaction')
            ->example('Quality score: >= 95% (professional writing standards met)')->key('quality')
            ->example('Completeness: >= 95% (all planned sections written)')->key('completeness')
            ->example('File compliance: 100% (all files <= 500 lines)')->key('file-size')
            ->example('Structure compliance: 100% (.docs/ hierarchy followed)')->key('structure')
            ->example('Evidence-based: >= 95% (content backed by exploration/research)')->key('evidence');

        // =========================================================================
        // DIRECTIVE
        // =========================================================================
        $this->guideline('directive')
            ->text('Core documentation directive')
            ->example('Ask constantly! Explore thoroughly! Validate frequently! Write professionally! Deliver excellently!');
    }
}
