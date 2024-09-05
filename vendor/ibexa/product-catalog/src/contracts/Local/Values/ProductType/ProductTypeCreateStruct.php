<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ProductTypeCreateStruct extends ValueObject
{
    private ContentTypeCreateStruct $contentTypeCreateStruct;

    private string $mainLanguageCode;

    /** @var \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] */
    private iterable $assignedAttributesDefinitions;

    private bool $isVirtual;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] $assignedAttributesDefinitions
     */
    public function __construct(
        ContentTypeCreateStruct $contentTypeCreateStruct,
        string $mainLanguageCode,
        iterable $assignedAttributesDefinitions = [],
        bool $isVirtual = false
    ) {
        parent::__construct();

        $this->contentTypeCreateStruct = $contentTypeCreateStruct;
        $this->mainLanguageCode = $mainLanguageCode;
        $this->assignedAttributesDefinitions = $assignedAttributesDefinitions;
        $this->isVirtual = $isVirtual;
    }

    public function getContentTypeCreateStruct(): ContentTypeCreateStruct
    {
        return $this->contentTypeCreateStruct;
    }

    public function getMainLanguageCode(): string
    {
        return $this->mainLanguageCode;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[]
     */
    public function getAssignedAttributesDefinitions(): iterable
    {
        return $this->assignedAttributesDefinitions;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] $assignedAttributesDefinitions
     */
    public function setAssignedAttributesDefinitions(iterable $assignedAttributesDefinitions): void
    {
        $this->assignedAttributesDefinitions = $assignedAttributesDefinitions;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    public function setVirtual(bool $isVirtual): void
    {
        $this->isVirtual = $isVirtual;
    }
}
