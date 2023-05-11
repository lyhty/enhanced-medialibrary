<?php

namespace Lyhty\EnhancedMediaLibrary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;

abstract class MediaConversionDefinition
{
    /**
     * Add the media collection to the model.
     *
     * @param  TModel  $model
     */
    final public function add(Model&HasMedia $model): Conversion
    {
        if (! method_exists($model, 'addMediaConversion')) {
            throw new LogicException("Model {$model} does not have the addMediaCollection method.");
        }

        return $this->handle($model->addMediaConversion($this->getName()));
    }

    public static function getName(): string
    {
        return Str::of(static::class)->classBasename()->before(class_basename(self::class))->snake();
    }

    /**
     * The media collection handler. This method is called by the add method.
     */
    abstract protected function handle(Conversion $conversion): Conversion;
}
