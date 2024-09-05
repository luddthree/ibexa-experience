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

final class CreatedAtRange extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'CreatedAtRangeCriterion';
    private const MIN_KEY = 'min';
    private const MAX_KEY = 'max';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\CreatedAtRange $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::MIN_KEY, $data->createdAtRange->getMin());
        $generator->valueElement(self::MAX_KEY, $data->createdAtRange->getMax());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
