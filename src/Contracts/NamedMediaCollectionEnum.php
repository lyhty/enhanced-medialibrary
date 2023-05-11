<?php

namespace Lyhty\EnhancedMediaLibrary\Contracts;

use Lyhty\EnhancedMediaLibrary\NamedMediaCollection as NamedMediaCollectionClass;

interface NamedMediaCollectionEnum
{
    public function makeNewNamedMediaCollection(): NamedMediaCollectionClass;

    public function getNamedMediaCollectionClassName(): string;

    public function getName(): string;
}
