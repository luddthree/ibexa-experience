<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\SiteFactory\Persistence\Site\Query\Criterion;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;
use Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter;

class MatchName extends Matcher
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\MatchName;
    }

    public function handle(CriteriaConverter $converter, QueryBuilder $query, Criterion $criterion)
    {
        $name = '%' . addcslashes($criterion->name, '%_') . '%';

        /** @var Criterion\MatchName $criterion */
        $query->setParameter('name', $name);

        return $query->expr()->like('name', ':name');
    }
}

class_alias(MatchName::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Query\Criterion\MatchName');
