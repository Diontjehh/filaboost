<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

final class MakeActionCommand extends GeneratorCommand
{
    protected $name = 'make:action';

    protected $description = 'Create a new action class';

    protected $type = 'Action';

    public function handle(): bool|int|null
    {
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return 1;
        }

        return parent::handle();
    }

    protected function getNameInput(): string
    {
        $name = $this->argument('name');

        return Str::of(mb_trim($name))
            ->replaceEnd('.php', '')
            ->replaceEnd('Action', '')
            ->append('Action')
            ->toString();
    }

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/action.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Actions';
    }

    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return app_path(str_replace('\\', '/', $name).'.php');
    }

    private function resolveStubPath(string $stub): string
    {
        $basePath = $this->laravel->basePath(mb_trim($stub, '/'));

        return file_exists($basePath)
            ? $basePath
            : __DIR__.'/../../'.$stub;
    }
}