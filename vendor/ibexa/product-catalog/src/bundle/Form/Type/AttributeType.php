<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\ValueFormMapperRegistryInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeType extends AbstractType
{
    private ValueFormMapperRegistryInterface $valueMapperRegistry;

    public function __construct(ValueFormMapperRegistryInterface $valueMapperRegistry)
    {
        $this->valueMapperRegistry = $valueMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface $assignment */
        $assignment = $options['attribute_definition'];
        $definition = $assignment->getAttributeDefinition();

        $typeIdentifier = $definition->getType()->getIdentifier();
        if ($this->valueMapperRegistry->hasMapper($typeIdentifier)) {
            $mapper = $this->valueMapperRegistry->getMapper($typeIdentifier);
            $mapper->createValueForm('value', $builder, $assignment, [
                'translation_mode' => $options['translation_mode'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeData::class,
            'empty_data' => static function (Options $options): AttributeData {
                return new AttributeData($options['attribute_definition']->getAttributeDefinition());
            },
        ]);
        $resolver->setRequired(['attribute_definition']);
        $resolver->setAllowedTypes('attribute_definition', AttributeDefinitionAssignmentInterface::class);

        $resolver->setDefault('translation_mode', false);
        $resolver->setAllowedTypes('translation_mode', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'product_catalog_attribute';
    }
}
