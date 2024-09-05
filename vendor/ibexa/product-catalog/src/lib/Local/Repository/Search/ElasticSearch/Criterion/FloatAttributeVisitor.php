<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttribute;

/**
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion\AbstractAttributeVisitor<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttribute
 * >
 */
final class FloatAttributeVisitor extends AbstractAttributeVisitor
{
    protected function getCriterionClass(): string
    {
        return FloatAttribute::class;
    }

    protected function getAttributeType(AbstractAttribute $criterion): string
    {
        return 'f';
    }
}
