---
name: "doc:work"
description: "Interactive documentation command with maximum quality and user engagement"
---

<command>
<meta>
<id>doc:work</id>
<description>Interactive documentation command with maximum quality and user engagement</description>
</meta>
<execute>Document anything specified in $ARGUMENTS with maximum quality, interactivity, and professional technical writing standards</execute>
<provides>The Work command is a guided workflow for writing documentation for a project.</provides>

# Iron Rules
## Max-interactivity (CRITICAL)
MUST engage user with clarifying questions via AskUserQuestion tool. NEVER assume.
- **why**: Assumptions lead to misalignment and rework.
- **on_violation**: Stop and ask clarifying question.

## 500-line-limit (CRITICAL)
Each file MUST NOT exceed 500 lines. Split into part-1.md, part-2.md if needed.
- **why**: Maintains readability.
- **on_violation**: Split content with clear naming and cross-references.

## Docs-folder-structure (HIGH)
All documentation in .docs/ with hierarchy: features/, modules/, concepts/, architecture/, guides/, api/, tor/, reference/
- **why**: Ensures organization and discoverability.
- **on_violation**: Restructure to comply with folder hierarchy.

## Evidence-based (HIGH)
Content MUST be based on codebase exploration, file reading, or verified web research.
- **why**: Prevents speculation.
- **on_violation**: Use Explore agent, Read tool, or Web Research Master first.

## Validation-checkpoints (HIGH)
Obtain user approval at: structure proposal, first `draft` section, before finalization.
- **why**: Ensures alignment with expectations.
- **on_violation**: Pause and request validation.

## Yaml-front-matter (CRITICAL)
EVERY file MUST start with YAML front matter for brain docs indexing.
- **why**: brain docs parses metadata for index and search.
- **on_violation**: Add YAML front matter before markdown content.


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($DOC_TARGET = {documentation target extracted from $RAW_INPUT})

# Arguments format
Input accepts: feature:X, module:X, concept:X, file:X, topic:X, or plain text description.

# Phase 1 understanding
Phase 1: Understand what to document through maximum interactivity.
- `1`: STORE-AS($TARGET_TYPE = {detect type from $RAW_INPUT: feature|module|concept|file|topic})
- `2`: STORE-AS($TARGET_VALUE = {extract value after type prefix from $RAW_INPUT})
- `step-1`: Parse $RAW_INPUT to identify $TARGET_TYPE and scope via $TARGET_VALUE
- `step-2`: Ask: What aspects? What depth? Target audience? Use cases?
- `step-3`: Use AskUserQuestion until crystal clear
- `validation`: Requirements clarity >= 95%

# Phase 2 information gathering
Phase 2: Gather comprehensive information.
- `step-1`: Task(subagent_type="Explore") for codebase structure
- `step-2`: Read relevant files
- `step-3`: Search vector memory: mcp__vector-memory__search_memories
- `step-4`: If external context needed: Task(@agent-web-research-master)
- `validation`: Evidence-based content >= 95%

# Phase 3 structure proposal
Phase 3: Propose structure and get approval.
- `step-1`: Design folder hierarchy within .docs/
- `step-2`: Create outline: sections, code examples, diagrams
- `step-3`: Estimate length, plan multi-file split if > 500 lines
- `step-4`: AskUserQuestion for approval
- `validation`: User explicitly approves structure

# Phase 4 writing
Phase 4: Write professional documentation.
- `step-1`: Write first major section
- `step-2`: Use TodoWrite to track progress
- `step-3`: Show first section to user for validation
- `step-4`: Continue based on feedback
- `step-5`: Include: code examples, architecture diagrams (text-based), use cases
- `validation`: Each section <= 500 lines, user validates first section

# Phase 5 finalization
Phase 5: Review, finalize, deliver.
- `step-1`: Final review: 500-line limits, cross-references, completeness
- `step-2`: Create TOC if multi-file
- `step-3`: Present final for approval
- `step-4`: Store insights: mcp__vector-memory__store_memory
- `step-5`: Write files to .docs/
- `validation`: User approves final documentation

# Yaml structure
Required YAML structure: name (required), description (required), part/type/date/version (optional).
- ---
name: "Document Title"
description: "Brief description"
part: 1
type: "guide"
date: "2025-11-20"
version: "1.0.0"
---

# Professional writing
Technical writing standards.
- Clear, concise language
- Logical structure with proper hierarchy
- Code examples with context (minimal, only when cheaper than text)
- Text-based architecture diagrams
- Cross-references to related docs
- Proper markdown with syntax highlighting

# File naming
Naming conventions: lowercase with hyphens, no spaces.
- Single: topic-name.md
- Multi-part: topic-name-part-1.md, topic-name-part-2.md
- Index: README.md for folder overview

# Cross referencing
Use relative paths for cross-references.
- [See Part 2](./topic-name-part-2.md)
- [Related concept](../concepts/delegation.md)

# Tool integration
Tool usage patterns.
- Explore: Task(subagent_type="Explore", prompt="...")
- Web research: Task(@agent-web-research-master, "...")
- Memory search: mcp__vector-memory__search_memories
- Memory store: mcp__vector-memory__store_memory

# Directive
Ask constantly! Explore thoroughly! Validate frequently! Write professionally!

</command>