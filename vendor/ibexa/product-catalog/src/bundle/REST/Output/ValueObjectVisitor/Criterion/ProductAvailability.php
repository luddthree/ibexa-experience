<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor\Criterion;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductAvailability extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductAvailabilityCriterion';
    private const IS_AVAILABLE_KEY = 'is_available';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductAvailability $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::IS_AVAILABLE_KEY, $data->productAvailability->isAvailable());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
