<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;

class FieldDefinitionLocator
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $pageFieldType;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Type $pageFieldType
     */
    public function __construct(Type $pageFieldType)
    {
        $this->pageFieldType = $pageFieldType;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition|null
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function locate(Content $content, ContentType $contentType): ?FieldDefinition
    {
        if ($content->contentInfo->contentTypeId !== $contentType->id) {
            throw new InvalidArgumentException('$content', 'Content does not match the passed content type');
        }

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier === $this->pageFieldType->getFieldTypeIdentifier()) {
                return $fieldDefinition;
            }
        }

        return null;
    }
}

class_alias(FieldDefinitionLocator::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\FieldDefinitionLocator');
