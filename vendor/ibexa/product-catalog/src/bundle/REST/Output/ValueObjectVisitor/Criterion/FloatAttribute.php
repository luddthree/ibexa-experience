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

final class FloatAttribute extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'FloatAttributeCriterion';
    private const IDENTIFIER_KEY = 'identifier';
    private const VALUE_KEY = 'value';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\FloatAttribute $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::IDENTIFIER_KEY, $data->floatAttribute->getIdentifier());
        $generator->valueElement(self::VALUE_KEY, $data->floatAttribute->getValue());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
