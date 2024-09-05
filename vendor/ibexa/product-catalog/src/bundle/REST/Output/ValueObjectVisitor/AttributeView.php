<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute as RestAttribute;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeList as RestAttributeList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class AttributeView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'AttributeView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const ATTRIBUTE_QUERY_IDENTIFIER = 'AttributeQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restAttributeGroups = [];
        $viewIdentifier = $data->getIdentifier();
        $attributeList = $data->getAttributeDefinitionList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::ATTRIBUTE_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::ATTRIBUTE_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $attributeList->getTotalCount());

        foreach ($attributeList->getAttributeDefinitions() as $attribute) {
            $restAttributeGroups[] = new RestAttribute($attribute);
        }

        $visitor->visitValueObject(new RestAttributeList($restAttributeGroups));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
