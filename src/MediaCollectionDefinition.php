<?php

namespace Lyhty\EnhancedMediaLibrary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

abstract class MediaCollectionDefinition
{
    public static bool $required = false;

    public static bool $singleFile = false;

    public static ?array $mimeTypes = null;

    public static array $conversionDefinitions = [];

    /**
     * The media collection handler. This method is called by the add method.
     */
    protected function handle(MediaCollection $mediaCollection): MediaCollection
    {
        return $mediaCollection;
    }

    /**
     * Get the name of the media collection.
     */
    public static function getName(): string
    {
        return Str::of(static::class)->classBasename()->before(class_basename(self::class))->snake();
    }

    /**
     * Add the media collection to the model.
     */
    final public function add(Model&HasMedia $model): MediaCollection
    {
        if (!method_exists($model, 'addMediaCollection')) {
            throw new LogicException("Model {$model} does not have the addMediaCollection method.");
        }

        return $this->handle(tap(
            $model->addMediaCollection(static::getName()),
            function (MediaCollection $collection): void {
                if (static::$mimeTypes !== null) {
                    $collection->acceptsMimeTypes(static::$mimeTypes);
                }

                if (static::$singleFile) {
                    $collection->singleFile();
                }
            }
        ));
    }

    /**
     * Get the rules for the media collection.
     */
    final public static function rules(): array
    {
        $rules = [];

        if (static::$required) {
            $rules[] = 'required';
        }

        if (static::$mimeTypes !== null) {
            $rules[] = 'mimetypes:' . implode(',', static::$mimeTypes);
        }

        if (property_exists(static::class, $custom = 'customRules')) {
            $rules = array_merge($rules, static::$$custom);
        } else if (method_exists(static::class, 'customRules')) {
            $rules = array_merge($rules, static::{$custom}());
        }

        return $rules;
    }
}
