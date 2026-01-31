<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\InitTaskInclude;

#[Meta('id', 'init-task')]
#[Meta('description', 'Initialize project tasks from documentation and codebase analysis')]
#[Purpose('Initializes project task hierarchy by scanning documentation (.docs/, README), analyzing codebase structure via Explore agent, decomposing work into root-level tasks with estimates, and creating tasks in vector storage after user approval. Ensures comprehensive project understanding before task creation.')]
#[Includes(InitTaskInclude::class)]
class InitTaskCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
