<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Permission;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;

final class ActivityLogOwnerLimitationType implements SPILimitationTypeInterface
{
    public function acceptValue(Limitation $limitationValue): void
    {
        if (!$limitationValue instanceof ActivityLogOwnerLimitation) {
            throw new InvalidArgumentType('$limitationValue', ActivityLogOwnerLimitation::class, $limitationValue);
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if ((int)$value != $value) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'int', $value);
            }

            $limitationValue->limitationValues[$key] = (int)$value;
        }
    }

    public function validate(Limitation $limitationValue): array
    {
        $validationErrors = [];
        foreach ($limitationValue->limitationValues as $key => $value) {
            if ($value !== 1) {
                $validationErrors[] = new ValidationError(
                    "limitationValues[%key%] => '%value%' must be 1 (owner)",
                    null,
                    [
                        'value' => $value,
                        'key' => $key,
                    ]
                );
            }
        }

        return $validationErrors;
    }

    public function buildValue(array $limitationValues): ActivityLogOwnerLimitation
    {
        return new ActivityLogOwnerLimitation(['limitationValues' => $limitationValues]);
    }

    public function evaluate(
        Limitation $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): ?bool {
        if (!$value instanceof ActivityLogOwnerLimitation) {
            throw new InvalidArgumentException(
                '$value',
                sprintf('Must be of type: %s', ActivityLogOwnerLimitation::class)
            );
        }

        throw new NotImplementedException(__METHOD__);
    }

    public function getCriterion(Limitation $value, APIUserReference $currentUser)
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}
