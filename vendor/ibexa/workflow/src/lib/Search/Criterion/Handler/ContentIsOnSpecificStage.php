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
use Ibexa\Workflow\Persistence\Gateway\DoctrineGateway as WorkflowDoctrineGateway;
use Ibexa\Workflow\Search\Criterion\ContentIsOnSpecificStage as ContentIsOnSpecificStageCriterion;

final class ContentIsOnSpecificStage extends CriterionHandler
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof ContentIsOnSpecificStageCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $stageIdentifier = reset($criterion->value);
        $workflowIdentifier = $criterion->target;

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select(
                'wf.id'
            )->from(
                WorkflowDoctrineGateway::TABLE_MARKINGS,
                'm'
            )->join(
                'm',
                WorkflowDoctrineGateway::TABLE_WORKFLOWS,
                'wf',
                'm.workflow_id = wf.id'
            )->andWhere(
                $queryBuilder->expr()->eq(
                    'wf.workflow_name',
                    $queryBuilder->createNamedParameter($workflowIdentifier)
                )
            )->andWhere(
                $queryBuilder->expr()->eq(
                    'm.name',
                    $queryBuilder->createNamedParameter($stageIdentifier)
                )
            );

        return $queryBuilder->expr()->in(
            'w.id',
            $subSelect->getSQL()
        );
    }
}
