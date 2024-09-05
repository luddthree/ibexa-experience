<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Permission\Limitation;

use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Limitation\Type as LimitationType;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as LimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\Limitation\ApplicationStateLimitation as ApplicationStateLimitationValue;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\CorporateAccount\Permission\Limitation\Target\ApplicationStateTarget;

final class ApplicationStateLimitation implements LimitationType
{
    public const LIMITATION_VALUES_ARRAYS = ['from', 'to'];

    public function acceptValue(LimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof ApplicationStateLimitationValue) {
            throw new InvalidArgumentType(
                '$limitationValue',
                'ApplicationStateLimitationValue',
                $limitationValue
            );
        }

        if (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach (self::LIMITATION_VALUES_ARRAYS as $array) {
            $limitationValue->limitationValues[$array] ??= [];
            foreach ($limitationValue->limitationValues[$array] as $key => $value) {
                if (!is_string($value)) {
                    throw new InvalidArgumentType(
                        sprintf(
                            '$limitationValue->limitationValues[%s][%s]',
                            $key,
                            $array
                        ),
                        'string',
                        $limitationValue->limitationValues[$array][$key]
                    );
                }
            }
        }
    }

    public function validate(LimitationValue $limitationValue): iterable
    {
        $validationErrors = [];

        foreach (self::LIMITATION_VALUES_ARRAYS as $array) {
            $limitationValue->limitationValues[$array] ??= [];
            foreach ($limitationValue->limitationValues[$array] as $key => $value) {
                if (is_string($value)) {
                    continue;
                }

                $validationErrors[] = new ValidationError(
                    "limitationValues[%array%][%key%] => '%value%' must be a string",
                    null,
                    [
                        'array' => $array,
                        'value' => $value,
                        'key' => $key,
                    ]
                );
            }
        }

        return $validationErrors;
    }

    public function buildValue(array $limitationValues): ApplicationStateLimitationValue
    {
        return new ApplicationStateLimitationValue(['limitationValues' => $limitationValues]);
    }

    public function evaluate(
        LimitationValue $value,
        UserReference $currentUser,
        ValueObject $object,
        array $targets = null
    ): ?bool {
        if (!$object instanceof Application || empty($targets)) {
            return self::ACCESS_ABSTAIN;
        }

        $applicationStateTarget = $this->getApplicationStateTarget($targets);

        if ($applicationStateTarget === null) {
            return self::ACCESS_ABSTAIN;
        }

        $fromLimitationGranted = empty($value->limitationValues['from']) || in_array(
            $applicationStateTarget->getFrom(),
            $value->limitationValues['from'],
            true
        );
        $toLimitationGranted = empty($value->limitationValues['to']) || in_array(
            $applicationStateTarget->getTo(),
            $value->limitationValues['to'],
            true
        );

        if ($fromLimitationGranted && $toLimitationGranted) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }

    public function getCriterion(LimitationValue $value, UserReference $currentUser): Criterion
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\ValueObject> $targets
     */
    private function getApplicationStateTarget(array $targets): ?ApplicationStateTarget
    {
        foreach ($targets as $target) {
            if ($target instanceof ApplicationStateTarget) {
                return $target;
            }
        }

        return null;
    }
}
