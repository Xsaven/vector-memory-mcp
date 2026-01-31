---
name: "mem:list"
description: "List recent memories chronologically"
---

<command>
<meta>
<id>mem:list</id>
<description>List recent memories chronologically</description>
</meta>
<execute>Lists recent memories in chronological order. Accepts optional limit parameter via $ARGUMENTS (default 10, max 50). Shows previews with category and tags.</execute>
<provides>Lists recent memories in chronological order with content previews and metadata.</provides>
<guidelines>

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($LIST_LIMIT = {numeric limit extracted from $RAW_INPUT, default 10})

# Role
Simple memory listing utility that displays recent memories chronologically with previews and metadata.

# Workflow step1
STEP 1 - Parse Arguments for Limit
- `format`: /mem:list OR /mem:list limit=20
- `extract`: STORE-AS($LIMIT = {parse limit from $RAW_INPUT, default 10, max 50})
- `validate`: IF($LIMIT > 50) → Set $LIMIT = 50 (max allowed)

# Workflow step2
STEP 2 - Fetch Recent Memories
- `fetch`: mcp__vector-memory__list_recent_memories('{limit: STORE-GET($LIMIT)}')
- `store`: STORE-AS($MEMORIES = recent memories array)

# Workflow step3
STEP 3 - Handle No Memories
- `check`: IF(STORE-GET($MEMORIES) is empty) →
  Display: "No memories stored yet." → Suggest: "Use /mem:store to add your first memory"
→ END-IF

# Workflow step4
STEP 4 - Format and Display
- `header`: Display: "--- Recent Memories ({count}) ---"
- `list`: FOREACH(memory in STORE-GET($MEMORIES)) →
  Display: "#{id} [{category}] {created_at}" → Display: "  {content_preview} (first 80 chars)..." → Display: "  Tags: {tags}"
→ END-FOREACH
- `footer`: Display: "Use /mem:get {id} to view full content"

# Output format
Display format for memory list
- --- Recent Memories (10) ---
- #{id} [{category}] 2025-11-22
-   Content preview here...
-   Tags: php, laravel, auth
- Use /mem:get {id} to view full content

# Usage examples
Command usage patterns
- /mem:list → last 10 memories
- /mem:list limit=20 → last 20 memories
- /mem:list limit=50 → maximum allowed

</guidelines>
</command>