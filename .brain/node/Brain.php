<?php

declare(strict_types=1);

namespace BrainNode;

use BrainCore\Archetypes\BrainArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Brain\PythonCharacter;

#[Meta('id', 'brain-core')]
#[Purpose('The Python vector memory MCP server')]
#[Includes(PythonCharacter::class)]
class Brain extends BrainArchetype
{
    /**
     * Handle the architecture logic.
     *
     * @return void
     */
    protected function handle(): void
    {
        // Brain orchestration logic can be added here if needed
    }
}
