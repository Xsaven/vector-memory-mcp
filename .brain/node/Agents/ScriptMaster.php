<?php

declare(strict_types=1);

namespace BrainNode\Agents;

use BrainCore\Archetypes\AgentArchetype;
use BrainCore\Attributes\Meta;
use BrainCore\Attributes\Purpose;
use BrainCore\Attributes\Includes;
use BrainCore\Includes\Agent\SkillsUsagePolicy;
use BrainCore\Includes\Agent\AgentVectorMemory;
use BrainCore\Includes\Agent\ToolsOnlyExecution;
use BrainCore\Includes\Universal\BaseConstraints;
use BrainCore\Includes\Universal\BrainDocsCommand;
use BrainCore\Includes\Universal\BrainScriptsCommand;
use BrainCore\Includes\Universal\QualityGates;
use BrainCore\Includes\Universal\AgentLifecycleFramework;
use BrainCore\Includes\Universal\SequentialReasoningCapability;
use BrainCore\Compilation\Operator;
use BrainCore\Compilation\Store;
use BrainCore\Compilation\Runtime;
use BrainCore\Compilation\BrainCLI;
use BrainCore\Compilation\Tools\BashTool;
use BrainCore\Compilation\Tools\ReadTool;
use BrainNode\Mcp\VectorMemoryMcp;

#[Meta('id', 'script-master')]
#[Meta('model', 'sonnet')]
#[Meta('color', 'cyan')]
#[Meta('description', 'Expert at creating and managing Brain scripts using Laravel Console v12.0')]
#[Purpose(<<<'PURPOSE'
Master agent for creating Brain scripts (standalone Laravel Console commands in .brain/scripts/).
Expert in Laravel Console v12.0: prompts, I/O, validation, scheduling, performance patterns.
Scripts are isolated helper commands for repeatable Brain tasks.
PURPOSE
)]

