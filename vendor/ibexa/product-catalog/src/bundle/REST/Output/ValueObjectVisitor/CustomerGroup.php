<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Exceptions\NotFoundException;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class CustomerGroup extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'CustomerGroup';
    private const ATTRIBUTE_IDENTIFIER_ID = 'id';
    private const ATTRIBUTE_IDENTIFIER_NAME = 'name';
    private const ATTRIBUTE_IDENTIFIER_IDENTIFIER = 'identifier';
    private const ATTRIBUTE_IDENTIFIER_DESCRIPTION = 'description';
    private const ATTRIBUTE_IDENTIFIER_USERS = 'Users';
    private const ATTRIBUTE_IDENTIFIER_GLOBAL_PRICE_RATE = 'global_price_rate';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroup $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        if (empty($data->customerGroup)) {
            throw new NotFoundException('Customer Group was not found.');
        }

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_ID, $data->customerGroup->getId());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_NAME, $data->customerGroup->getName());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_IDENTIFIER, $data->customerGroup->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_DESCRIPTION, $data->customerGroup->getDescription());

        $generator->startList(self::ATTRIBUTE_IDENTIFIER_USERS);

        foreach ($data->customerGroup->getUsers() as $user) {
            $visitor->visitValueObject($user);
        }

        $generator->endList(self::ATTRIBUTE_IDENTIFIER_USERS);

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_GLOBAL_PRICE_RATE, $data->customerGroup->getGlobalPriceRate());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
