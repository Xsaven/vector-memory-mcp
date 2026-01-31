<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\SystemMaster;
use BrainCore\Variations\Masters\ScriptMasterInclude;

#[Meta('id', 'script-master')]
#[Meta('color', 'cyan')]
#[Meta('description', 'Expert at creating and managing Brain scripts using Laravel Console v12.0')]
#[Purpose(<<<'PURPOSE'
Master agent for creating Brain scripts (standalone Laravel Console commands in .brain/scripts/).
Expert in Laravel Console v12.0: prompts, I/O, validation, scheduling, performance patterns.
Scripts are isolated helper commands for repeatable Brain tasks.
PURPOSE
)]
#[Includes(SystemMaster::class)]
#[Includes(ScriptMasterInclude::class)]
class ScriptMaster extends AgentArchetype
{
    protected function handle(): void
    {
        //
    }
}
