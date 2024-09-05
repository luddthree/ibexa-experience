<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Traversable;

/**
 * @final
 */
class ProductType implements ContentTypeAwareProductTypeInterface
{
    private ContentType $contentType;

    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface> */
    private iterable $assignedAttributesDefinitions;

    /** @var iterable<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface> */
    private iterable $vatCategories;

    private bool $isVirtual;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface> $assignedAttributesDefinitions
     * @param iterable<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface> $vatCategories
     */
    public function __construct(
        ContentType $contentType,
        iterable $assignedAttributesDefinitions = [],
        iterable $vatCategories = [],
        bool $isVirtual = false
    ) {
        $this->contentType = $contentType;
        $this->assignedAttributesDefinitions = $assignedAttributesDefinitions;
        $this->vatCategories = $vatCategories;
        $this->isVirtual = $isVirtual;
    }

    public function getIdentifier(): string
    {
        return $this->contentType->identifier;
    }

    public function getName(): string
    {
        return $this->contentType->getName();
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface>
     */
    public function getAttributesDefinitions(): iterable
    {
        return $this->assignedAttributesDefinitions;
    }

    public function getVatCategory(RegionInterface $region): ?VatCategoryInterface
    {
        $vatCategories = $this->vatCategories;
        if ($vatCategories instanceof Traversable) {
            $vatCategories = iterator_to_array($vatCategories);
        }

        return $vatCategories[$region->getIdentifier()] ?? null;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }
}
