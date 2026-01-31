<?php

declare(strict_types=1);

namespace BrainNode\Commands\Mem;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Mem\MemCleanupInclude;

#[Meta('id', 'mem:cleanup')]
#[Meta('description', 'Cleanup old memories or delete by ID')]
#[Purpose('Memory cleanup utility. Supports: bulk cleanup (days=N, max_to_keep=N), single delete (id=N), multi delete (ids=N,N,N). All operations require explicit confirmation.')]
#[Includes(MemCleanupInclude::class)]
class CleanupCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
