<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

interface ListFilterInterface
{
    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata[] $workflowMetadataList
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    public function filter(array $workflowMetadataList): array;
}

class_alias(ListFilterInterface::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ListFilter\ListFilterInterface');
