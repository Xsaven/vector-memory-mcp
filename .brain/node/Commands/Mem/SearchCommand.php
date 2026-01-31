<?php

declare(strict_types=1);

namespace BrainNode\Commands\Mem;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Mem\MemSearchInclude;

#[Meta('id', 'mem:search')]
#[Meta('description', 'Semantic search memories with optional filters')]
#[Purpose('Searches memories using semantic similarity from $ARGUMENTS query. Supports filters: category, limit, offset, tags. Displays results with similarity scores.')]
#[Includes(MemSearchInclude::class)]
class SearchCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
