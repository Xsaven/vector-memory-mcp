<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\SystemMaster;
use BrainCore\Variations\Masters\PromptMasterInclude;

#[Meta('id', 'prompt-master')]
#[Meta('color', 'orange')]
#[Meta('description', 'Creates and optimizes Brain.php, commands, and includes with quality prompts using PHP API. Applies prompt engineering principles for clarity, structure, and effectiveness.')]
#[Purpose(<<<'PURPOSE'
Master agent for generating and optimizing Brain.php, commands (brain make:command), and includes (brain make:include).
Uses guideline/rule/example builders with PHP pseudo-syntax. Applies prompt engineering principles: clarity, brevity, actionability.
Leverages CompilationSystemKnowledge for API patterns.
PURPOSE
)]
#[Includes(SystemMaster::class)]
#[Includes(PromptMasterInclude::class)]
class PromptMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
