<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @internal
 */
final class MediaTypeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof MediaType) {
            throw new UnexpectedTypeException($constraint, MediaType::class);
        }

        if (is_string($value) && $this->isMediaType($value, $constraint->expectedResourceName)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setParameter('{{ expectedResourceName }}', $constraint->expectedResourceName)
            ->setCode(MediaType::IS_MALFORMED_MEDIA_TYPE_ERROR)
            ->addViolation();
    }

    private function isMediaType(string $value, string $expectedResourceName): bool
    {
        if (preg_match('@^application/vnd\.ibexa\.api\.(?<resourceName>\w+)\+(xml|json)@', $value, $matches)) {
            return $matches['resourceName'] === $expectedResourceName;
        }

        return false;
    }
}
