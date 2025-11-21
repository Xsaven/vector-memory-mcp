<?php

declare(strict_types=1);

namespace BrainNode;

use BrainCore\Archetypes\BrainArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Brain\BrainBasicErrorHandling;
use BrainCore\Includes\Brain\BrainCore;
use BrainCore\Includes\Brain\BrainCoreConstraints;
use BrainCore\Includes\Brain\BrainDelegationWorkflow;
use BrainCore\Includes\Brain\BrainResponseValidation;
use BrainCore\Includes\Brain\BrainScriptMasterDelegation;
use BrainCore\Includes\Brain\DelegationProtocols;
use BrainCore\Includes\Brain\PreActionValidation;
use BrainCore\Includes\Universal\BrainDocsCommand;
use BrainCore\Includes\Universal\BrainScriptsCommand;
use BrainCore\Includes\Universal\VectorMemoryMCP;

#[Meta('id', 'brain-core')]
#[Purpose('<!-- Specify the primary project purpose of this Brain here -->')]

// === UNIVERSAL (Brain runtime essentials) ===
#[Includes(BrainCoreConstraints::class)]                // Simplified constraints for Brain orchestration
#[Includes(VectorMemoryMCP::class)]                     // Vector memory primary knowledge base
#[Includes(BrainDocsCommand::class)]
#[Includes(BrainScriptsCommand::class)]
#[Includes(BrainScriptMasterDelegation::class)]

// === BRAIN ORCHESTRATION (Brain-specific) ===
#[Includes(BrainCore::class)]                           // Foundation + meta
#[Includes(PreActionValidation::class)]                 // Pre-action safety gate
#[Includes(DelegationProtocols::class)]                 // Delegation protocols
#[Includes(BrainDelegationWorkflow::class)]             // Simplified delegation workflow
#[Includes(BrainResponseValidation::class)]             // Agent response validation
#[Includes(BrainBasicErrorHandling::class)]             // Basic error handling
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
