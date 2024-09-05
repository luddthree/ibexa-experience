<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Provider;

interface WorkflowDefinitionMetadataProviderInterface
{
    /**
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[]
     */
    public function getWorkflowDefinitionsMetadata(): array;
}

class_alias(WorkflowDefinitionMetadataProviderInterface::class, 'EzSystems\EzPlatformWorkflow\Provider\WorkflowDefinitionMetadataProviderInterface');
