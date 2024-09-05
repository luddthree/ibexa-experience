<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroup as RestCustomerGroup;
use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupList as RestCustomerGroupList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class CustomerGroupView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'CustomerGroupView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const CUSTOMER_GROUP_QUERY_IDENTIFIER = 'CustomerGroupQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restCustomerGroups = [];
        $viewIdentifier = $data->getIdentifier();
        $customerGroupList = $data->getCustomerGroupList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::CUSTOMER_GROUP_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::CUSTOMER_GROUP_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $customerGroupList->getTotalCount());

        foreach ($customerGroupList->getCustomerGroups() as $customerGroup) {
            $restCustomerGroups[] = new RestCustomerGroup($customerGroup);
        }

        $visitor->visitValueObject(new RestCustomerGroupList($restCustomerGroups));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
