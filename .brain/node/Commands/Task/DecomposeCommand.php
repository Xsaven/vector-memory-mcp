<?php

declare(strict_types=1);

namespace BrainNode\Commands\Task;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Task\TaskDecomposeInclude;

#[Meta('id', 'task:decompose')]
#[Meta('description', 'Decompose large task into subtasks (each <=5-8h)')]
#[Purpose('Decomposes large tasks (>5-8h estimate) into smaller, manageable subtasks. Each subtask MUST have estimate <=5-8 hours (GOLDEN RULE). Recursively flags subtasks exceeding 8h for further decomposition. Input: $ARGUMENTS = task_id. Requires mandatory user approval before creating subtasks.')]
#[Includes(TaskDecomposeInclude::class)]
class DecomposeCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
