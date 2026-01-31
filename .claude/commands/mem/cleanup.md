---
name: "mem:cleanup"
description: "Cleanup old memories or delete by ID"
---

<command>
<meta>
<id>mem:cleanup</id>
<description>Cleanup old memories or delete by ID</description>
</meta>
<execute>Memory cleanup utility. Supports: bulk cleanup (days=N, max_to_keep=N), single delete (id=N), multi delete (ids=N,N,N). All operations require explicit confirmation.</execute>
<provides>Memory cleanup utility for bulk deletion by age/count or specific ID deletion. Requires confirmation for destructive operations.</provides>
<guidelines>

# Role
Memory cleanup utility that handles bulk deletion by age/count or specific ID deletion. All destructive operations require explicit confirmation.

# Input
STORE-AS($RAW_INPUT = $ARGUMENTS)
STORE-AS($CLEANUP_PARAMS = {cleanup parameters extracted from $RAW_INPUT: days_old, max_to_keep, or memory_id})

# Workflow step1
STEP 1 - Parse $RAW_INPUT for Operation Type
- `format-1`: /mem:cleanup → preview default cleanup (30 days, keep 1000)
- `format-2`: /mem:cleanup days=60 → cleanup older than 60 days
- `format-3`: /mem:cleanup max_to_keep=500 → keep only 500 most recent
- `format-4`: /mem:cleanup id=15 → delete specific memory
- `format-5`: /mem:cleanup ids=15,16,17 → delete multiple specific
- `detect`: STORE-AS($MODE = {detect mode: bulk|single|multi from STORE-GET($RAW_INPUT)})
- `extract-ids`: STORE-AS($DELETE_IDS = {extract id/ids from STORE-GET($RAW_INPUT) if present})
- `extract-params`: Use STORE-GET($CLEANUP_PARAMS) for days_old, max_to_keep values

# Workflow step2
STEP 2 - Handle Single ID Delete
- `check`: IF(STORE-GET($MODE) === "single") →
  Use ID from STORE-GET($DELETE_IDS) → mcp__vector-memory__get_by_memory_id('{memory_id: {id}}') → STORE-AS($TARGET = memory to delete) → Display: "--- Memory to Delete ---" → Display: "ID: {id}" → Display: "Category: {category}" → Display: "Content: {content_preview}" → Display: "Tags: {tags}" → Display: "" → Prompt: "DELETE this memory? This cannot be undone. (yes/no)"
→ END-IF

# Workflow step3
STEP 3 - Handle Multiple IDs Delete
- `check`: IF(STORE-GET($MODE) === "multi") →
  Use IDs array from STORE-GET($DELETE_IDS) → ForEach ID: fetch memory preview → Display: "--- Memories to Delete ({count}) ---" → ForEach: "#{id} [{category}] {preview}" → Display: "" → Prompt: "DELETE all {count} memories? This cannot be undone. (yes/no)"
→ END-IF

# Workflow step4
STEP 4 - Bulk Cleanup Preview
- `check`: IF(STORE-GET($MODE) === "bulk") →
  Parse: days_old (default 30), max_to_keep (default 1000) → mcp__vector-memory__get_memory_stats('{}') → Calculate: how many would be deleted → Display: "--- Cleanup Preview ---" → Display: "Current total: {total} memories" → Display: "Settings: days_old={days}, max_to_keep={max}" → Display: "Would delete: ~{estimate} memories" → Display: "Would keep: ~{remaining} memories" → Display: "" → Prompt: "Proceed with cleanup? (yes/no)"
→ END-IF

# Workflow step5
STEP 5 - Execute After Confirmation
- `validate`: VALIDATE(User response is YES, DELETE, or CONFIRM) → FAILS → Abort: "Cleanup cancelled."
- `execute-single`: IF(STORE-GET($MODE) === "single") →
  mcp__vector-memory__delete_by_memory_id('{memory_id: {id}}') → Display: "Memory #{id} deleted successfully."
→ END-IF
- `execute-multi`: IF(STORE-GET($MODE) === "multi") →
  ForEach ID: mcp__vector-memory__delete_by_memory_id('{memory_id: {id}}') → Display: "Deleted {count} memories successfully."
→ END-IF
- `execute-bulk`: IF(STORE-GET($MODE) === "bulk") →
  mcp__vector-memory__clear_old_memories('{days_old: {days}, max_to_keep: {max}}') → Display: "Cleanup `completed`. Removed {count} old memories."
→ END-IF

# Output format
Cleanup display format
- --- Cleanup Preview ---
- --- Memory to Delete ---
- Current total: 37 memories
- Would delete: ~12 memories
- DELETE this memory? This cannot be undone. (yes/no)
- Proceed with cleanup? (yes/no)
- Cleanup cancelled.
- Memory #15 deleted successfully.
- Cleanup `completed`. Removed 12 old memories.

# Usage examples
Command usage patterns
- /mem:cleanup → preview default cleanup
- /mem:cleanup days=60 → older than 60 days
- /mem:cleanup max_to_keep=500 → limit to 500
- /mem:cleanup id=15 → delete specific memory
- /mem:cleanup ids=15,16,17 → delete multiple

</guidelines>

# Iron Rules
## Mandatory-confirmation (CRITICAL)
ALL delete operations MUST require explicit user confirmation
- **why**: Deletion is permanent and cannot be undone
- **on_violation**: Show preview, ask for YES/DELETE confirmation, never auto-delete

## Show-preview (HIGH)
MUST show what will be deleted before confirmation
- **why**: User must understand impact before confirming
- **on_violation**: Display count, preview content, then ask confirmation

</command>