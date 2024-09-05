<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldValidator;

use Ibexa\FormBuilder\Form\Type\FieldValidator\Regex\PatternSelectType;
use Ibexa\FormBuilder\Form\Type\FieldValidatorsConfigurationType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ValidatorRegexType extends AbstractType implements TranslationContainerInterface
{
    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_validator_regex';
    }

    /**
     * Returns an array of messages.
     *
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create(FieldValidatorsConfigurationType::LABEL_PREFIX . 'regex', 'ibexa_form_builder_field_config')
                ->setDesc('Regular expression pattern'),
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('select', PatternSelectType::class);
        $builder->add('pattern', TextType::class, [
            'empty_data' => PatternSelectType::PATTERN_NONE,
        ]);

        $transformer = new CallbackTransformer(
            static function ($input) {
                if (empty($input)) {
                    return [];
                }

                return json_decode($input, true, 2, JSON_OBJECT_AS_ARRAY);
            },
            static function ($input) {
                if (PatternSelectType::PATTERN_NONE === $input['pattern']) {
                    return null;
                }

                return json_encode($input);
            }
        );

        $builder->addModelTransformer($transformer);
    }
}

class_alias(ValidatorRegexType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidator\ValidatorRegexType');
