<?php

namespace Lyhty\EnhancedMediaLibrary\Macros;

use Closure;
use Lyhty\EnhancedMediaLibrary\MediaCollectionDefinition;

/**
 * @mixin \Spatie\MediaLibrary\MediaCollections\FileAdder
 */
class ToDefinedMediaCollectionMacro
{
    public function __invoke(): Closure
    {
        return function (string $name, string $diskName = '') {
            $name = is_a($name, MediaCollectionDefinition::class, true)
                ? $name::getName()
                : $name;

            return $this->toMediaCollection($name, $diskName);
        };
    }
}
