<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Pagerfanta\Adapter;

use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Value\WorkflowMetadataQuery;
use Pagerfanta\Adapter\AdapterInterface;

final class WorkflowMetadataAdapter implements AdapterInterface
{
    public function __construct(WorkflowServiceInterface $workflowService, WorkflowMetadataQuery $query)
    {
        $this->workflowService = $workflowService;
        $this->query = $query;
    }

    public function getNbResults(): int
    {
        $countQuery = clone $this->query;
        $countQuery->limit = 0;

        return $this->workflowService->loadWorkflowMetadataList($countQuery)->totalCount;
    }

    public function getSlice($offset, $length): iterable
    {
        $selectQuery = clone $this->query;
        $selectQuery->offset = $offset;
        $selectQuery->limit = $length;
        $selectQuery->performCount = false;

        return $this->workflowService->loadWorkflowMetadataList($selectQuery);
    }
}
