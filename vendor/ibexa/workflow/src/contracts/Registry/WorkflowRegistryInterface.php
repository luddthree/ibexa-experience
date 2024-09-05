<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Registry;

use Symfony\Component\Workflow\Workflow;

interface WorkflowRegistryInterface
{
    /**
     * @param string $identifier
     * @param \Symfony\Component\Workflow\Workflow $workflow
     */
    public function setWorkflow(string $identifier, Workflow $workflow): void;

    /**
     * @param \Symfony\Component\Workflow\Workflow[] $workflows
     */
    public function setWorkflows(array $workflows): void;

    /**
     * @return \Symfony\Component\Workflow\Workflow[]
     */
    public function getWorkflows(): array;

    /**
     * @param $subject
     *
     * @return \Symfony\Component\Workflow\Workflow[]
     */
    public function getSupportedWorkflows($subject): array;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasWorkflow(string $identifier): bool;

    /**
     * @param string $identifier
     *
     * @return \Symfony\Component\Workflow\Workflow
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getWorkflow(string $identifier): Workflow;

    /**
     * @param string $identifier
     * @param $subject
     *
     * @return \Symfony\Component\Workflow\Workflow
     */
    public function getSupportedWorkflow(string $identifier, $subject): Workflow;
}

class_alias(WorkflowRegistryInterface::class, 'EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistryInterface');
