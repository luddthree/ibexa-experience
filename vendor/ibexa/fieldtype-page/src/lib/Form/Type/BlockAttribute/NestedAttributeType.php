<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\FieldTypePage\Form\DataTransformer\NestedAttributeDataTransformer;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NestedAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entryOptions = $options['entry_options'];
        $builder->add('attributes', NestedAttributeCollectionType::class, [
            'allow_add' => $options['allow_add'],
            'allow_delete' => $options['allow_delete'],
            'prototype' => true,
            'entry_options' => $entryOptions,
            'translation_domain' => 'ibexa_page_fieldtype',
            'label' => /** @Desc("Setup for field groups %attributeName%") */'block.nested_attribute.setup_for_field_groups',
            'label_translation_parameters' => [
                '%attributeName%' => $entryOptions['block_attribute_definition']->getName(),
            ],
        ]);

        $builder->addModelTransformer(
            new NestedAttributeDataTransformer($options['entry_options']['attributes'])
        );
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_nested_attribute';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(
                [
                    'allow_add',
                    'allow_delete',
                    'entry_options',
                ]
            )
            ->setDefaults(['language_code' => ''])
            ->setAllowedTypes('allow_add', 'bool')
            ->setAllowedTypes('allow_delete', 'bool')
            ->setAllowedTypes('entry_options', 'array')
            ->setAllowedTypes('language_code', 'string');
    }
}
