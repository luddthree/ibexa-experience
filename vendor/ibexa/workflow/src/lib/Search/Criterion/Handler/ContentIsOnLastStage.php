<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Search\Criterion\Handler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\Workflow\Persistence\Gateway\DoctrineGateway as WorkflowDoctrineGateway;
use Ibexa\Workflow\Search\Criterion\ContentIsOnLastStage as ContentIsOnLastStageCriterion;
use Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata;

class ContentIsOnLastStage extends CriterionHandler
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
        return $criterion instanceof ContentIsOnLastStageCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $lastStagesMap = [];
        $workflowDefinitionList = $this->workflowDefinitionMetadataRegistry->getAllWorkflowMetadata();
        foreach ($workflowDefinitionList as $workflowIdentifier => $workflowDefinitionMetadata) {
            $lastStages = array_filter($workflowDefinitionMetadata->getStagesMetadata(), static function (WorkflowStageDefinitionMetadata $markingMetadata): bool {
                return $markingMetadata->isLastStage();
            });

            if (empty($lastStages)) {
                continue;
            }

            $lastStagesMap[$workflowIdentifier] = array_keys($lastStages);
        }

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select(
                'wf.id'
            )->from(
                WorkflowDoctrineGateway::TABLE_MARKINGS,
                'm'
            )->leftJoin(
                'm',
                WorkflowDoctrineGateway::TABLE_WORKFLOWS,
                'wf',
                'm.workflow_id = wf.id'
            );

        $conditions = [];
        foreach ($lastStagesMap as $workflowIdentifier => $stageNames) {
            $conditions[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq(
                    'wf.workflow_name',
                    $queryBuilder->createNamedParameter($workflowIdentifier, ParameterType::STRING)
                ),
                $queryBuilder->expr()->in(
                    'm.name',
                    $queryBuilder->createNamedParameter($stageNames, Connection::PARAM_STR_ARRAY)
                )
            );
        }

        if (!empty($conditions)) {
            $subSelect->where($queryBuilder->expr()->orX(...$conditions));
        }

        $whereClause = $queryBuilder->expr()->in(
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

class_alias(ContentIsOnLastStage::class, 'EzSystems\EzPlatformWorkflow\Search\Criterion\Handler\ContentIsOnLastStage');
