<?php

declare(strict_types=1);

namespace BrainScripts;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class HelloScript extends Command
{
    protected $signature = 'hello';

    protected $description = 'Command description';

    public function handle(): void
    {
        $this->info(getcwd());
    }
}
