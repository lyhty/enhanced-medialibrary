<?php

namespace Lyhty\EnhancedMediaLibrary;

use Lyhty\Macronite\MacroServiceProvider;

class EnhancedMediaLibraryServiceProvider extends MacroServiceProvider
{
    protected static array $macros = [
        \Spatie\MediaLibrary\MediaCollections\FileAdder::class => [
            Macros\ToDefinedMediaCollectionMacro::class,
        ],
    ];

    /**
     * Bootstrap application macros.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeMediaCollectionDefinitionCommand::class,
                Commands\MakeMediaConversionDefinitionCommand::class,
            ]);
        }
    }
}
