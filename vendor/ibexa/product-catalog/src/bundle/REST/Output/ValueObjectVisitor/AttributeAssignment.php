<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class AttributeAssignment extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'AttributeAssignment';
    private const ATTRIBUTE_ASSIGNMENT_IDENTIFIER_IS_REQUIRED = 'is_required';
    private const ATTRIBUTE_ASSIGNMENT_IDENTIFIER_IS_DISCRIMINATOR = 'is_discriminator';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeAssignment $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(
            self::ATTRIBUTE_ASSIGNMENT_IDENTIFIER_IS_REQUIRED,
            $data->attributeDefinitionAssignment->isRequired()
        );

        $generator->valueElement(
            self::ATTRIBUTE_ASSIGNMENT_IDENTIFIER_IS_DISCRIMINATOR,
            $data->attributeDefinitionAssignment->isDiscriminator()
        );

        $restAttribute = new Attribute($data->attributeDefinitionAssignment->getAttributeDefinition());
        $visitor->visitValueObject($restAttribute);

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
