<?php

namespace Lyhty\EnhancedMediaLibrary;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Lyhty\EnhancedMediaLibrary\Contracts\NamedMediaCollectionEnum as Name;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia as BaseInteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection as ModelMediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    use BaseInteractsWithMedia {
        addMediaCollection as _addMediaCollection;
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

    public function addMediaCollection(string|Name $name): MediaCollection
    {
        if (is_string($name)) {
            return $this->_addMediaCollection($name);
        }

        return $this->addNamedMediaCollection($name);
    }

    public function addNamedMediaCollection(Name $name): void
    {
        $name->makeNewNamedMediaCollection()->add($this);
    }

    public function hasMedia(string|Name $collectionName = 'default', array $filters = []): bool
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_hasMedia($collectionName, $filters);
    }

    public function getMedia(string|Name $collectionName = 'default', array|callable $filters = []): ModelMediaCollection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getMedia($collectionName, $filters);
    }

    public function getFirstMedia(string|Name $collectionName = 'default', $filters = []): ?Media
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getFirstMedia($collectionName, $filters);
    }

    public function getFirstMediaUrl(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getFirstMediaUrl($collectionName, $conversionName);
    }

    public function getFirstTemporaryUrl(DateTimeInterface $expiration, string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getFirstTemporaryUrl($expiration, $collectionName, $conversionName);
    }

    public function getMediaCollection(string|Name $collectionName = 'default'): ?ModelMediaCollection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getMediaCollection($collectionName);
    }

    public function getFallbackMediaUrl(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getFallbackMediaUrl($collectionName, $conversionName);
    }

    public function getFallbackMediaPath(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getFallbackMediaPath($collectionName, $conversionName);
    }

    public function getFirstMediaPath(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_getFirstMediaPath($collectionName, $conversionName);
    }

    public function updateMedia(array $newMediaArray, string|Name $collectionName = 'default'): Collection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_updateMedia($newMediaArray, $collectionName);
    }

    protected function removeMediaItemsNotPresentInArray(array $newMediaArray, string|Name $collectionName = 'default'): void
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        $this->_removeMediaItemsNotPresentInArray($newMediaArray, $collectionName);
    }

    public function clearMediaCollection(string|Name $collectionName = 'default'): HasMedia
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_clearMediaCollection($collectionName);
    }

    public function clearMediaCollectionExcept(string|Name $collectionName = 'default', array|Collection|Media $excludedMedia = []): HasMedia
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_clearMediaCollectionExcept($collectionName, $excludedMedia);
    }

    public function loadMedia(string|Name $collectionName): Collection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_loadMedia($collectionName);
    }

    /**
     * @return Name[]
     */
    public function getNamedMediaCollections(): array|null
    {
        return property_exists($this, 'namedMediaCollections')
            ? static::${'namedMediaCollections'}
            : null;
    }

    public function registerMediaCollections(): void
    {
        if (!property_exists($this, 'namedMediaCollections')) {
            return;
        }

        foreach ($this->getNamedMediaCollections() as $name) {
            $this->addNamedMediaCollection($name);
        };
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (!property_exists($this, 'namedMediaCollections')) {
            return;
        }

        $grouped = [];

        foreach ($this->getNamedMediaCollections() as $name) {
            $class = $name->getNamedMediaCollectionClassName();

            foreach ($class::$conversions as $conversion) {
                if (! Arr::has($grouped, $conversion)) {
                    Arr::set($grouped, $conversion, []);
                }

                array_push($grouped[$conversion], $name->getName());
            }
        }

        foreach ($grouped as $conversion => $collectionNames) {
            (new $conversion)->add($this)->performOnCollections(...$collectionNames);
        }
    }
}
