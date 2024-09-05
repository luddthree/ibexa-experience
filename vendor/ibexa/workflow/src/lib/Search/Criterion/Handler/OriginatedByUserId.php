<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Search\Criterion\Handler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\Workflow\Search\Criterion\OriginatedByUserId as OriginatedByUserIdCriterion;

class OriginatedByUserId extends CriterionHandler
{
    public function __construct(
        Connection $connection
    ) {
        parent::__construct($connection);
    }

    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof OriginatedByUserIdCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $userId = reset($criterion->value);

        $queryBuilder->setParameter(':initial_owner_id', $userId);

        return $queryBuilder->expr()->in(
            'w.initial_owner_id',
            ':initial_owner_id'
        );
    }
}

class_alias(OriginatedByUserId::class, 'EzSystems\EzPlatformWorkflow\Search\Criterion\Handler\OriginatedByUserId');
