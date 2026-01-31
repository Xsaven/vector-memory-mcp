<?php

declare(strict_types=1);

namespace BrainNode\Commands\Task;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Task\TaskCreateInclude;

#[Meta('id', 'task:create')]
#[Meta('description', 'Create task from description with analysis and estimation')]
#[Purpose('Creates task(s) from user description provided via $ARGUMENTS. Analyzes relevant materials, searches vector memory for similar past work, estimates time, gets mandatory user approval, creates task(s), and recommends decomposition if estimate >5-8 hours. Golden rule: each task 5-8 hours max.')]
#[Includes(TaskCreateInclude::class)]
class CreateCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
