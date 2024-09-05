<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroup as RestAttributeGroup;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupList as RestAttributeGroupList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class AttributeGroupView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'AttributeGroupView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const ATTRIBUTE_GROUP_QUERY_IDENTIFIER = 'AttributeGroupQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restAttributeGroups = [];
        $viewIdentifier = $data->getIdentifier();
        $attributeGroupList = $data->getAttributeGroupList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::ATTRIBUTE_GROUP_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::ATTRIBUTE_GROUP_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $attributeGroupList->getTotalCount());

        foreach ($attributeGroupList->getAttributeGroups() as $attributeGroup) {
            $restAttributeGroups[] = new RestAttributeGroup($attributeGroup);
        }

        $visitor->visitValueObject(new RestAttributeGroupList($restAttributeGroups));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
