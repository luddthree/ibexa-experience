<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Permissions\Security\Limitation;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Limitation\TargetAwareType as SPITargetAwareLimitationType;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Permissions\Repository\Values\User\Limitation\FieldGroupLimitation;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList;

class FieldGroupLimitationType implements SPITargetAwareLimitationType
{
    /** @var \Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsList;

    /** @var \Ibexa\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    public function __construct(
        FieldsGroupsList $fieldsGroupsList,
        FieldTypeRegistry $fieldTypeRegistry,
        ContentService $contentService
    ) {
        $this->fieldsGroupsList = $fieldsGroupsList;
        $this->fieldTypeRegistry = $fieldTypeRegistry;
        $this->contentService = $contentService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected type/structure
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof FieldGroupLimitation) {
            throw new InvalidArgumentType(
                '$limitationValue',
                FieldGroupLimitation::class,
                $limitationValue
            );
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!is_string($value)) {
                throw new InvalidArgumentType(
                    "\$limitationValue->limitationValues[{$key}]",
                    'string',
                    $value
                );
            }
        }
    }

    /**
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];
        $missingFieldsGroups = [];
        $fieldsGroupIdentifiers = array_keys($this->fieldsGroupsList->getGroups());

        foreach ($limitationValue->limitationValues as $fieldGroupIdentifier) {
            if (!in_array($fieldGroupIdentifier, $fieldsGroupIdentifiers)) {
                $missingFieldsGroups[] = $fieldGroupIdentifier;
            }
        }

        if (!empty($missingFieldsGroups)) {
            $validationErrors[] = new ValidationError(
                "limitationValues[] => '%fieldsGroups%' fields group(s) do not exist",
                null,
                [
                    'fieldsGroups' => implode(', ', $missingFieldsGroups),
                ]
            );
        }

        return $validationErrors;
    }

    public function buildValue(array $limitationValues): APILimitationValue
    {
        return new FieldGroupLimitation(['limitationValues' => $limitationValues]);
    }

    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        ValueObject $object,
        array $targets = null
    ): ?bool {
        // the main focus here is an intent to update to a new Version
        foreach ($targets as $target) {
            if (!$target instanceof Target\Version) {
                continue;
            }

            $accessVote = $this->evaluateVersionTarget($target, $value, $object);

            // break evaluation of targets if there was explicit grant/deny
            if ($accessVote !== self::ACCESS_ABSTAIN) {
                return $accessVote;
            }
        }

        // in other cases we need to evaluate object
        return $this->evaluateObject($object, $value);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function getCriterion(
        APILimitationValue $value,
        APIUserReference $currentUser
    ): CriterionInterface {
        throw new NotImplementedException(__METHOD__);
    }

    public function valueSchema(): array
    {
        return [];
    }

    private function evaluateVersionTarget(
        Target\Version $target,
        APILimitationValue $value,
        ValueObject $object
    ) {
        if ($object instanceof ContentInfo) {
            $object = $this->contentService->loadContentByContentInfo($object);
        }

        if ($object instanceof Content) {
            $contentType = $object->getContentType();
            foreach ($target->updatedFields as $updatedField) {
                $fieldDefinition = $contentType->getFieldDefinition($updatedField->fieldDefIdentifier);
                $fieldGroup = $this->fieldsGroupsList->getFieldGroup($fieldDefinition);

                if (!in_array($fieldGroup, $value->limitationValues)) {
                    return self::ACCESS_DENIED;
                }
            }

            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_ABSTAIN;
    }

    private function evaluateObject(
        ValueObject $object,
        APILimitationValue $value
    ) {
        if ($object instanceof ContentCreateStruct) {
            $contentType = $object->contentType;

            foreach ($object->fields as $createdField) {
                $fieldDefinition = $contentType->getFieldDefinition($createdField->fieldDefIdentifier);
                $fieldGroup = $this->fieldsGroupsList->getFieldGroup($fieldDefinition);

                if (!in_array($fieldGroup, $value->limitationValues)) {
                    return self::ACCESS_DENIED;
                }
            }
        }

        return self::ACCESS_GRANTED;
    }
}

class_alias(FieldGroupLimitationType::class, 'Ibexa\Platform\Permissions\Security\Limitation\FieldGroupLimitationType');
