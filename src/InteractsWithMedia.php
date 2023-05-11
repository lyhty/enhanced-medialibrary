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
        addMediaCollection as _baseAddMediaCollection;
        hasMedia as _baseHasMedia;
        getMedia as _baseGetMedia;
        getFirstMedia as _baseGetFirstMedia;
        getFirstMediaUrl as _baseGetFirstMediaUrl;
        getFirstTemporaryUrl as _baseGetFirstTemporaryUrl;
        getMediaCollection as _baseGetMediaCollection;
        getFallbackMediaUrl as _baseGetFallbackMediaUrl;
        getFallbackMediaPath as _baseGetFallbackMediaPath;
        getFirstMediaPath as _baseGetFirstMediaPath;
        updateMedia as _baseUpdateMedia;
        removeMediaItemsNotPresentInArray as _baseRemoveMediaItemsNotPresentInArray;
        clearMediaCollection as _baseClearMediaCollection;
        clearMediaCollectionExcept as _baseClearMediaCollectionExcept;
        loadMedia as _baseLoadMedia;
    }

    public function addMediaCollection(string|Name $name): MediaCollection
    {
        if (is_string($name)) {
            return $this->_baseAddMediaCollection($name);
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

        return $this->_baseHasMedia($collectionName, $filters);
    }

    public function getMedia(string|Name $collectionName = 'default', array|callable $filters = []): ModelMediaCollection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetMedia($collectionName, $filters);
    }

    public function getFirstMedia(string|Name $collectionName = 'default', $filters = []): ?Media
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetFirstMedia($collectionName, $filters);
    }

    public function getFirstMediaUrl(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetFirstMediaUrl($collectionName, $conversionName);
    }

    public function getFirstTemporaryUrl(DateTimeInterface $expiration, string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetFirstTemporaryUrl($expiration, $collectionName, $conversionName);
    }

    public function getMediaCollection(string|Name $collectionName = 'default'): ?ModelMediaCollection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetMediaCollection($collectionName);
    }

    public function getFallbackMediaUrl(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetFallbackMediaUrl($collectionName, $conversionName);
    }

    public function getFallbackMediaPath(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetFallbackMediaPath($collectionName, $conversionName);
    }

    public function getFirstMediaPath(string|Name $collectionName = 'default', string $conversionName = ''): string
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseGetFirstMediaPath($collectionName, $conversionName);
    }

    public function updateMedia(array $newMediaArray, string|Name $collectionName = 'default'): Collection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseUpdateMedia($newMediaArray, $collectionName);
    }

    protected function removeMediaItemsNotPresentInArray(array $newMediaArray, string|Name $collectionName = 'default'): void
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        $this->_baseRemoveMediaItemsNotPresentInArray($newMediaArray, $collectionName);
    }

    public function clearMediaCollection(string|Name $collectionName = 'default'): HasMedia
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseClearMediaCollection($collectionName);
    }

    public function clearMediaCollectionExcept(string|Name $collectionName = 'default', array|Collection|Media $excludedMedia = []): HasMedia
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseClearMediaCollectionExcept($collectionName, $excludedMedia);
    }

    public function loadMedia(string|Name $collectionName): Collection
    {
        $collectionName = is_string($collectionName) ? $collectionName : $collectionName->getName();

        return $this->_baseLoadMedia($collectionName);
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
