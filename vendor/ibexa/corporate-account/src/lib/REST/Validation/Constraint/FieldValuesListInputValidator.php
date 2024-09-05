<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Constraint;

use Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @internal
 *
 * @phpstan-import-type RESTContentFieldsInputArray from \Ibexa\CorporateAccount\REST\Input\Parser\BaseContentParser
 */
final class FieldValuesListInputValidator extends ConstraintValidator
{
    /**
     * @phpstan-param RESTContentFieldsInputArray $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof FieldValuesListInput) {
            throw new UnexpectedTypeException($constraint, FieldValuesListInput::class);
        }

        $context = $this->context;
        foreach ($value as $idx => $fieldValueInputData) {
            $context->getValidator()
                ->inContext($context)
                ->atPath("[$idx]")
                ->validate(
                    $fieldValueInputData,
                    $this->buildFieldValueInputDataConstraint((string)$idx, $constraint)
                );
        }
    }

    private function buildFieldValueInputDataConstraint(
        string $idx,
        FieldValuesListInput $constraint
    ): Constraint {
        $unknownTypeErrorMessage = sprintf(
            'Field value structure expects "%s" and "%s" keys for Field at index %s',
            BaseContentParser::FIELD_DEFINITION_IDENTIFIER_KEY,
            BaseContentParser::FIELD_VALUE_KEY,
            $idx
        );

        return new Sequentially(
            [
                new Assert\NotNull(null, $unknownTypeErrorMessage),
                new Assert\Type('array', $unknownTypeErrorMessage),
                new Assert\Collection(
                    [
                        'fields' => [
                            BaseContentParser::FIELD_DEFINITION_IDENTIFIER_KEY => new Assert\Sequentially(
                                [
                                    new Assert\NotBlank(
                                        null,
                                        sprintf(
                                            'Missing "%s" element in Field value structure',
                                            BaseContentParser::FIELD_DEFINITION_IDENTIFIER_KEY
                                        )
                                    ),
                                    new FieldDefinitionIdentifier(
                                        $constraint->getContentType()
                                    ),
                                ]
                            ),
                            BaseContentParser::FIELD_VALUE_KEY => new Assert\Required(),
                        ],
                        'extraFieldsMessage' => sprintf(
                            'The field {{ field }} was not expected in Field value structure at index %s',
                            $idx
                        ),
                        'missingFieldsMessage' => sprintf(
                            'Missing {{ field }} element in Field value structure at index %s',
                            $idx
                        ),
                    ],
                ),
            ]
        );
    }
}
