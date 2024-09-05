<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\SiteFactory\Persistence\Site\Query\Criterion;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;
use Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter;
use Ibexa\SiteFactory\Persistence\Site\Query\CriterionHandler;

class MatchAll implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\MatchAll;
    }

    public function handle(CriteriaConverter $converter, QueryBuilder $query, Criterion $criterion)
    {
        return $query->expr()->eq(1, 1);
    }
}

class_alias(MatchAll::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Query\Criterion\MatchAll');
