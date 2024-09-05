<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Type;

use Ibexa\AdminUi\Form\Type\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TillDateTimeType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Never' => 'never',
                    'Set date' => 'custom_date',
                ],
                'expanded' => true,
                'multiple' => false,
                'translation_domain' => 'ibexa_page_builder_forms',
                'choice_translation_domain' => 'ibexa_page_builder_forms',
            ])
            ->add('custom_date', DateTimePickerType::class)
            ->addModelTransformer(
                new CallbackTransformer(
                    static function ($value) {
                        return [
                            'type' => $value ? 'custom_date' : 'never',
                            'custom_date' => $value,
                        ];
                    },
                    static function ($value) {
                        return $value['type'] === 'custom_date' && $value['custom_date'] !== null
                            ? $value['custom_date']
                            : null;
                    }
                )
            );
    }
}

class_alias(TillDateTimeType::class, 'EzSystems\EzPlatformPageBuilder\Form\Type\TillDateTimeType');
