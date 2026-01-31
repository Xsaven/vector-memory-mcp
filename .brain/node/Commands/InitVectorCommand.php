<?php

declare(strict_types=1);

namespace BrainNode\Commands;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\InitVectorInclude;

#[Meta('id', 'init-vector')]
#[Meta('description', 'Systematically initialize vector memory by scanning entire project through sequential ExploreMaster agents')]
#[Purpose('Systematically scan and document entire project into vector memory. Sequential ExploreMaster agents explore logical areas, communicating through vector memory for continuity. Each agent searches memory before exploring, stores findings after. Enables comprehensive project knowledge base for all agents.')]
#[Includes(InitVectorInclude::class)]
class InitVectorCommand extends CommandArchetype
{
    /**
     * Handle the command logic.
     */
    protected function handle(): void
    {

    }
}
