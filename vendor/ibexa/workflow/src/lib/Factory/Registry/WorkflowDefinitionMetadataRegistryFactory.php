<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Factory\Registry;

use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Workflow\Registry\WorkflowDefinitionMetadataRegistry;

class WorkflowDefinitionMetadataRegistryFactory
{
    /** @var \Ibexa\Contracts\Workflow\Provider\WorkflowDefinitionMetadataProviderInterface[] */
    protected $providers;

    /**
     * @param \Ibexa\Contracts\Workflow\Provider\WorkflowDefinitionMetadataProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface
     */
    public function create(): WorkflowDefinitionMetadataRegistryInterface
    {
        $metadata = [];
        foreach ($this->providers as $provider) {
            $metadata = array_merge($metadata, $provider->getWorkflowDefinitionsMetadata());
        }

        return new WorkflowDefinitionMetadataRegistry($metadata);
    }
}

class_alias(WorkflowDefinitionMetadataRegistryFactory::class, 'EzSystems\EzPlatformWorkflow\Factory\Registry\WorkflowDefinitionMetadataRegistryFactory');
