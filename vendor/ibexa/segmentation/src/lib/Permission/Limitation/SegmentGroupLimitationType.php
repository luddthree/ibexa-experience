<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Permission\Limitation;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Segmentation\Exception\Persistence\SegmentGroupNotFoundException;
use Ibexa\Segmentation\Permission\Limitation\Value\SegmentGroupLimitation;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;

final class SegmentGroupLimitationType implements SPILimitationTypeInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $segmentationHandler;

    public function __construct(HandlerInterface $segmentationHandler)
    {
        $this->segmentationHandler = $segmentationHandler;
    }

    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof SegmentGroupLimitation) {
            throw new InvalidArgumentType('$limitationValue', SegmentGroupLimitation::class, $limitationValue);
        }

        if (!\is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!\is_int($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string', $value);
            }
        }
    }

    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];
        /** @var int $segmentGroupId */
        foreach ($limitationValue->limitationValues as $segmentGroupId) {
            try {
                $segmentGroup = $this->segmentationHandler->loadSegmentGroupById($segmentGroupId);
            } catch (SegmentGroupNotFoundException $e) {
                $validationErrors[] = new ValidationError(
                    "Segment Group (ID: '%segmentGroupId%') does not exist",
                    null,
                    ['segmentGroupId' => $segmentGroupId]
                );
            }
        }

        return $validationErrors;
    }

    public function buildValue(array $limitationValues): Limitation
    {
        return new SegmentGroupLimitation(['limitationValues' => $limitationValues]);
    }

    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        ValueObject $object,
        array $targets = null
    ): ?bool {
        if (!$value instanceof SegmentGroupLimitation) {
            throw new InvalidArgumentException('$value', \sprintf('Must be of type: %s', SegmentGroupLimitation::class));
        }

        $groupId = null;
        if ($object instanceof Segment) {
            /** @var \Ibexa\Segmentation\Value\Segment $object */
            $groupId = $object->group->id;
        } elseif ($object instanceof SegmentCreateStruct) {
            /** @var \Ibexa\Segmentation\Value\SegmentCreateStruct $object */
            $groupId = $object->group->id;
        } elseif ($object instanceof SegmentUpdateStruct) {
            /** @var \Ibexa\Segmentation\Value\SegmentUpdateStruct $object */
            $groupId = $object->group->id;
        } else {
            throw new InvalidArgumentException(
                '$object',
                'Must be of type: Segment, SegmentCreateStruct, SegmentUpdateStruct'
            );
        }

        if (empty($value->limitationValues)) {
            return Type::ACCESS_DENIED;
        }

        return \in_array($groupId, $value->limitationValues)
            ? Type::ACCESS_GRANTED
            : Type::ACCESS_DENIED;
    }

    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser)
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}

class_alias(SegmentGroupLimitationType::class, 'Ibexa\Platform\Segmentation\Permission\Limitation\SegmentGroupLimitationType');
