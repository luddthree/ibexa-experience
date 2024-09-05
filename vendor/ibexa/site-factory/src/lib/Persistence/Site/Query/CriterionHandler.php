<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\SiteFactory\Persistence\Site\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;

interface CriterionHandler
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \Ibexa\Contracts\SiteFactory\Values\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion);

    /**
     * Generate query expression for a Criterion this handler accepts.
     *
     * accept() must be called before calling this method.
     *
     * @param \Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter $converter
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param \Ibexa\Contracts\SiteFactory\Values\Query\Criterion $criterion
     *
     * @return \Ibexa\Core\Persistence\Database\Expression|string
     */
    public function handle(CriteriaConverter $converter, QueryBuilder $query, Criterion $criterion);
}

class_alias(CriterionHandler::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Query\CriterionHandler');
