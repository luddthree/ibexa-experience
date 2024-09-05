<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\OptionsFormMapperRegistryInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeDefinitionUpdateType extends AbstractType
{
    private OptionsFormMapperRegistryInterface $optionsFormMapperRegistry;

    public function __construct(OptionsFormMapperRegistryInterface $optionsFormMapperRegistry)
    {
        $this->optionsFormMapperRegistry = $optionsFormMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isTranslation = $options['main_language_code'] !== $options['language_code'];

        $builder->add('name', TextType::class);
        $builder->add('identifier', TextType::class, ['disabled' => $isTranslation]);
        $builder->add('attributeGroup', AttributeGroupChoiceType::class, ['disabled' => $isTranslation]);
        $builder->add('position', IntegerType::class, [
            'required' => false,
            'disabled' => $isTranslation,
        ]);
        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);

        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface $type */
        $type = $options['attribute_type'];
        if ($this->optionsFormMapperRegistry->hasMapper($type->getIdentifier())) {
            $mapper = $this->optionsFormMapperRegistry->getMapper($type->getIdentifier());
            $mapper->createOptionsForm('options', $builder, [
                'language_code' => $options['language_code'],
                'type' => $type,
                'translation_mode' => $isTranslation,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeDefinitionUpdateData::class,
            'translation_domain' => 'ibexa_product_catalog',
            'main_language_code' => null,
            'attribute_type' => null,
        ])
        ->setDefined(['main_language_code', 'attribute_type'])
        ->setAllowedTypes('attribute_type', AttributeTypeInterface::class)
        ->setAllowedTypes('main_language_code', ['null', 'string'])
        ->setRequired(['attribute_type', 'language_code']);
    }
}
