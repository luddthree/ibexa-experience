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

final class VatCategory extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'VatCategory';
    private const ATTRIBUTE_IDENTIFIER_ID = 'identifier';
    private const ATTRIBUTE_IDENTIFIER_REGION = 'region';
    private const ATTRIBUTE_IDENTIFIER_VAT_VALUE = 'vatValue';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\VatCategory $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_ID, $data->vatCategory->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_REGION, $data->vatCategory->getRegion());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_VAT_VALUE, $data->vatCategory->getVatValue());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
