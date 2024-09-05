<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterRegistry;

final class CriterionConverterVisitor extends CriterionHandler
{
    private CriterionConverterRegistry $converterRegistry;

    public function __construct(Connection $connection, CriterionConverterRegistry $converterRegistry)
    {
        parent::__construct($connection);

        $this->converterRegistry = $converterRegistry;
    }

    public function accept(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $this->converterRegistry->hasConverter($criterion->getProductCriterion());
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
     * > $criterion
     *
     * @param array<string,mixed> $languageSettings
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        ContentCriterion $criterion,
        array $languageSettings
    ) {
        return $converter->convertCriteria(
            $queryBuilder,
            $this->convert($criterion->getProductCriterion()),
            $languageSettings
        );
    }

    private function convert(ProductCriterion $criterion): ContentCriterion
    {
        return $this->converterRegistry->getConverter($criterion)->convert($criterion);
    }
}
