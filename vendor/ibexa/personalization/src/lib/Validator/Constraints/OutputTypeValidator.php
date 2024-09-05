<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Value\Content\AbstractItemType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class OutputTypeValidator extends ConstraintValidator
{
    /**
     * @throws \JsonException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof OutputType) {
            throw new UnexpectedTypeException($constraint, OutputType::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $itemType = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        if (!is_a($itemType['type'], AbstractItemType::class, true)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ type }}', AbstractItemType::class);
        }
    }
}
