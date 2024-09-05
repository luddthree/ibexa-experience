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
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\Workflow\Persistence\Gateway\DoctrineGateway as WorkflowDoctrineGateway;
use Ibexa\Workflow\Search\Criterion\ContentIsOnInitialStage as ContentIsOnInitialStageCriterion;

class ContentIsOnInitialStage extends CriterionHandler
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    public function __construct(
        Connection $connection,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
    ) {
        parent::__construct($connection);

        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof ContentIsOnInitialStageCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $workflowDefinitionList = $this->workflowDefinitionMetadataRegistry->getAllWorkflowMetadata();

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select(
                'wf.id'
            )->distinct()->from(
                WorkflowDoctrineGateway::TABLE_MARKINGS,
                'm'
            )->leftJoin(
                'm',
                WorkflowDoctrineGateway::TABLE_WORKFLOWS,
                'wf',
                'm.workflow_id = wf.id'
            )->leftJoin(
                'wf',
                WorkflowDoctrineGateway::TABLE_TRANSITIONS,
                't',
                't.workflow_id = wf.id'
            );

        $conditions = [];
        foreach ($workflowDefinitionList as $workflowName => $workflowDefinitionMetadata) {
            $conditions[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('wf.workflow_name', $this->connection->quote($workflowName)),
                $queryBuilder->expr()->in('m.name', $this->connection->quote($workflowDefinitionMetadata->getInitialStage()))
            );
        }

        if (!empty($conditions)) {
            $subSelect->orWhere(...$conditions);
        }

        $subSelect->andWhere(
            $subSelect->expr()->isNull('t.id')
        );

        $whereClause = $subSelect->expr()->in(
            'w.id',
            $subSelect->getSQL()
        );

        $negation = reset($criterion->value);

        if ($negation && empty($conditions)) {
            return $queryBuilder->expr()->eq(1, 1);
        }

        return ($negation)
            ? sprintf('NOT (%s)', $whereClause)
            : $whereClause;
    }
}

class_alias(ContentIsOnInitialStage::class, 'EzSystems\EzPlatformWorkflow\Search\Criterion\Handler\ContentIsOnInitialStage');
