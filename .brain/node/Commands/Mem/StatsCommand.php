<?php

declare(strict_types=1);

namespace BrainNode\Commands\Mem;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Mem\MemStatsInclude;

#[Meta('id', 'mem:stats')]
#[Meta('description', 'Show memory statistics and health')]
#[Purpose('Displays memory statistics: total count, category breakdown, storage usage, health status. Accepts optional filters via $ARGUMENTS: category, tags, top.')]
#[Includes(MemStatsInclude::class)]
class StatsCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
