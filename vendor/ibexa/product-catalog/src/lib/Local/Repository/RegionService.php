<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\ProductCatalog\Local\Repository\Region\RegionProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\RegionList;

final class RegionService implements RegionServiceInterface
{
    private RegionProviderInterface $regionProvider;

    private CriterionMapper $criterionMapper;

    public function __construct(
        RegionProviderInterface $regionProvider,
        CriterionMapper $criterionMapper
    ) {
        $this->regionProvider = $regionProvider;
        $this->criterionMapper = $criterionMapper;
    }

    public function findRegions(?RegionQuery $query = null): RegionListInterface
    {
        $query ??= new RegionQuery();

        $collection = new ArrayCollection();
        foreach ($this->regionProvider->getRegions() as $region) {
            $collection->add($region);
        }

        if ($query->getQuery()) {
            $criteria = $this->criterionMapper->handle($query->getQuery());
            $collection = $collection->matching(Criteria::create()->andWhere($criteria));
        }

        $count = count($collection);

        $collection = $collection->matching(
            Criteria::create()
                ->setMaxResults($query->getLimit())
                ->setFirstResult($query->getOffset())
        );

        return new RegionList($collection->toArray(), $count);
    }

    public function getRegion(string $identifier): RegionInterface
    {
        return $this->regionProvider->getRegion($identifier);
    }
}
