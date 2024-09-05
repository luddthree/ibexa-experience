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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ValidatorStringType extends AbstractType implements TranslationContainerInterface
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_validator_string';
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
                    return (string)$input;
                },
                static function ($input) {
                    return (string)$input;
                }
            )
        );
    }

    /**
     * Returns an array of messages.
     *
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'max_length', 'ibexa_form_builder_field_config')
                ->setDesc('Maximum length'),
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'min_length', 'ibexa_form_builder_field_config')
                ->setDesc('Minimum length'),
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'exact_length', 'ibexa_form_builder_field_config')
                ->setDesc('Exact length'),
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'required', 'ibexa_form_builder_field_config')
                ->setDesc('Required'),
        ];
    }
}

class_alias(ValidatorStringType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidator\ValidatorStringType');
