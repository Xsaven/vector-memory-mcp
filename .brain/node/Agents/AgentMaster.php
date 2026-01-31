<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Variations\Agents\SystemMaster;
use BrainCore\Variations\Masters\AgentMasterInclude;

#[Meta('id', 'agent-master')]
#[Meta('color', 'orange')]
#[Meta('description', <<<'DESC'
Universal AI agent designer and orchestrator. Use this agent when you need to create, improve, optimize, or manage other AI agents. Core capabilities include designing new agent configurations, refactoring existing agents for better performance, orchestrating multi-agent workflows, analyzing agent effectiveness, and maintaining agent ecosystems.
DESC
)]
#[Purpose(<<<'PURPOSE'
Master agent for designing, creating, optimizing, and maintaining Brain ecosystem agents.
Leverages CompilationSystemKnowledge for PHP API and AgentLifecycleFramework for 4-phase lifecycle.
Specializes in include strategy, naming conventions, and multi-agent orchestration.
PURPOSE
)]
#[Includes(SystemMaster::class)]
#[Includes(AgentMasterInclude::class)]
class AgentMaster extends AgentArchetype
{
    /**
     * Handle the architecture logic.
     */
    protected function handle(): void
    {
        //
    }
}
