---
description: "Store memory with analysis, category, and tags"
---

<command>
<meta>
<id>mem:store</id>
<description>Store memory with analysis, category, and tags</description>
</meta>
<execute>Stores new memory from $ARGUMENTS content. Analyzes content, checks for duplicates, suggests category/tags, and requires user approval before storing.</execute>
<provides>Memory storage specialist that analyzes content, detects duplicates, suggests category/tags, and stores after user approval.</provides>

# Iron Rules
## Analyze-content (CRITICAL)
MUST analyze STORE-GET($RAW_INPUT) content before storing
- **why**: Content analysis ensures proper categorization and prevents garbage storage
- **on_violation**: Parse STORE-GET($RAW_INPUT), extract content, determine domain and type

## Check-duplicates (HIGH)
MUST search for similar memories before storing
- **why**: Prevents duplicate entries and wasted storage
- **on_violation**: Execute mcp__vector-memory__search_memories('{query: "{content_summary}", limit: 3}')

## Mandatory-approval (CRITICAL)
MUST get user approval before storing memory
- **why**: User must validate content, category, and tags before committing
- **on_violation**: Present memory specification and wait for YES/APPROVE


# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($MEMORY_CONTENT = {content to store extracted from $RAW_INPUT})

# Role
Memory storage specialist that analyzes content, checks for duplicates, suggests appropriate category and tags, and stores memory after user approval.

# Workflow step1
STEP 1 - Parse STORE-GET($RAW_INPUT)
- `format-1`: Direct content: /mem:store "This is the memory content"
- `format-2`: With params: /mem:store content="..." category=code-solution tags=php,laravel
- `extract`: Extract from STORE-GET($RAW_INPUT): content (required), category (optional), tags (optional)
- `derive-content`: STORE-AS($CONTENT = {extract content from STORE-GET($RAW_INPUT)})
- `derive-category`: STORE-AS($CATEGORY = {extract category from STORE-GET($RAW_INPUT) if provided})
- `derive-tags`: STORE-AS($TAGS = {extract tags from STORE-GET($RAW_INPUT) if provided})
- `output`: STORE-AS($INPUT = {STORE-GET($CONTENT), STORE-GET($CATEGORY)?, STORE-GET($TAGS)?})

# Workflow step2
STEP 2 - Search for Similar Memories
- `summarize`: Create short summary (20-30 words) of content for search
- `search`: mcp__vector-memory__search_memories('{query: "{summary}", limit: 5}')
- `analyze`: IF(similar memories found with similarity > 0.8) →
  WARN: Similar memory exists (ID: {id}, similarity: {score}) → Show: "{content_preview}" → Ask: "Continue storing? (yes/no/update existing)"
→ ELSE →
  STORE-AS($DUPLICATES = none)
→ END-IF

# Workflow step3
STEP 3 - Analyze Content and Suggest Category/Tags
- `detect-category`: IF(STORE-GET($CATEGORY) is empty) →
  Analyze content domain and type → Suggest category based on content analysis
→ END-IF
- `detect-tags`: IF(STORE-GET($TAGS) is empty) →
  Extract key topics, technologies, concepts → Suggest 3-5 relevant tags
→ END-IF
- `output`: STORE-AS($SUGGESTION = {category, tags, reasoning})

# Workflow step4
STEP 4 - Present Memory for User Approval (MANDATORY)
- `display-1`: --- Memory to Store ---
- `display-2`: Content: {content_preview} ({char_count} chars)
- `display-3`: Category: {category}
- `display-4`: Tags: {tags}
- `display-5`: IF(STORE-GET($DUPLICATES) !== none) →
  WARNING: Similar memories exist!
→ END-IF
- `prompt`: Store this memory? (yes/no/modify)
- `gate`: VALIDATE(User response is YES, APPROVE, Y, or CONFIRM) → FAILS → Wait for explicit approval. Allow modifications if requested.

# Workflow step5
STEP 5 - Store Memory After Approval
- `store`: mcp__vector-memory__store_memory({
                content: "STORE-GET($INPUT).content",
                category: "STORE-GET($SUGGESTION).category",
                tags: STORE-GET($SUGGESTION).tags
            })
- `confirm`: Display: "Memory stored successfully"

# Categories
Available memory categories
- Implementations, patterns, working solutions
- Resolved issues, root causes, fixes applied
- Design decisions, system structure, trade-offs
- Insights, discoveries, lessons learned
- Workflows, tool patterns, configurations
- Debug approaches, troubleshooting steps
- Optimizations, benchmarks, metrics
- Security patterns, vulnerabilities, fixes
- Anything that does not fit other categories

# Tag guidelines
Tag naming conventions
- php, laravel, javascript, python, go
- api, database, auth, cache, queue
- react, vue, tailwind, livewire
- docker, nginx, redis, mysql
- testing, ci-cd, deployment, monitoring

</command>