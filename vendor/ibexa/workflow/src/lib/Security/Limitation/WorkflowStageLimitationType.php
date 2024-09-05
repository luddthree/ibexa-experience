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
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
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
use Ibexa\Workflow\Value\Limitation\WorkflowStageLimitation as APIWorkflowStageLimitation;

class WorkflowStageLimitationType implements SPILimitationTypeInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     */
    public function __construct(
        WorkflowRegistryInterface $workflowRegistry,
        Repository $repository
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof APIWorkflowStageLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'APIWorkflowStageLimitation', $limitationValue);
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
            [$workflowName, $stageName] = explode(':', $value);

            try {
                $workflow = $this->workflowRegistry->getWorkflow($workflowName);
            } catch (NotFoundException $e) {
                $validationErrors[] = new ValidationError(
                    "Workflow '%workflowName%' does not exist",
                    null,
                    ['workflowName' => $workflowName]
                );
            }

            if (!\in_array($stageName, $workflow->getDefinition()->getPlaces(), true)) {
                $validationErrors[] = new ValidationError(
                    "Stage '%stageName%' in Workflow '%workflowName%' does not exist",
                    null,
                    [
                        'stageName' => $stageName,
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
        return new APIWorkflowStageLimitation(['limitationValues' => $limitationValues]);
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
        if (!$value instanceof APIWorkflowStageLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: APIWorkflowStageLimitation');
        }

        if (
            !$object instanceof ContentInfo
            && !$object instanceof Content
            && !$object instanceof VersionInfo
            && !$object instanceof ContentCreateStruct
        ) {
            throw new InvalidArgumentException(
                '$object',
                'Must be of type: Content, VersionInfo, ContentInfo, ContentCreateStruct'
            );
        }

        if ($object instanceof Content) {
            $subject = $this->loadContentObject((int) $object->id, (int) $object->getVersionInfo()->versionNo);
        } elseif ($object instanceof VersionInfo) {
            $subject = $this->loadContentObject((int) $object->getContentInfo()->id, (int) $object->versionNo);
        } elseif ($object instanceof ContentInfo) {
            $subject = $this->loadContentObject((int) $object->id, (int) $object->currentVersionNo);
        } elseif ($object instanceof ContentCreateStruct) {
            $subject = $object;
        } else {
            throw new InvalidArgumentException(
                '$object',
                'Must be of type: Content, VersionInfo, ContentInfo or ContentCreateStruct'
            );
        }

        if (empty($value->limitationValues)) {
            return Type::ACCESS_DENIED;
        }

        $markings = [];
        foreach ($value->limitationValues as $limitationValue) {
            [$workflowName, $stageName] = explode(':', $limitationValue);

            $workflow = $this->workflowRegistry->getWorkflow($workflowName);
            if (!array_key_exists($workflowName, $markings)) {
                $markings[$workflowName] = array_keys($workflow->getMarking($subject)->getPlaces());
            }

            if (\in_array($stageName, $markings[$workflowName], true)) {
                return Type::ACCESS_GRANTED;
            }
        }

        return Type::ACCESS_DENIED;
    }

    /**
     * {@inheritdoc}
     */
    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser): CriterionInterface
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Loads content object with permission check disabled.
     *
     * Caution: API Content object is needed to get Workflow information
     * but DomainMapper in not exposed. There is no other way around
     * than using ContentService even though this is prohibited in SPI.
     *
     * $this->repository->sudo() is used to avoid triggering permission check.
     *
     * @param int $contentId
     * @param int $versionNo
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    private function loadContentObject(int $contentId, int $versionNo): Content
    {
        return $this->repository->sudo(static function (Repository $repository) use ($contentId, $versionNo) {
            return $repository->getContentService()->loadContent($contentId, null, $versionNo, true);
        });
    }
}

class_alias(WorkflowStageLimitationType::class, 'EzSystems\EzPlatformWorkflow\Security\Limitation\WorkflowStageLimitationType');
