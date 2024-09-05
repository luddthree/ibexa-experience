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

final class Availability extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'Availability';
    private const AVAILABILITY_IDENTIFIER_IS_AVAILABLE = 'is_available';
    private const AVAILABILITY_IDENTIFIER_STOCK = 'stock';
    private const AVAILABILITY_IDENTIFIER_IS_INFINITE = 'is_infinite';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Availability $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $availability = $data->availability;
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::AVAILABILITY_IDENTIFIER_IS_AVAILABLE, $availability->isAvailable());
        $generator->valueElement(self::AVAILABILITY_IDENTIFIER_STOCK, $availability->getStock());
        $generator->valueElement(self::AVAILABILITY_IDENTIFIER_IS_INFINITE, $availability->isInfinite());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
