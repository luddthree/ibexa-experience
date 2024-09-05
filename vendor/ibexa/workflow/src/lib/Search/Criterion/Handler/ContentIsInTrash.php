<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Search\Criterion\Handler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\Workflow\Persistence\Gateway\DoctrineGateway as WorkflowDoctrineGateway;
use Ibexa\Workflow\Search\Criterion\ContentIsInTrash as ContentIsInTrashCriterion;

/**
 * @internal
 */
final class ContentIsInTrash extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof ContentIsInTrashCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $subSelect = $this->createSubqueryForContentWithTrashStatus($queryBuilder);

        $whereClause = $subSelect->expr()->in(
            'w.id',
            $subSelect->getSQL()
        );

        return $whereClause;
    }

    private function createSubqueryForContentWithTrashStatus(QueryBuilder $query): QueryBuilder
    {
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select(
                'wf.id'
            )->from(
                WorkflowDoctrineGateway::TABLE_WORKFLOWS,
                'wf'
            )->leftJoin(
                'wf',
                'ezcontentobject',
                'c',
                'wf.content_id = c.id'
            )->andWhere(
                $subSelect->expr()->eq('c.status', ContentInfo::STATUS_TRASHED)
            );

        return $subSelect;
    }
}

class_alias(ContentIsInTrash::class, 'EzSystems\EzPlatformWorkflow\Search\Criterion\Handler\ContentIsInTrash');
