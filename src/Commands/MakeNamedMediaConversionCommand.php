<?php

namespace Lyhty\EnhancedMediaLibrary\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeNamedMediaConversionCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:named-media-conversion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new named media conversion class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Named Media Conversion';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        return parent::buildClass($name);

        // $stub = parent::buildClass($name);
        // return $this->replaceStubVariables($stub);
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceStubVariables($stub)
    {
        $replace = [];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/named-media-conversion.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Media\Conversions';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
