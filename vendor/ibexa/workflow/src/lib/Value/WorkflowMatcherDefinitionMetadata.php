<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

class WorkflowMatcherDefinitionMetadata
{
    /** @var mixed array */
    protected $configuration;

    /**
     * @param $configuration
     */
    public function __construct($configuration)
    {
        $this->setConfiguration($configuration);
    }

    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration): void
    {
        $this->configuration = $configuration;
    }
}

class_alias(WorkflowMatcherDefinitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowMatcherDefinitionMetadata');
