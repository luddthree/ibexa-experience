<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Content;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Personalization\FieldType\ValueNormalizerDispatcherInterface;

final class DataResolver implements DataResolverInterface
{
    private LocationService $locationService;

    private ValueNormalizerDispatcherInterface $valueNormalizerDispatcher;

    private URLAliasService $urlAliasService;

    private string $restPathPrefix;

    public function __construct(
        LocationService $locationService,
        ValueNormalizerDispatcherInterface $valueNormalizerDispatcher,
        URLAliasService $urlAliasService,
        string $restPathPrefix
    ) {
        $this->locationService = $locationService;
        $this->valueNormalizerDispatcher = $valueNormalizerDispatcher;
        $this->urlAliasService = $urlAliasService;
        $this->restPathPrefix = $restPathPrefix;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function resolve(Content $content, string $languageCode): array
    {
        $data = [];
        foreach ($content->getFieldsByLanguage($languageCode) as $field) {
            if ($this->valueNormalizerDispatcher->supportsNormalizer($field->value)) {
                $data[$field->fieldDefIdentifier] = $this->valueNormalizerDispatcher->dispatch($field->value);
            }
        }

        $contentInfo = $content->getVersionInfo()->getContentInfo();
        $locations = $this->locationService->loadLocations($contentInfo);
        $data['categoryPath'] = $this->getCategoryPaths($locations);
        $mainLocationPathString = $this->getMainLocationPathString($contentInfo);
        if (null !== $mainLocationPathString) {
            $data['mainLocation'] = [
                'href' => $this->restPathPrefix . '/content/locations' . $mainLocationPathString,
            ];
        }

        $data['locations'] = [
            'href' => $this->restPathPrefix . '/content/objects/' . $content->id . '/locations',
        ];
        $data['publishedDate'] = $contentInfo->publishedDate->format('c');
        $data['uri'] = $this->getUrls($locations, $languageCode);

        return $data;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $locations
     *
     * @return array<string>
     */
    private function getCategoryPaths(iterable $locations): array
    {
        $categoryPaths = [];
        foreach ($locations as $location) {
            $categoryPaths[] = $location->pathString;
        }

        return $categoryPaths;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $locations
     *
     * @return array<string>
     */
    private function getUrls(iterable $locations, string $languageCode): array
    {
        $urls = [];
        foreach ($locations as $location) {
            foreach ($this->getListLocationAliases($location, $languageCode) as $urlAlias) {
                $urls[] = $urlAlias->path;
            }
        }

        return $urls;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\URLAlias>
     */
    private function getListLocationAliases(Location $location, string $languageCode): iterable
    {
        $prioritizedLanguagesList = [];

        foreach ($location->getContent()->getVersionInfo()->getLanguages() as $language) {
            $prioritizedLanguagesList[] = $language->getLanguageCode();
        }

        yield from $this->urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            true,
            $prioritizedLanguagesList
        );
    }

    private function getMainLocationPathString(ContentInfo $contentInfo): ?string
    {
        $mainLocation = $contentInfo->getMainLocation();
        if (null !== $mainLocation) {
            return $mainLocation->getPathString();
        }

        return null;
    }
}
