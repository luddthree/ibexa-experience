<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Factory\Registry;

use Ibexa\Contracts\Workflow\Provider\WorkflowProviderInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Workflow\Registry\WorkflowRegistry;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;

class WorkflowRegistryFactory
{
    /** @var \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface */
    private $supportStrategy;

    /** @var \Ibexa\Contracts\Workflow\Provider\WorkflowProviderInterface[] */
    protected $providers;

    /**
     * @param \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface $supportStrategy
     * @param \Ibexa\Contracts\Workflow\Provider\WorkflowProviderInterface[] $providers
     */
    public function __construct(WorkflowSupportStrategyInterface $supportStrategy, array $providers)
    {
        $this->supportStrategy = $supportStrategy;
        $this->providers = $providers;
    }

    /**
     * @return \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface
     */
    public function create(): WorkflowRegistryInterface
    {
        /** @var \Symfony\Component\Workflow\Workflow[] $workflows */
        $workflows = array_map(
            static function (WorkflowProviderInterface $provider): array {
                return $provider->getWorkflows();
            },
            $this->providers
        );

        return new WorkflowRegistry($this->supportStrategy, array_replace(...$workflows ?? []));
    }
}

class_alias(WorkflowRegistryFactory::class, 'EzSystems\EzPlatformWorkflow\Factory\Registry\WorkflowRegistryFactory');
