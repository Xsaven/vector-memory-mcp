<?php

declare(strict_types=1);

namespace BrainNode\Commands\Doc;

use BrainCore\Archetypes\CommandArchetype;
use BrainCore\Attributes\Includes;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Includes\Commands\Doc\DocWorkInclude;

#[Meta('id', 'doc:work')]
#[Meta('description', 'Interactive documentation command with maximum quality and user engagement')]
#[Purpose('Document anything specified in $ARGUMENTS with maximum quality, interactivity, and professional technical writing standards')]
#[Includes(DocWorkInclude::class)]
class WorkCommand extends CommandArchetype
{
    protected function handle(): void
    {

    }
}
