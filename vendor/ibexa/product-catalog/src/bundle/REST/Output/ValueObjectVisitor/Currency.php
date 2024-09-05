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

final class Currency extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'Currency';
    private const ATTRIBUTE_IDENTIFIER_ID = 'id';
    private const ATTRIBUTE_IDENTIFIER_CODE = 'code';
    private const ATTRIBUTE_IDENTIFIER_SUBUNITS = 'subunits';
    private const ATTRIBUTE_IDENTIFIER_ENABLED = 'enabled';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Currency $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_ID, $data->currency->getId());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_CODE, $data->currency->getCode());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_SUBUNITS, $data->currency->getSubUnits());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_ENABLED, $data->currency->isEnabled());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
