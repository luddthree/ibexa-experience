<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Search\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface;

/**
 * @implements \Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
 * >
 */
final class DummyConverter implements CriterionConverterInterface
{
    private ContentCriterion $targetCriterion;

    public function __construct(ContentCriterion $targetCriterion)
    {
        $this->targetCriterion = $targetCriterion;
    }

    public function convert(ProductCriterion $criterion): ContentCriterion
    {
        return $this->targetCriterion;
    }
}
