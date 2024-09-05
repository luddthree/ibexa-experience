<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\SupportStrategy;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Workflow\SupportStrategy\Matcher\MatcherRegistry;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class MatcherSupportStrategy implements WorkflowSupportStrategyInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowMetadataRegistry;

    /** @var \Ibexa\Workflow\SupportStrategy\Matcher\MatcherRegistry */
    private $matcherRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowMetadataRegistry
     * @param \Ibexa\Workflow\SupportStrategy\Matcher\MatcherRegistry $matcherRegistry
     */
    public function __construct(
        WorkflowDefinitionMetadataRegistryInterface $workflowMetadataRegistry,
        MatcherRegistry $matcherRegistry
    ) {
        $this->matcherRegistry = $matcherRegistry;
        $this->workflowMetadataRegistry = $workflowMetadataRegistry;
    }

    /**
     * @param \Symfony\Component\Workflow\WorkflowInterface $workflow
     * @param object $subject
     *
     * @return bool
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function supports(WorkflowInterface $workflow, $subject): bool
    {
        // Support only API repository domain Value Objects
        if (!$subject instanceof ValueObject) {
            return false;
        }

        $identifier = $workflow->getName();
        $matchers = $this->workflowMetadataRegistry->getWorkflowMetadata($identifier)->getMatchersMetadata();

        foreach ($matchers as $identifier => $configuration) {
            $matcher = $this->matcherRegistry->get($identifier);

            if (!$matcher->match($subject, (array)$configuration->getConfiguration())) {
                return false;
            }
        }

        return true;
    }
}

class_alias(MatcherSupportStrategy::class, 'EzSystems\EzPlatformWorkflow\SupportStrategy\MatcherSupportStrategy');
