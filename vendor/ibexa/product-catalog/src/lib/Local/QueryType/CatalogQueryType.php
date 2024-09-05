<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\QueryType;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogQueryType extends OptionsResolverBasedQueryType
{
    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    protected function doGetQuery(array $parameters): Query
    {
        $catalog = $this->catalogService->getCatalogByIdentifier(
            $parameters['identifier']
        );

        $query = new Query();
        $query->filter = new ProductCriterionAdapter($catalog->getQuery());

        return $query;
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['identifier']);
    }

    public static function getName(): string
    {
        return 'Catalog';
    }
}
