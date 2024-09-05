<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Type\Block;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\PageBuilder\Form\Type\Attribute\AttributeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributesType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition */
        $blockDefinition = $options['block_type'];

        foreach ($blockDefinition->getAttributes() as $attributeDefinition) {
            $attributeOptions = $attributeDefinition->getOptions();

            $builder->add(
                $builder->create($attributeDefinition->getIdentifier(), AttributeType::class, [
                    'label' => $attributeDefinition->getName(),
                    'block_definition' => $blockDefinition,
                    'block_attribute_definition' => $attributeDefinition,
                    'language_code' => $options['language_code'],
                    'help' => $attributeOptions['help']['text'] ?? null,
                    'help_attr' => $attributeOptions['help']['attr'] ?? [],
                    'help_html' => $attributeOptions['help']['html'] ?? false,
                ])
            );
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['language_code' => ''])
            ->setRequired(['block_type'])
            ->setAllowedTypes('block_type', BlockDefinition::class)
            ->setAllowedTypes('language_code', 'string');
    }
}

class_alias(AttributesType::class, 'EzSystems\EzPlatformPageBuilder\Form\Type\Block\AttributesType');
