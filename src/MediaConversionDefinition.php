<?php

namespace Lyhty\EnhancedMediaLibrary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

abstract class MediaConversionDefinition
{
    /**
     * The media conversion handler. This method is called by the add method.
     */
    abstract protected function handle(Conversion $conversion, Media $media = null): Conversion;

    /**
     * Get the name of the media conversion.
     */
    public static function getName(): string
    {
        return Str::of(static::class)->classBasename()->before(class_basename(self::class))->snake();
    }

    /**
     * Add the media conversion to the model.
     */
    final public function add(Model&HasMedia $model, Media $media = null): Conversion
    {
        if (! method_exists($model, 'addMediaConversion')) {
            throw new LogicException("Model {$model} does not have the addMediaCollection method.");
        }

        return $this->handle($model->addMediaConversion($this->getName()), $media);
    }
}
