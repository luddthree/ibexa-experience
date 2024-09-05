<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class OptionsBagConstraintValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->disableMagicMethods()
            ->getPropertyAccessor();
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof OptionsBagConstraint) {
            throw new UnexpectedTypeException($constraint, OptionsBagConstraint::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof OptionsBag) {
            throw new UnexpectedValueException($value, OptionsBag::class);
        }

        // We need to keep the initialized context when CollectionValidator
        // calls itself recursively (Collection constraints can be nested).
        // Since the context of the validator is overwritten when initialize()
        // is called for the nested constraint, the outer validator is
        // acting on the wrong context when the nested validation terminates.
        $context = $this->context;
        $context->setNode($value, $value, null, $context->getPropertyPath());

        $value = $value->all();

        foreach ($constraint->fields as $field => $fieldConstraint) {
            $option = null;
            try {
                $option = $this->accessor->getValue($value, $field);
                $existsInArray = true;
            } catch (NoSuchIndexException $e) {
                $existsInArray = false;
            }

            if ($existsInArray) {
                if (count($fieldConstraint->constraints) > 0) {
                    $context->getValidator()
                        ->inContext($context)
                        ->atPath($field)
                        ->validate($option, $fieldConstraint->constraints);
                }
            } elseif (!$fieldConstraint instanceof Optional && !$constraint->allowMissingFields) {
                $context->buildViolation($constraint->missingFieldsMessage)
                    ->atPath($field)
                    ->setParameter('{{ field }}', $this->formatValue($field))
                    ->setInvalidValue(null)
                    ->setCode(Collection::MISSING_FIELD_ERROR)
                    ->addViolation();
            }
        }

        $baseFields = $this->getBaseFields($constraint);

        if (!$constraint->allowExtraFields) {
            foreach ($value as $field => $fieldValue) {
                if (!isset($baseFields[$field])) {
                    $context->buildViolation($constraint->extraFieldsMessage)
                        ->atPath('[' . $field . ']')
                        ->setParameter('{{ field }}', $this->formatValue($field))
                        ->setInvalidValue($fieldValue)
                        ->setCode(Collection::NO_SUCH_FIELD_ERROR)
                        ->addViolation();
                }
            }
        }
    }

    /**
     * @return array<string, int>
     */
    private function getBaseFields(OptionsBagConstraint $constraint): array
    {
        $baseFields = array_map(static function ($field): string {
            [$field] = explode('][', (string)$field, 2);

            return trim($field, '[]');
        }, array_keys($constraint->fields));

        return array_flip($baseFields);
    }
}
