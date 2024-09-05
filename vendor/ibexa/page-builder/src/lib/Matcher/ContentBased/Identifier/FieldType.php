<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Matcher\ContentBased\Identifier;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;

class FieldType extends MultipleValued
{
    /**
     * Checks if a Location object matches.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function matchLocation(Location $location)
    {
        $contentType = $this->repository
            ->getContentTypeService()
            ->loadContentType($location->getContentInfo()->contentTypeId);

        return $this->hasFieldType($contentType);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     */
    private function hasFieldType(ContentType $contentType): bool
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if (in_array($fieldDefinition->fieldTypeIdentifier, $this->getValues(), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a ContentInfo object matches.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function matchContentInfo(ContentInfo $contentInfo)
    {
        $contentType = $this->repository
            ->getContentTypeService()
            ->loadContentType($contentInfo->contentTypeId);

        return $this->hasFieldType($contentType);
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\View $view
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function match(View $view)
    {
        if (!$view instanceof ContentValueView) {
            return false;
        }

        $contentType = $this->repository
            ->getContentTypeService()
            ->loadContentType($view->getContent()->contentInfo->contentTypeId);

        return $this->hasFieldType($contentType);
    }
}

class_alias(FieldType::class, 'EzSystems\EzPlatformPageBuilder\Matcher\ContentBased\Identifier\FieldType');
