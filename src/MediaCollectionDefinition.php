<?php

namespace Lyhty\EnhancedMediaLibrary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

abstract class MediaCollectionDefinition
{
    public static array $conversionDefinitions = [];

    /**
     * Add the media collection to the model.
     */
    final public function add(Model&HasMedia $model): MediaCollection
    {
        if (! method_exists($model, 'addMediaCollection')) {
            throw new LogicException("Model {$model} does not have the addMediaCollection method.");
        }

        return $this->handle($model->addMediaCollection(static::getName()));
    }

    /**
     * Get the name of the media collection.
     */
    public static function getName(): string
    {
        return Str::of(static::class)->classBasename()->before(class_basename(self::class))->snake();
    }

    /**
     * The media collection handler. This method is called by the add method.
     */
    abstract protected function handle(MediaCollection $mediaCollection): MediaCollection;
}
