<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Registry;

use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Workflow\Exception\NotFoundException;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;
use Symfony\Component\Workflow\Workflow;

class WorkflowRegistry implements WorkflowRegistryInterface
{
    /** @var \Symfony\Component\Workflow\Workflow[] */
    protected $workflows;

    /** @var \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface */
    private $supportStrategy;

    /**
     * @param \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface $supportStrategy
     * @param \Symfony\Component\Workflow\Workflow[]
     */
    public function __construct(
        WorkflowSupportStrategyInterface $supportStrategy,
        array $workflows = []
    ) {
        $this->supportStrategy = $supportStrategy;
        $this->workflows = $workflows;
    }

    /**
     * @return \Symfony\Component\Workflow\Workflow[]
     */
    public function getWorkflows(): array
    {
        return $this->workflows;
    }

    /**
     * @param \Symfony\Component\Workflow\Workflow[] $workflows
     */
    public function setWorkflows(array $workflows): void
    {
        foreach ($workflows as $identifier => $workflow) {
            $this->setWorkflow($identifier, $workflow);
        }
    }

    /**
     * @param string $identifier
     * @param \Symfony\Component\Workflow\Workflow $workflow
     */
    public function setWorkflow(string $identifier, Workflow $workflow): void
    {
        $this->workflows[$identifier] = $workflow;
    }

    /**
     * @param $subject
     *
     * @return array
     */
    public function getSupportedWorkflows($subject): array
    {
        $workflows = [];

        foreach ($this->workflows as $identifier => $workflow) {
            if ($this->supportStrategy->supports($workflow, $subject)) {
                $workflows[$identifier] = $workflow;
            }
        }

        return $workflows;
    }

    /**
     * @param string $identifier
     * @param $subject
     *
     * @return \Symfony\Component\Workflow\Workflow
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getSupportedWorkflow(string $identifier, $subject): Workflow
    {
        $workflow = $this->getWorkflow($identifier);

        if ($this->supportStrategy->supports($workflow, $subject)) {
            return $workflow;
        }

        throw new NotFoundException('Workflow', $identifier);
    }

    /**
     * @param string $identifier
     *
     * @return \Symfony\Component\Workflow\Workflow
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getWorkflow(string $identifier): Workflow
    {
        if ($this->hasWorkflow($identifier)) {
            return $this->workflows[$identifier];
        }

        throw new NotFoundException('Workflow', $identifier);
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasWorkflow(string $identifier): bool
    {
        return isset($this->workflows[$identifier]);
    }
}

class_alias(WorkflowRegistry::class, 'EzSystems\EzPlatformWorkflow\Registry\WorkflowRegistry');
