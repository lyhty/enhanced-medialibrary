<?php

namespace Lyhty\EnhancedMediaLibrary;

use Lyhty\Macronite\MacroServiceProvider;

class EnhancedMediaLibraryServiceProvider extends MacroServiceProvider
{
    protected static array $macros = [
        \Spatie\MediaLibrary\MediaCollections\FileAdder::class => [
            'toNamedMediaCollection' => Macros\ToNamedMediaCollectionMacro::class,
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
                Commands\MakeNamedMediaCollectionCommand::class,
                Commands\MakeNamedMediaConversionCommand::class,
            ]);
        }
    }
}
