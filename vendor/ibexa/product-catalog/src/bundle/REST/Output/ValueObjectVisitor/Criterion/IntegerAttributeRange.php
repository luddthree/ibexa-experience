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

final class IntegerAttributeRange extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'IntegerAttributeRangeCriterion';
    private const IDENTIFIER_KEY = 'identifier';
    private const MIN_KEY = 'min';
    private const MAX_KEY = 'max';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\IntegerAttributeRange $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::IDENTIFIER_KEY, $data->integerAttributeRange->getIdentifier());
        $generator->valueElement(self::MIN_KEY, $data->integerAttributeRange->getMin());
        $generator->valueElement(self::MAX_KEY, $data->integerAttributeRange->getMax());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
