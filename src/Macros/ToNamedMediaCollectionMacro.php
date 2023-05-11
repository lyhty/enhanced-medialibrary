<?php

namespace Lyhty\EnhancedMediaLibrary\Macros;

use Lyhty\EnhancedMediaLibrary\Contracts\NamedMediaCollectionEnum;
use Closure;

/**
 * @mixin \Spatie\MediaLibrary\MediaCollections\FileAdder
 */
class ToNamedMediaCollectionMacro
{
    public function __invoke(): Closure
    {
        return function (NamedMediaCollectionEnum $name, string $diskName = '') {
            return $this->toMediaCollection($name->getName(), $diskName);
        };
    }
}
