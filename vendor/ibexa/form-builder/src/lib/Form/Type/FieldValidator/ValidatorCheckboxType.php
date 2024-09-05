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
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ValidatorCheckboxType extends AbstractType implements TranslationContainerInterface
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return CheckboxType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_validator_checkbox';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function ($input) {
                    return (bool)$input;
                },
                static function ($input) {
                    return (bool)$input;
                }
            )
        );
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'min_choices', 'ibexa_form_builder_field_config')
                ->setDesc('Minimum number of choices'),
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'max_choices', 'ibexa_form_builder_field_config')
                ->setDesc('Maximum number of choices'),
        ];
    }
}

class_alias(ValidatorCheckboxType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidator\ValidatorCheckboxType');
