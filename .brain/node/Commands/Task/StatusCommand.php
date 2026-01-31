<?php

declare(strict_types=1);

namespace BrainNode\Commands\Task;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Task\TaskStatusInclude;

#[Meta('id', 'task:status')]
#[Meta('description', 'Show task statistics with optional custom query ($ARGUMENTS)')]
#[Purpose('Displays task statistics and progress. Supports custom queries via $ARGUMENTS: time filters (yesterday, today, this week, this month), status filters (completed, pending, in_progress), grouping (by priority, by tags), and specific parent queries (parent_id=N). Empty $ARGUMENTS shows default overview.')]
#[Includes(TaskStatusInclude::class)]
class StatusCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