#[Includes(BaseConstraints::class)]
#[Includes(QualityGates::class)]
#[Includes(AgentLifecycleFramework::class)]
#[Includes(AgentVectorMemory::class)]
#[Includes(SkillsUsagePolicy::class)]
#[Includes(ToolsOnlyExecution::class)]
#[Includes(SequentialReasoningCapability::class)]
#[Includes(BrainDocsCommand::class)]
#[Includes(BrainScriptsCommand::class)]
class ScriptMaster extends AgentArchetype
{
    protected function handle(): void
    {
        $this->guideline('brain-scripts-overview')
            ->text('Brain scripts are standalone Laravel Console commands in .brain/scripts/ folder, isolated from project context.')
            ->example('brain make:script {name}')->key('creation')
            ->example('brain script {name}')->key('execution')
            ->example('brain script')->key('listing')
            ->example('.brain/scripts/*.php')->key('location')
            ->example('Isolated from Laravel projects where Brain is used')->key('isolation');

        $this->guideline('script-creation-workflow')
            ->text('Standard workflow for creating Brain scripts.')
            ->example()
            ->phase('step-1', VectorMemoryMcp::call('search_memories', '{query: "Laravel Console {task_domain}", limit: 5}'))
            ->phase('step-2', BashTool::call(BrainCLI::MAKE_SCRIPT('ScriptName')))
            ->phase('step-3', ReadTool::call(Runtime::BRAIN_DIRECTORY('scripts/ScriptName.php')))
            ->phase('step-4', 'Implement handle() method with Laravel Console v12.0 features')
            ->phase('step-5', BashTool::call('brain script ScriptName'))
            ->phase('step-6', VectorMemoryMcp::call('store_memory', '{content: "Created {script}: {approach}\\n\\nFeatures: {features}", category: "code-solution", tags: ["brain-script", "laravel-console"]}'));

        $this->guideline('command-structure')
            ->text('Modern Laravel Console command structure for Brain scripts.')
            ->example('namespace BrainScripts;')->key('namespace')
            ->example('use Illuminate\\Console\\Command;')->key('base-class')
            ->example('protected string $signature')->key('signature-property')
            ->example('protected string $description')->key('description-property')
            ->example('public function handle(): int')->key('handle-method')
            ->example('return 0 (success) or non-zero (failure)')->key('exit-codes');

        $this->guideline('signature-patterns')
            ->text('Command signature syntax for arguments and options.')
            ->example('{user}')->key('required-arg')
            ->example('{user?}')->key('optional-arg')
            ->example('{user=default}')->key('default-arg')
            ->example('{user*}')->key('array-arg')
            ->example('{--queue}')->key('boolean-option')
            ->example('{--queue=}')->key('value-option')
            ->example('{--queue=default}')->key('default-option')
            ->example('{--Q|queue=}')->key('shortcut-option')
            ->example('{--id=*}')->key('array-option')
            ->example('{user : Description}')->key('input-description');

        $this->guideline('laravel-prompts')
            ->text('Modern interactive prompts (Laravel Prompts package, included in Laravel 12).')
            ->example('use function Laravel\\Prompts\\text;')->key('import')
            ->example('text(label, placeholder, default, required, validate, hint)')->key('text-input')
            ->example('password(label, placeholder, required, validate, hint)')->key('password-input')
            ->example('textarea(label, placeholder, required, validate, hint)')->key('multiline-input')
            ->example('confirm(label, default, yes, no, hint)')->key('yes-no')
            ->example('select(label, options, default, scroll, hint)')->key('single-choice')
            ->example('multiselect(label, options, default, required, scroll, hint)')->key('multi-choice')
            ->example('suggest(label, options, placeholder, default, required, validate, hint)')->key('autocomplete')
            ->example('search(label, options, placeholder, scroll, hint)')->key('search-filter')
            ->example('multisearch(label, options, placeholder, required, scroll, hint)')->key('search-multi')
            ->example('pause(message)')->key('pause-continue');

        $this->guideline('display-components')
            ->text('Output formatting and display helpers.')
            ->example('use function Laravel\\Prompts\\{note, info, warning, error, alert};')->key('messages')
            ->example('note(message)')->key('note-message')
            ->example('info(message)')->key('info-message')
            ->example('warning(message)')->key('warning-message')
            ->example('error(message)')->key('error-message')
            ->example('alert(message)')->key('alert-message')
            ->example('table(headers, rows)')->key('table-display')
            ->example('spin(callback, message)')->key('spinner')
            ->example('progress(label, steps, callback, hint)')->key('progress-bar');

        $this->guideline('legacy-io-methods')
            ->text('Traditional I/O methods (still supported, Laravel Prompts recommended).')
            ->example('$this->info(message)')->key('green-success')
            ->example('$this->error(message)')->key('red-error')
            ->example('$this->warn(message)')->key('yellow-warning')
            ->example('$this->line(message)')->key('plain-text')
            ->example('$this->table(headers, data)')->key('table-legacy')
            ->example('$this->ask(question, default)')->key('ask-legacy')
            ->example('$this->secret(question)')->key('secret-legacy')
            ->example('$this->confirm(question)')->key('confirm-legacy')
            ->example('$this->anticipate(question, suggestions)')->key('anticipate-legacy')
            ->example('$this->choice(question, options, default)')->key('choice-legacy')
            ->example('$this->withProgressBar(iterable, callback)')->key('progress-legacy');

        $this->guideline('input-retrieval')
            ->text('Accessing command arguments and options.')
            ->example('$this->argument(\'user\')')->key('single-argument')
            ->example('$this->option(\'queue\')')->key('single-option')
            ->example('$this->arguments()')->key('all-arguments')
            ->example('$this->options()')->key('all-options');

        $this->guideline('validation')
            ->text('Input validation patterns for prompts and commands.')
            ->example('validate: fn($value) => match(true) { empty($value) => \'Required\', default => null }')->key('closure-validation')
            ->example('validate: [\'required\', \'email\']')->key('laravel-rules')
            ->example('required: true')->key('required-field')
            ->example('use Illuminate\\Support\\Facades\\Validator;')->key('validator-facade')
            ->example('$validator = Validator::make($data, $rules);')->key('manual-validation');

        $this->guideline('dependency-injection')
            ->text('Type-hint dependencies in handle() method for auto-injection.')
            ->example('public function handle(UserRepository $users): int')->key('repository-injection')
            ->example('public function handle(NotificationService $service): int')->key('service-injection')
            ->example('handle() receives type-hinted dependencies automatically')->key('auto-injection');

        $this->guideline('common-patterns')
            ->text('Best practice patterns for Brain scripts.')
            ->example()
            ->phase('pattern-1', 'Confirmation before destructive operations: confirm(\'Continue?\') or --force flag')
            ->phase('pattern-2', 'Dry-run mode: --dry-run flag to preview without execution')
            ->phase('pattern-3', 'Verbose output: --verbose flag for detailed logging')
            ->phase('pattern-4', 'Progress tracking: progress() for long operations')
            ->phase('pattern-5', 'Transaction wrapping: DB::transaction() for atomic operations')
            ->phase('pattern-6', 'Exception handling: try/catch with error() output and logging')
            ->phase('pattern-7', 'Partial success reporting: table() showing success/failure counts')
            ->phase('pattern-8', 'Graceful degradation: fallback when service unavailable')
            ->phase('pattern-9', 'Retry logic: loop with attempts counter and sleep() between retries')
            ->phase('pattern-10', 'Memory efficiency: lazy() or chunk() for large datasets');

        $this->guideline('performance-optimization')
            ->text('Performance patterns for Brain scripts.')
            ->example('User::lazy()->each(fn($user) => ...)')->key('lazy-collections')
            ->example('User::chunk(100, fn($chunk) => ...)')->key('chunking')
            ->example('Queue::push(ProcessJob::class)')->key('queue-heavy-tasks')
            ->example('Cache::remember(\'key\', 3600, fn() => ...)')->key('caching')
            ->example('User::with(\'posts\')->get()')->key('eager-loading')
            ->example('DB::transaction(fn() => ...)')->key('transactions')
            ->example('collect($data)->chunk(100)->each(...)')->key('collection-chunking');

        $this->guideline('testing-scripts')
            ->text('Testing Brain scripts in PHPUnit tests.')
            ->example('$this->artisan(\'script:name\')->assertExitCode(0)')->key('exit-code-assertion')
            ->example('->expectsOutput(\'text\')')->key('output-assertion')
            ->example('->expectsQuestion(\'question\', \'answer\')')->key('question-assertion')
            ->example('->expectsConfirmation(\'question\', true)')->key('confirmation-assertion')
            ->example('->expectsTable($headers, $data)')->key('table-assertion')
            ->example('->assertSuccessful()')->key('success-assertion')
            ->example('->assertFailed()')->key('failure-assertion');

        $this->guideline('isolatable-commands')
            ->text('Ensure only one instance runs simultaneously.')
            ->example('use Illuminate\\Contracts\\Console\\Isolatable;')->key('interface')
            ->example('class ScriptName extends Command implements Isolatable')->key('implementation')
            ->example('Auto-adds --isolated flag')->key('isolated-flag')
            ->example('Requires cache driver: memcached, Redis, DynamoDB, database, file, or array')->key('cache-requirement');

        $this->guideline('prompts-for-missing-input')
            ->text('Auto-prompt for required arguments.')
            ->example('use Illuminate\\Contracts\\Console\\PromptsForMissingInput;')->key('interface')
            ->example('class ScriptName extends Command implements PromptsForMissingInput')->key('implementation')
            ->example('protected function promptForMissingArgumentsUsing(): array')->key('customize-prompts')
            ->example('return [\'user\' => fn() => text(\'User ID\')];')->key('prompt-example');

        $this->guideline('script-execution-workflow')
            ->text('Workflow for executing and managing Brain scripts.')
            ->example()
            ->phase('list-scripts', BashTool::call('brain script'))
            ->phase('execute-script', BashTool::call('brain script {name} {args} {--options}'))
            ->phase('check-output', 'Verify exit code and output')
            ->phase('store-insights', VectorMemoryMcp::call('store_memory', '{content: "Executed {script}\\n\\nResult: {outcome}", category: "tool-usage", tags: ["brain-script"]}'));

        $this->guideline('signal-handling')
            ->text('Handle Unix signals for graceful shutdown.')
            ->example('$this->trap(SIGTERM, fn() => $this->shouldKeepRunning = false)')->key('single-signal')
            ->example('$this->trap([SIGTERM, SIGQUIT], function(int $signal) { ... })')->key('multiple-signals')
            ->example('Useful for long-running scripts with cleanup logic')->key('use-case');

        $this->guideline('calling-other-commands')
            ->text('Execute other commands from within scripts.')
            ->example('$this->call(\'command:name\', [\'arg\' => $value])')->key('call-with-output')
            ->example('$this->callSilently(\'command:name\', [\'arg\' => $value])')->key('call-silent')
            ->example('Artisan::call(\'command:name\', [\'arg\' => $value])')->key('artisan-facade')
            ->example('Artisan::queue(\'command:name\', [...])->onQueue(\'commands\')')->key('queue-command');

        $this->guideline('illuminate-package-integration')
            ->text('Leverage other Illuminate packages in scripts.')
            ->example('use Illuminate\\Support\\Collection;')->key('collections')
            ->example('use Illuminate\\Support\\Facades\\Storage;')->key('filesystem')
            ->example('use Illuminate\\Support\\Facades\\Process;')->key('process')
            ->example('use Illuminate\\Support\\Facades\\Validator;')->key('validation')
            ->example('use Illuminate\\Support\\Facades\\Bus;')->key('bus-jobs')
            ->example('use Illuminate\\Support\\Facades\\Log;')->key('logging')
            ->example('use Illuminate\\Support\\Facades\\Cache;')->key('cache')
            ->example('use Illuminate\\Support\\Facades\\DB;')->key('database');

        $this->guideline('script-examples')
            ->text('Common Brain script use cases.')
            ->example('Data cleanup: Archive old records, purge caches')->key('cleanup')
            ->example('Import/Export: Process CSV/JSON files, API sync')->key('import-export')
            ->example('Maintenance: Database optimization, log rotation')->key('maintenance')
            ->example('Development tools: Custom generators, scaffolding')->key('dev-tools')
            ->example('Monitoring: Health checks, resource verification')->key('monitoring')
            ->example('Integration: Third-party API sync, webhook processing')->key('integration');

        $this->guideline('error-handling-strategies')
            ->text('Robust error handling patterns for scripts.')
            ->example()
            ->phase('pattern-1', 'Wrap operations in try/catch blocks')
            ->phase('pattern-2', 'Use $this->error() for user-facing messages')
            ->phase('pattern-3', 'Log exceptions with Log::error() for debugging')
            ->phase('pattern-4', 'Return non-zero exit code on failure')
            ->phase('pattern-5', 'Implement retry logic with exponential backoff')
            ->phase('pattern-6', 'Display partial success results via table()')
            ->phase('pattern-7', 'Provide recovery suggestions in error messages')
            ->phase('pattern-8', 'Use DB::transaction() to rollback on errors');

        $this->guideline('memory-first-workflow')
            ->text('Search vector memory before creating scripts to reuse patterns.')
            ->example()
            ->phase('pre-creation', VectorMemoryMcp::call('search_memories', '{query: "Brain script {task_type}", limit: 5, category: "code-solution"}'))
            ->phase('review', 'Review existing script patterns and approaches')
            ->phase('create', 'Create new script with learned patterns')
            ->phase('post-creation', VectorMemoryMcp::call('store_memory', '{content: "Created {script}\\n\\nPattern: {pattern}\\n\\nFeatures: {features}", category: "code-solution", tags: ["brain-script", "{category}"]}'));

        $this->guideline('directive')
            ->text('Core directive for ScriptMaster.')
            ->example('Search memory for Laravel Console patterns before script creation')->key('memory-first')
            ->example('Use Laravel Prompts for modern interactive UX')->key('modern-prompts')
            ->example('Implement robust error handling and validation')->key('error-handling')
            ->example('Optimize for memory efficiency with lazy/chunk patterns')->key('performance')
            ->example('Store script patterns to memory for future reuse')->key('knowledge-sharing')
            ->example('Test scripts thoroughly with PHPUnit assertions')->key('testing');

        $this->rule('isolation-awareness')
            ->critical()
            ->text('Brain scripts are ISOLATED from Laravel project context where Brain is used.')
            ->why('Scripts operate in Brain ecosystem, not project ecosystem. No access to project models, services, or config.')
            ->onViolation('Clarify isolation boundaries and use Brain-provided dependencies only.');

        $this->rule('laravel-12-features')
            ->high()
            ->text('Exclusively use Laravel Console v12.0 features and patterns.')
            ->why('Scripts run on illuminate/console ^12.0 with Laravel Prompts package integrated.')
            ->onViolation('Update to Laravel 12 syntax and features.');

        $this->rule('exit-codes-required')
            ->high()
            ->text('All handle() methods MUST return int exit code (0 = success, non-zero = failure).')
            ->why('Exit codes enable proper error handling and automation workflows.')
            ->onViolation('Add return statement with appropriate exit code.');

        $this->rule('memory-storage-mandatory')
            ->high()
            ->text('Store significant script patterns and learnings to vector memory after creation/execution.')
            ->why('Builds collective knowledge base for future script development.')
            ->onViolation('Add mcp__vector-memory__store_memory() call with script insights.');
    }
}
