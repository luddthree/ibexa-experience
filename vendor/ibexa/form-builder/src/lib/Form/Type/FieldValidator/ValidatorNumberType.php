<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldValidator;

use Ibexa\FormBuilder\Form\Type\FieldValidatorsConfigurationType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ValidatorNumberType extends AbstractType implements TranslationContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return NumberType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldPrefix(): string
    {
        return 'field_configuration_validator_number';
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'min_value', 'ibexa_form_builder_field_config')
                ->setDesc('Minimum value'),
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'max_value', 'ibexa_form_builder_field_config')
                ->setDesc('Maximum value'),
        ];
    }
}

class_alias(ValidatorNumberType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidator\ValidatorNumberType');
