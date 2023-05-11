<?php

namespace Lyhty\EnhancedMediaLibrary;

function is_mcd(MediaCollectionDefinition|string $object_or_class): bool
{
    return is_subclass_of($object_or_class, MediaCollectionDefinition::class);
}

function get_mcd_name(MediaCollectionDefinition|string $object_or_class): string
{
    return is_mcd($object_or_class) ? $object_or_class::getName() : $object_or_class;
}
