<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Constraint;

use Ibexa\Contracts\Rest\Exceptions\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @internal
 */
final class ResourceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Resource) {
            throw new UnexpectedTypeException($constraint, Resource::class);
        }

        if (is_string($value) && $this->isCorrectResourcePath($value, $constraint)) {
            return;
        }
        $builder = $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setCode(Resource::IS_MALFORMED_RESOURCE_ERROR);

        if (null !== $constraint->parserException) {
            $builder->setParameter(
                '{{ parserExceptionMessage }}',
                $constraint->parserException->getMessage()
            );
            $builder->setParameter(
                '{{ parserExceptionClass }}',
                get_class($constraint->parserException)
            );
        }
        $builder->addViolation();
    }

    private function matchesRoute(string $value, Resource $constraint): bool
    {
        $parsed = null;
        try {
            $parsed = $constraint->requestParser->parse($value);
        } catch (InvalidArgumentException $exception) {
            $constraint->parserException = $exception;
        }

        return is_array($parsed) && !empty($parsed['_route']);
    }

    private function isCorrectResourcePath(string $value, Resource $constraint): bool
    {
        return !empty($value) && $this->matchesRoute($value, $constraint);
    }
}
