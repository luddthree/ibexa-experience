<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Matcher\ContentTypeBased\Identifier;

use Ibexa\ContentForms\Content\View\ContentTypeValueView;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use Ibexa\Core\MVC\Symfony\View\View;

/**
 * Matches views only when particular fieldtypes are present in the content type.
 */
class FieldType extends MultipleValued
{
    /**
     * {@inheritdoc}
     */
    public function match(View $view)
    {
        if (!$view instanceof ContentTypeValueView) {
            return false;
        }

        return $this->hasFieldType($view->getContentType());
    }

    /**
     * {@inheritdoc}
     */
    public function matchLocation(Location $location)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function matchContentInfo(ContentInfo $contentInfo)
    {
        return false;
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
}

class_alias(FieldType::class, 'EzSystems\EzPlatformPageBuilder\Matcher\ContentTypeBased\Identifier\FieldType');
