<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ProductTypeUpdateStruct extends ValueObject
{
    private ContentTypeUpdateStruct $contentTypeUpdateStruct;

    /** @var \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] */
    private array $attributeDefinitionStructs;

    /** @param \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] $attributeDefinitionStructs */
    public function __construct(
        ContentTypeUpdateStruct $contentTypeUpdateStruct,
        array $attributeDefinitionStructs = []
    ) {
        parent::__construct();

        $this->contentTypeUpdateStruct = $contentTypeUpdateStruct;
        $this->attributeDefinitionStructs = $attributeDefinitionStructs;
    }

    public function getContentTypeUpdateStruct(): ContentTypeUpdateStruct
    {
        return $this->contentTypeUpdateStruct;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[]
     */
    public function getAttributeDefinitionStructs(): array
    {
        return $this->attributeDefinitionStructs;
    }
}
