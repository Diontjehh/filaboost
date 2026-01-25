<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CodeFix extends Command
{
    protected $signature = 'code:fix 
        {--dry-run : Show what would be fixed without making changes}
        {--tools=* : Specific tools to run (pint,rector)}';

    protected $description = 'Run multiple code fixing tools (Pint, Rector)';

    public function handle(): int
    {
        $tools = $this->option('tools') ?: ['pint', 'rector'];
        $dryRun = $this->option('dry-run');

        $this->components->info('Running code fix tools...');
        $this->components->info('Tools: '.implode(', ', $tools));
        $this->components->info('Dry run: '.($dryRun ? 'Yes' : 'No'));

        $results = [];

        foreach ($tools as $tool) {
            $this->newLine();
            $this->info("Running {$tool}...");

            $result = $this->runTool($tool, $dryRun);
            $results[$tool] = $result;

            if ($result['success']) {
                $this->info("✓ {$tool} completed successfully");
                if (filled($result['output'])) {
                    $this->line($result['output']);
                }
            } else {
                $this->error("✗ {$tool} failed");
                if (filled($result['output'])) {
                    $this->line($result['output']);
                }
            }
        }

        $this->newLine();
        $this->displaySummary($results);

        return $this->getExitCode($results);
    }

    /**
     * @return array{success: bool, output: string}
     */
    private function runTool(string $tool, bool $dryRun): array
    {
        $command = $this->getToolCommand($tool, $dryRun);

        if (blank($command)) {
            return [
                'success' => false,
                'output' => "Unknown tool: {$tool}",
            ];
        }

        $process = Process::fromShellCommandline($command, base_path());
        $process->run();

        return [
            'success' => $process->isSuccessful(),
            'output' => $process->getOutput().$process->getErrorOutput(),
        ];
    }

    private function getToolCommand(string $tool, bool $dryRun): string
    {
        return match ($tool) {
            'rector' => $dryRun
                ? 'vendor/bin/rector process --dry-run'
                : 'vendor/bin/rector process',
            'pint' => $dryRun
                ? 'vendor/bin/pint --test'
                : 'vendor/bin/pint',
            default => ''
        };
    }

    /**
     * @param  array<string, array{success: bool, output: string}>  $results
     */
    private function displaySummary(array $results): void
    {
        $this->components->info('Summary:');

        foreach ($results as $tool => $result) {
            $status = $result['success'] ? '✓' : '✗';
            $this->line("  {$status} {$tool}");
        }

        $successful = count(array_filter($results, fn ($r) => $r['success']));
        $total = count($results);

        $this->newLine();
        $this->components->info("Results: {$successful}/{$total} tools completed successfully");
    }

    /**
     * @param  array<string, array{success: bool, output: string}>  $results
     */
    private function getExitCode(array $results): int
    {
        return array_filter($results, fn ($r) => ! $r['success']) ? 1 : 0;
    }
}
