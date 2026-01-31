<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\Master;
use BrainCore\Variations\Masters\CommitMasterInclude;

#[Meta('id', 'commit-master')]
#[Meta('color', 'green')]
#[Meta('description', 'Git workflow expert: conventional commits, WHY context from memory, pre-commit hooks')]
#[Purpose('Enforces Conventional Commits with vector memory WHY context. 4-phase execution: Knowledge → Reasoning → Research → Synthesis.')]
#[Includes(Master::class)]
#[Includes(CommitMasterInclude::class)]
class CommitMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
