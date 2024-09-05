<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Validator\Constraints;

use Ibexa\FormBuilder\Form\Type\FieldValidatorsConfigurationType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class IsNotBlankMessageActionValidator extends AbstractActionValidator implements TranslationContainerInterface
{
    private const ACTION_IDENTIFIER = 'message';

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsNotBlankMessageAction) {
            throw new UnexpectedTypeException($constraint, IsNotBlankMessageAction::class);
        }

        $action = $this->getActionIdentifier();

        if (
            $action === self::ACTION_IDENTIFIER
            && (false === $value || (empty($value) && '0' != $value))
        ) {
            $this->context
                ->buildViolation(FieldValidatorsConfigurationType::LABEL_PREFIX . 'is_not_blank_message_action')
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setTranslationDomain('ibexa_form_builder_field_config')
                ->setCode(NotBlank::IS_BLANK_ERROR)
                ->addViolation();
        }
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                FieldValidatorsConfigurationType::LABEL_PREFIX . 'is_not_blank_message_action',
                'ibexa_form_builder_field_config'
            )
                ->setDesc('The message to display cannot be empty.'),
        ];
    }
}
