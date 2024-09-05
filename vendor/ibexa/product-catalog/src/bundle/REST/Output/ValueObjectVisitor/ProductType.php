<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeAssignment;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductType extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductType';
    private const ATTRIBUTE_IDENTIFIER_ID = 'identifier';
    private const ATTRIBUTE_IDENTIFIER_NAME = 'name';
    private const LIST_OBJECT_IDENTIFIER = 'AttributeAssignmentList';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductType $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_ID, $data->productType->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_NAME, $data->productType->getName());

        $generator->startList(self::LIST_OBJECT_IDENTIFIER);

        foreach ($data->productType->getAttributesDefinitions() as $attributeAssignment) {
            $restAttributeAssignment = new AttributeAssignment($attributeAssignment);
            $visitor->visitValueObject($restAttributeAssignment);
        }

        $generator->endList(self::LIST_OBJECT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
