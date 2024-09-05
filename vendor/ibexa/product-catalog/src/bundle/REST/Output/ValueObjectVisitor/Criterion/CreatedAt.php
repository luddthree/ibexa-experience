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

final class CreatedAt extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'CreatedAtCriterion';
    private const CREATED_AT_KEY = 'created_at';
    private const OPERATOR_KEY = 'operator';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\CreatedAt $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::CREATED_AT_KEY, $data->createdAt->getValue());
        $generator->valueElement(self::OPERATOR_KEY, $data->createdAt->getOperator());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
