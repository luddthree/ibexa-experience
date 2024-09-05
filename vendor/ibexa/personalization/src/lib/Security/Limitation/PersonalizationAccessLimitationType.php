<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security\Limitation;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation;
use Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation as APIPersonalizationAccessLimitation;
use Ibexa\Personalization\Value\Security\PersonalizationSecurityContext;

final class PersonalizationAccessLimitationType implements SPILimitationTypeInterface
{
    private PersonalizationLimitationListLoaderInterface $accessListLoader;

    public function __construct(PersonalizationLimitationListLoaderInterface $accessListLoader)
    {
        $this->accessListLoader = $accessListLoader;
    }

    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof APIPersonalizationAccessLimitation) {
            throw new InvalidArgumentType(
                '$limitationValue',
                'APIPersonalizationAccessLimitation',
                $limitationValue
            );
        }

        if (!\is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!\is_string($value) && !\is_int($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string|int', $value);
            }
        }
    }

    public function buildValue(array $limitationValues): PersonalizationAccessLimitation
    {
        return new APIPersonalizationAccessLimitation(['limitationValues' => $limitationValues]);
    }

    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): ?bool {
        if (!$value instanceof APIPersonalizationAccessLimitation) {
            throw new InvalidArgumentException(
                '$value',
                'Must be of type: APIPersonalizationAccessLimitation'
            );
        }

        if (!$object instanceof PersonalizationSecurityContext) {
            throw new InvalidArgumentException(
                '$object',
                'Must be of type: PersonalizationSecurityContext'
            );
        }

        if (empty($value->limitationValues) || empty($object->customerId)) {
            return false;
        }

        return in_array((string)$object->customerId, $value->limitationValues, true);
    }

    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser): CriterionInterface
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!isset($this->accessListLoader->getList()[$value])) {
                $validationErrors[] = new ValidationError(
                    "\$limitationValue->limitationValues[%key%] => Invalid CustomerId \"$value\"",
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

    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}

class_alias(PersonalizationAccessLimitationType::class, 'Ibexa\Platform\Personalization\Security\Limitation\PersonalizationAccessLimitationType');
