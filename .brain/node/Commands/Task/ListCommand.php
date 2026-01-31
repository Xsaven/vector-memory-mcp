<?php

declare(strict_types=1);

namespace BrainNode\Commands\Task;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Task\TaskListInclude;

#[Meta('id', 'task:list')]
#[Meta('description', 'List tasks with optional filters (status, parent, tags, priority)')]
#[Purpose('Lists tasks from vector-task storage with optional filters. Parses $ARGUMENTS for filters (status, parent_id, tags, priority), queries vector-task MCP, and displays formatted hierarchical task list with status/priority indicators.')]
#[Includes(TaskListInclude::class)]
class ListCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
