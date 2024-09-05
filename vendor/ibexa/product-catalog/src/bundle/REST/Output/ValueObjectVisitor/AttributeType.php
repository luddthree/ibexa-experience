<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class AttributeType extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'AttributeType';
    private const ATTRIBUTE_TYPE_IDENTIFIER_ID = 'identifier';
    private const ATTRIBUTE_TYPE_IDENTIFIER_NAME = 'name';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeType $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_TYPE_IDENTIFIER_ID, $data->attributeType->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_TYPE_IDENTIFIER_NAME, $data->attributeType->getName());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
