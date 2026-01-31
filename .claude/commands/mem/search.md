---
name: "mem:search"
description: "Semantic search memories with optional filters"
---

<command>
<meta>
<id>mem:search</id>
<description>Semantic search memories with optional filters</description>
</meta>
<execute>Searches memories using semantic similarity from $ARGUMENTS query. Supports filters: category, limit, offset, tags. Displays results with similarity scores.</execute>
<provides>Semantic memory search with flexible filters. Displays results with similarity scores and content previews.</provides>
<guidelines>

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($SEARCH_QUERY = {search query extracted from $RAW_INPUT})

# Role
Semantic memory search utility that queries vector storage with optional filters and displays formatted results with similarity scores.

# Workflow step1
STEP 1 - Parse Arguments for Query and Filters
- `format-1`: Simple query: /mem:search "authentication patterns"
- `format-2`: With filters: /mem:search query="auth" category=code-solution limit=20
- `format-3`: With tags: /mem:search query="api" tags=laravel,php
- `extract`: STORE-AS($QUERY = {parse query from $RAW_INPUT, required})
- `filters`: STORE-AS($FILTERS = {parse category?, limit?, offset?, tags? from $RAW_INPUT})
- `defaults`: Defaults: limit=10, offset=0
- `output`: STORE-AS($PARAMS = {query: $QUERY, ...$FILTERS})

# Workflow step2
STEP 2 - Execute Semantic Search
- `search`: mcp__vector-memory__search_memories('STORE-GET($PARAMS)')
- `store`: STORE-AS($RESULTS = search results array)

# Workflow step3
STEP 3 - Handle Empty Results
- `check`: IF(STORE-GET($RESULTS) is empty) →
  Display: "No memories found for: {query}" → Suggest: "Try broader search terms" → Suggest: "Remove category/tag filters" → Suggest: "Use /mem:list to see recent memories"
→ END-IF

# Workflow step4
STEP 4 - Format and Display Results
- `header`: Display: "--- Memory Search Results ---"
- `meta`: Display: "Query: {query} | Found: {count} | Category: {category or all}"
- `list`: FOREACH(memory in STORE-GET($RESULTS)) →
  Display: "#{id} [{category}] (similarity: {score})" → Display: "  {content_preview} (first 100 chars)" → Display: "  Tags: {tags} | Accessed: {access_count}x"
→ END-FOREACH
- `pagination`: IF(more results available (total > limit + offset)) →
  Display: "More results available. Use offset={next_offset} to see more"
→ END-IF

# Output format
Result display format
- --- Memory Search Results ---
- Query: "auth patterns" | Found: 5 | Category: code-solution
- #{id} [{category}] (similarity: 0.85)
-   Content preview here...
-   Tags: php, laravel | Accessed: 3x
- More results available. Use offset=10 to see more

# Similarity guide
Similarity score interpretation
- 0.90-1.00: Highly relevant, almost exact match
- 0.75-0.89: Relevant, good semantic match
- 0.50-0.74: Somewhat related, partial match
- < 0.50: Weak match, may not be useful

# Filter examples
Supported filter combinations
- /mem:search "query" → simple search
- /mem:search query="auth" category=bug-fix → filtered by category
- /mem:search query="api" tags=laravel → filtered by tag
- /mem:search query="cache" limit=20 → more results
- /mem:search query="db" offset=10 limit=10 → pagination

</guidelines>
</command>