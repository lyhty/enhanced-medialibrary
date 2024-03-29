<?php

namespace Lyhty\EnhancedMediaLibrary;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia as BaseInteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection as ModelMediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    use BaseInteractsWithMedia {
        hasMedia as _hasMedia;
        getMedia as _getMedia;
        getFirstMedia as _getFirstMedia;
        getFirstMediaUrl as _getFirstMediaUrl;
        getFirstTemporaryUrl as _getFirstTemporaryUrl;
        getMediaCollection as _getMediaCollection;
        getFallbackMediaUrl as _getFallbackMediaUrl;
        getFallbackMediaPath as _getFallbackMediaPath;
        getFirstMediaPath as _getFirstMediaPath;
        updateMedia as _updateMedia;
        removeMediaItemsNotPresentInArray as _removeMediaItemsNotPresentInArray;
        clearMediaCollection as _clearMediaCollection;
        clearMediaCollectionExcept as _clearMediaCollectionExcept;
        loadMedia as _loadMedia;
    }

    public function hasMedia(string $collectionName = 'default', array $filters = []): bool
    {
        return $this->_hasMedia(get_mcd_name($collectionName), $filters);
    }

    public function getMedia(string $collectionName = 'default', array|callable $filters = []): ModelMediaCollection
    {
        return $this->_getMedia(get_mcd_name($collectionName), $filters);
    }

    public function getFirstMedia(string $collectionName = 'default', $filters = []): ?Media
    {
        return $this->_getFirstMedia(get_mcd_name($collectionName), $filters);
    }

    public function getFirstMediaUrl(string $collectionName = 'default', string $conversionName = ''): string
    {
        return $this->_getFirstMediaUrl(get_mcd_name($collectionName), $conversionName);
    }

    public function getFirstTemporaryUrl(DateTimeInterface $expiration, string $collectionName = 'default', string $conversionName = ''): string
    {
        return $this->_getFirstTemporaryUrl($expiration, get_mcd_name($collectionName), $conversionName);
    }

    public function getMediaCollection(string $collectionName = 'default'): ?MediaCollection
    {
        return $this->_getMediaCollection(get_mcd_name($collectionName));
    }

    public function getFallbackMediaUrl(string $collectionName = 'default', string $conversionName = ''): string
    {
        return $this->_getFallbackMediaUrl(get_mcd_name($collectionName), $conversionName);
    }

    public function getFallbackMediaPath(string $collectionName = 'default', string $conversionName = ''): string
    {
        return $this->_getFallbackMediaPath(get_mcd_name($collectionName), $conversionName);
    }

    public function getFirstMediaPath(string $collectionName = 'default', string $conversionName = ''): string
    {
        return $this->_getFirstMediaPath(get_mcd_name($collectionName), $conversionName);
    }

    public function updateMedia(array $newMediaArray, string $collectionName = 'default'): Collection
    {
        return $this->_updateMedia($newMediaArray, get_mcd_name($collectionName));
    }

    protected function removeMediaItemsNotPresentInArray(array $newMediaArray, string $collectionName = 'default'): void
    {
        $this->_removeMediaItemsNotPresentInArray($newMediaArray, get_mcd_name($collectionName));
    }

    public function clearMediaCollection(string $collectionName = 'default'): HasMedia
    {
        return $this->_clearMediaCollection(get_mcd_name($collectionName));
    }

    public function clearMediaCollectionExcept(string $collectionName = 'default', array|Collection|Media $excludedMedia = []): HasMedia
    {
        return $this->_clearMediaCollectionExcept(get_mcd_name($collectionName), $excludedMedia);
    }

    public function loadMedia(string $collectionName): Collection
    {
        return $this->_loadMedia(get_mcd_name($collectionName));
    }

    public function registerMediaCollections(): void
    {
        $this->registerMediaCollectionDefinitions();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $grouped = $this->resolveMediaConversionsFromMediaCollections();

        foreach ($grouped as $mediaConversionDefinitionClass => $mediaCollectionDefinitionClasses) {
            (new $mediaConversionDefinitionClass)
                ->add($this, $media)
                ->performOnCollections(...array_map(
                    array: $mediaCollectionDefinitionClasses,
                    callback: fn (string $class) => $class::getName()
                ));
        }
    }

    protected function registerMediaCollectionDefinitions(): static
    {
        foreach ($this->getMediaCollectionDefinitions() as $definition => $conversions) {
            $this->addMediaCollectionDefinition($definition);
        }

        return $this;
    }

    /**
     * @return array<class-string<MediaCollectionDefinition>, array<class-string<MediaConversionDefinition>>>
     */
    public function getMediaCollectionDefinitions(): array
    {
        return property_exists($this, 'mediaCollectionDefinitions')
            ? Arr::associate($this->mediaCollectionDefinitions, [])
            : [];
    }

    public function addMediaCollectionDefinition(MediaCollectionDefinition|string $definition): static
    {
        if (! is_mcd($definition)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must be an instance of or a class that extends %s::class, %s::class given',
                __METHOD__, MediaCollectionDefinition::class,
                is_object($definition) ? get_class($definition) : $definition
            ));
        }

        (is_string($definition) ? new $definition : $definition)->add($this);

        return $this;
    }

    /**
     * @return array<class-string<MediaConversionDefinition>, class-string<MediaCollectionDefinition>[]>
     */
    public function resolveMediaConversionsFromMediaCollections(): array
    {
        $definitions = $this->getMediaCollectionDefinitions();
        $grouped = array_fill_keys(array_unique(Arr::flatten($definitions)), []);

        foreach ($definitions as $collection => $conversions) {
            foreach ($conversions as $conversion) {
                array_push($grouped[$conversion], $collection);
            }
        }

        return $grouped;
    }
}
