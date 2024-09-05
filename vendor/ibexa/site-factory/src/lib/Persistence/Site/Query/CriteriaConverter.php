<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\SiteFactory\Persistence\Site\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;

class CriteriaConverter
{
    /**
     * Criterion handlers.
     *
     * @var \Ibexa\SiteFactory\Persistence\Site\Query\CriterionHandler[]
     */
    protected $handlers;

    /**
     * Construct from an optional array of Criterion handlers.
     *
     * @param \Ibexa\SiteFactory\Persistence\Site\Query\CriterionHandler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds handler.
     *
     * @param \Ibexa\SiteFactory\Persistence\Site\Query\CriterionHandler $handler
     */
    public function addHandler(CriterionHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Generic converter of criteria into query fragments.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException if Criterion is not applicable to its target
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param \Ibexa\Contracts\SiteFactory\Values\Query\Criterion $criterion
     *
     * @return \Ibexa\Core\Persistence\Database\Expression|string
     */
    public function convertCriteria(QueryBuilder $query, Criterion $criterion)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->accept($criterion)) {
                return $handler->handle($this, $query, $criterion);
            }
        }

        throw new NotImplementedException(
            'No visitor available for: ' . get_class($criterion)
        );
    }
}

class_alias(CriteriaConverter::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Query\CriteriaConverter');
