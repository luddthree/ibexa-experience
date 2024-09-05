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
final class FieldDefinitionIdentifierValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof FieldDefinitionIdentifier) {
            throw new UnexpectedTypeException($constraint, FieldDefinitionIdentifier::class);
        }

        $fieldDefinition = $constraint->contentType->getFieldDefinition($value);

        if (null === $fieldDefinition) {
            $this->context->buildViolation(
                '{{ value }} is an invalid Field Definition identifier for {{ content_type }} ' .
                'content type in Field Value structure'
            )
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter(
                    '{{ content_type }}',
                    $this->formatValue($constraint->contentType->identifier)
                )
                ->setCode(FieldDefinitionIdentifier::IS_INVALID_FIELD_DEFINITION_IDENTIFIER_ERROR)
                ->addViolation();
        }
    }
}
