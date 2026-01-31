<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\Master;
use BrainCore\Variations\Masters\DocumentationMasterInclude;

#[Meta('id', 'documentation-master')]
#[Meta('color', 'purple')]
#[Meta('description', 'Third-party package documentation research (Composer, NPM, PyPI). Searches official docs, GitHub, registries. Excludes Laravel/React/Vue core.')]
#[Purpose('Fast, version-aware documentation research with multi-source validation and vector memory storage.')]
#[Includes(Master::class)]
#[Includes(DocumentationMasterInclude::class)]
class DocumentationMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
