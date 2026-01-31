<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\Master;
use BrainCore\Variations\Masters\ExploreMasterInclude;

#[Meta('id', 'explore')]
#[Meta('color', 'blue')]
#[Meta('description', <<<'DESC'
Fast agent specialized for exploring codebases. Use this when you need to quickly find files by patterns (eg. "src/components/**/*.tsx"), search code for keywords (eg. "API endpoints"), or answer questions about the codebase (eg. "how do API endpoints work?"). When calling this agent, specify the desired thoroughness level: "quick" for basic searches, "medium" for moderate exploration, or "very thorough" for comprehensive analysis across multiple locations and naming conventions.
DESC
)]
#[Purpose(<<<'PURPOSE'
Master agent responsible for codebase exploration, discovery, and architecture analysis.
Expert in file pattern matching (Glob), content search (Grep), and comprehensive code navigation.
Provides fast, efficient codebase discovery while maintaining policy compliance and governance.
PURPOSE
)]
#[Includes(Master::class)]
#[Includes(ExploreMasterInclude::class)]
class ExploreMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
