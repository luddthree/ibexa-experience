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

final class ProductName extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductNameCriterion';
    private const NAME_KEY = 'name';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductName $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::NAME_KEY, $data->productName->getName());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
