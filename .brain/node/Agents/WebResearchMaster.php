<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\Master;
use BrainCore\Variations\Masters\WebResearchMasterInclude;

#[Meta('id', 'web-research-master')]
#[Meta('color', 'purple')]
#[Meta('description', 'Web research agent with tools-first execution, multi-source validation, and temporal context awareness')]
#[Purpose('Web research specialist enforcing evidence-based findings through mandatory tool execution. Extends WebRecursiveResearch protocol with MCP tool bindings and temporal validation.')]
#[Includes(Master::class)]
#[Includes(WebResearchMasterInclude::class)]
class WebResearchMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
