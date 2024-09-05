<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute;

/**
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion\AbstractAttributeVisitor<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute
 * >
 */
final class SelectionAttributeVisitor extends AbstractAttributeVisitor
{
    protected function getCriterionClass(): string
    {
        return SelectionAttribute::class;
    }

    protected function getAttributeType(AbstractAttribute $criterion): string
    {
        return 's';
    }
}
