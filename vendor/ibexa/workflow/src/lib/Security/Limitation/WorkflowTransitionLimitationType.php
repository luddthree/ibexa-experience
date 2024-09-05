<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Security\Limitation;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\Value\Limitation\WorkflowTransitionLimitation as APIWorkflowTransitionLimitation;
use Ibexa\Workflow\Value\WorkflowTransition;
use Symfony\Component\Workflow\Transition;

class WorkflowTransitionLimitationType implements SPILimitationTypeInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     */
    public function __construct(WorkflowRegistryInterface $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof APIWorkflowTransitionLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'APIWorkflowTransitionLimitation', $limitationValue);
        }

        if (!\is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!\is_string($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string', $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];
        foreach ($limitationValue->limitationValues as $value) {
            list($workflowName, $transitionName) = explode(':', $value);

            try {
                $workflow = $this->workflowRegistry->getWorkflow($workflowName);
            } catch (NotFoundException $e) {
                $validationErrors[] = new ValidationError(
                    "Workflow '%workflowName%' does not exist",
                    null,
                    ['workflowName' => $workflowName]
                );
            }

            $transitionNames = array_map(
                static function (Transition $transition): string {
                    return $transition->getName();
                },
                $workflow->getDefinition()->getTransitions()
            );

            if (!\in_array($transitionName, $transitionNames, true)) {
                $validationErrors[] = new ValidationError(
                    "Transition '%transitionName%' in Workflow '%workflowName%' does not exist",
                    null,
                    [
                        'transitionName' => $transitionName,
                        'workflowName' => $workflowName,
                    ]
                );
            }
        }

        return $validationErrors;
    }

    /**
     * {@inheritdoc}
     */
    public function buildValue(array $limitationValues): Limitation
    {
        return new APIWorkflowTransitionLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        ValueObject $object,
        array $targets = null
    ): ?bool {
        if (!$value instanceof APIWorkflowTransitionLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: APIWorkflowTransitionLimitation');
        }

        if (!$object instanceof ContentInfo && !$object instanceof Content && !$object instanceof ContentCreateStruct && !$object instanceof VersionInfo) {
            throw new InvalidArgumentException('$object', 'Must be of type: Content, ContentCreateStruct, VersionInfo or ContentInfo');
        }

        $targets = array_filter($targets, static function ($target): bool {
            return $target instanceof WorkflowTransition;
        });

        if (empty($targets)) {
            throw new InvalidArgumentException('$targets', sprintf('Must contain objects of type: %s', WorkflowTransition::class));
        }

        if (empty($value->limitationValues)) {
            return Type::ACCESS_DENIED;
        }

        $limitationValues = $this->processRawLimitationValues($value->limitationValues);

        foreach ($targets as $target) {
            if (!\in_array($target, $limitationValues)) {
                return Type::ACCESS_DENIED;
            }
        }

        return Type::ACCESS_GRANTED;
    }

    /**
     * @param string[] $limitationValues
     *
     * @return \Ibexa\Workflow\Value\WorkflowTransition[]
     */
    private function processRawLimitationValues(array $limitationValues): array
    {
        $processedLimitationValues = [];
        foreach ($limitationValues as $limitationValue) {
            list($workflowName, $transitionName) = explode(':', $limitationValue);

            $processedLimitationValues[] = new WorkflowTransition([
                'workflow' => $workflowName,
                'transition' => $transitionName,
            ]);
        }

        return $processedLimitationValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser)
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}

class_alias(WorkflowTransitionLimitationType::class, 'EzSystems\EzPlatformWorkflow\Security\Limitation\WorkflowTransitionLimitationType');
