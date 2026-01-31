<?php

declare(strict_types=1);

namespace BrainNode\Commands\Do;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Do\DoAsyncInclude;

#[Meta('id', 'do:async')]
#[Meta('description', 'Multi-agent orchestration command for flexible task execution (sequential/parallel) with user approval gates')]
#[Purpose('Coordinates flexible agent execution (sequential by default, parallel when beneficial) with approval checkpoints and comprehensive vector memory integration. Agents communicate through vector memory for knowledge continuity. Accepts $ARGUMENTS task description. Zero distractions, atomic tasks only, strict plan adherence.')]
#[Includes(DoAsyncInclude::class)]
class AsyncCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
