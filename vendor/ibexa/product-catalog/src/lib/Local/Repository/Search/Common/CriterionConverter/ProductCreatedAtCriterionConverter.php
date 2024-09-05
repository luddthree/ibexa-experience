<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\OperatorConverterInterface;

/**
 * @implements \Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt
 * >
 */
final class ProductCreatedAtCriterionConverter implements CriterionConverterInterface
{
    private OperatorConverterInterface $operatorConverter;

    public function __construct(OperatorConverterInterface $operatorConverter)
    {
        $this->operatorConverter = $operatorConverter;
    }

    public function convert(ProductCriterion $criterion): ContentCriterion
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt $criterion */
        return new ContentCriterion\DateMetadata(
            ContentCriterion\DateMetadata::CREATED,
            $this->operatorConverter->convert($criterion->getOperator()),
            $criterion->getValue()->getTimestamp()
        );
    }
}
