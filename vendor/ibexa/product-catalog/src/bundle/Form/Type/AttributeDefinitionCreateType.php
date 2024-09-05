<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\OptionsFormMapperRegistryInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeDefinitionCreateType extends AbstractType
{
    private OptionsFormMapperRegistryInterface $optionsFormMapperRegistry;

    public function __construct(OptionsFormMapperRegistryInterface $optionsFormMapperRegistry)
    {
        $this->optionsFormMapperRegistry = $optionsFormMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class);
        $builder->add('identifier', TextType::class);
        $builder->add('attributeGroup', AttributeGroupChoiceType::class);
        $builder->add('position', IntegerType::class, [
            'required' => false,
        ]);
        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);

        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface $type */
        $type = $options['attribute_type'];
        if ($this->optionsFormMapperRegistry->hasMapper($type->getIdentifier())) {
            $mapper = $this->optionsFormMapperRegistry->getMapper($type->getIdentifier());
            $mapper->createOptionsForm('options', $builder, [
                'language_code' => $options['language'],
                'translation_mode' => false,
                'type' => $type,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeDefinitionCreateData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);

        $resolver->setRequired(['attribute_type', 'language']);

        $resolver->setAllowedTypes('attribute_type', AttributeTypeInterface::class);
        $resolver->setAllowedTypes('language', 'string');
    }
}
