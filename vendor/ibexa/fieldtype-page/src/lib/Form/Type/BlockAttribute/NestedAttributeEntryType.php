<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NestedAttributeEntryType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['allow_delete'] = $options['allow_delete'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $attributes = $options['attributes'];

        foreach ($attributes as $identifier => $nestedAttribute) {
            $name = $nestedAttribute['name'];
            $constraints = $nestedAttribute['validators'] ?? $options['block_attribute_definition']->getConstraints();

            $blockAttributeDefinition = new BlockAttributeDefinition();
            $blockAttributeDefinition->setIdentifier($identifier);
            $blockAttributeDefinition->setName($name);
            $blockAttributeDefinition->setType($nestedAttribute['type']);
            $blockAttributeDefinition->setOptions($nestedAttribute['options'] ?? []);
            $blockAttributeDefinition->setConstraints($constraints);
            $blockAttributeDefinition->setValue($nestedAttribute['value'] ?? null);
            $blockAttributeDefinition->setCategory($nestedAttribute['category'] ?? Configuration::DEFAULT_CATEGORY);

            $builder->add($identifier, NestedAttributeValueType::class, [
                'label' => $name,
                'attribute' => $nestedAttribute,
                'block_definition' => $options['block_definition'],
                'block_attribute_definition' => $blockAttributeDefinition,
                'language_code' => $options['language_code'],
                'help' => $nestedAttribute['options']['help']['text'] ?? null,
                'help_attr' => $nestedAttribute['options']['help']['attr'] ?? [],
                'help_html' => $nestedAttribute['options']['help']['html'] ?? false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(
                [
                    'allow_delete',
                    'attributes',
                    'block_definition',
                    'block_attribute_definition',
                ]
            )
            ->setDefaults(['language_code' => ''])
            ->setAllowedTypes('allow_delete', 'bool')
            ->setAllowedTypes('attributes', 'array')
            ->setAllowedTypes('block_definition', BlockDefinition::class)
            ->setAllowedTypes('block_attribute_definition', BlockAttributeDefinition::class);
    }
}
