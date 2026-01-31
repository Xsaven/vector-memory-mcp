<?php

declare(strict_types=1);

namespace BrainNode\Commands\Mem;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Mem\MemStoreInclude;

#[Meta('id', 'mem:store')]
#[Meta('description', 'Store memory with analysis, category, and tags')]
#[Purpose('Stores new memory from $ARGUMENTS content. Analyzes content, checks for duplicates, suggests category/tags, and requires user approval before storing.')]
#[Includes(MemStoreInclude::class)]
class StoreCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
