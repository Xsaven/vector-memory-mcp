<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\Master;
use BrainCore\Variations\Masters\VectorMasterInclude;

#[Meta('id', 'vector-master')]
#[Meta('color', 'orange')]
#[Meta('description', 'Deep vector memory research via recursive semantic search')]
#[Purpose('Vector Memory Executor responsible for direct execution of memory tools (search_memories, store_memory, list_recent_memories). This agent performs evidence-based memory research and storage with strict tools-only compliance (no delegation).')]
#[Includes(Master::class)]
#[Includes(VectorMasterInclude::class)]
class VectorMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
