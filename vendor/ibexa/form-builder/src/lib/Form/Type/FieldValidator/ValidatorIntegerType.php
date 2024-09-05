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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ValidatorIntegerType extends AbstractType implements TranslationContainerInterface
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return IntegerType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_validator_integer';
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
                    return (int)$input;
                },
                static function ($input) {
                    return (int)$input;
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
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'upload_size', 'ibexa_form_builder_field_config')
                ->setDesc('Maximum allowed file size (MB)'),
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'extensions', 'ibexa_form_builder_field_config')
                ->setDesc('Allowed file extensions'),
        ];
    }
}

class_alias(ValidatorIntegerType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidator\ValidatorIntegerType');
