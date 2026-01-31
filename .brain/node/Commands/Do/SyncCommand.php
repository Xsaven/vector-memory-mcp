<?php

declare(strict_types=1);

namespace BrainNode\Commands\Do;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Do\DoSyncInclude;

#[Meta('id', 'do:sync')]
#[Meta('description', 'Direct execution command - Brain executes tasks directly without agent delegation')]
#[Purpose('Direct synchronous task execution by Brain without agent delegation. Uses Read/Edit/Write/Glob/Grep tools directly. Single approval gate. Best for: simple tasks, quick fixes, single-file changes, when agent overhead is unnecessary. Accepts $ARGUMENTS task description. Zero distractions, atomic execution, strict plan adherence.')]
#[Includes(DoSyncInclude::class)]
class SyncCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
