---
description: "Show memory statistics and health"
---

<command>
<meta>
<id>mem:stats</id>
<description>Show memory statistics and health</description>
</meta>
<execute>Displays memory statistics: total count, category breakdown, storage usage, health status. Accepts optional filters via $ARGUMENTS: category, tags, top.</execute>
<provides>Displays memory statistics and health information with optional category/tag filtering.</provides>
<guidelines>

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)

# Role
Memory statistics utility that displays storage health, category breakdown, and usage metrics.

# Workflow step1
STEP 1 - Parse Arguments for Filters
- `parse`: Parse STORE-GET($RAW_INPUT) for filter parameters
- `format-1`: /mem:stats → full statistics overview
- `format-2`: /mem:stats category=code-solution → category-specific stats
- `format-3`: /mem:stats tags=php → tag-specific stats
- `format-4`: /mem:stats top=10 → top accessed memories
- `output`: STORE-AS($FILTER = {parse filter type and value from STORE-GET($RAW_INPUT): default|category|tags|top})

# Workflow step2
STEP 2 - Fetch Memory Statistics
- `fetch`: mcp__vector-memory__get_memory_stats('{}')
- `store`: STORE-AS($STATS = statistics object)

# Workflow step3
STEP 3 - Display Default Overview (when no filter)
- `check`: IF(STORE-GET($FILTER).type === "default") →
  Display: "--- Memory Statistics ---" → Display: "Total: {total} memories" → Display: "Limit: {memory_limit} | Usage: {usage_percentage}%" → Display: "Database: {database_size_mb} MB" → Display: "" → Display: "Categories:" → ForEach category: "  {name}: {count}" → Display: "" → Display: "Health: {health_status}" → Display: "Recent week: {recent_week_count} new"
→ END-IF

# Workflow step4
STEP 4 - Category-Specific Stats
- `check`: IF(STORE-GET($FILTER).type === "category") →
  mcp__vector-memory__search_memories('{query: "*", category: "{category}", limit: 50}') → Calculate: count, avg access, date range → Display: "--- Category: {category} ---" → Display: "Count: {count} memories" → Display: "Avg access: {avg_access}x" → Display: "Date range: {oldest} to {newest}"
→ END-IF

# Workflow step5
STEP 5 - Tag-Specific Stats
- `check`: IF(STORE-GET($FILTER).type === "tags") →
  mcp__vector-memory__get_unique_tags('{}') → Display: "--- All Tags ---" → ForEach tag: "{tag}: {count} memories" → Sort by count descending
→ END-IF

# Workflow step6
STEP 6 - Top Accessed Memories
- `check`: IF(STORE-GET($FILTER).type === "top") →
  Extract top_accessed from STORE-GET($STATS) → Display: "--- Top Accessed Memories ---" → ForEach memory: "#{id} ({access_count}x) {preview}"
→ END-IF

# Output format
Statistics display format
- --- Memory Statistics ---
- Total: 37 memories
- Limit: 2000000 | Usage: 0%
- Database: 1.65 MB
- code-solution: 16
- Health: Healthy
- Recent week: 5 new

# Usage examples
Command usage patterns
- /mem:stats → full overview
- /mem:stats category=bug-fix → category breakdown
- /mem:stats tags → all tags with counts
- /mem:stats top=5 → top 5 accessed

</guidelines>
</command>