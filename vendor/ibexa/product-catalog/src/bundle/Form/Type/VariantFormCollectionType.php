<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\VariantFormMapperRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VariantFormCollectionType extends AbstractType
{
    private VariantFormMapperRegistryInterface $formMapperRegistry;

    public function __construct(VariantFormMapperRegistryInterface $formMapperRegistry)
    {
        $this->formMapperRegistry = $formMapperRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $filter = $options['attribute_filter'];
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface $productType */
        $productType = $options['product_type'];

        foreach ($productType->getAttributesDefinitions() as $assignment) {
            if (!$assignment->isDiscriminator()) {
                continue;
            }

            if (is_callable($filter) && !($filter)($assignment)) {
                continue;
            }

            $definition = $assignment->getAttributeDefinition();

            $type = $definition->getType()->getIdentifier();
            if ($this->formMapperRegistry->hasMapper($type)) {
                $mapper = $this->formMapperRegistry->getMapper($type);
                $mapper->createForm($definition->getIdentifier(), $builder, $assignment);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['product_type']);
        $resolver->setAllowedTypes('product_type', ProductTypeInterface::class);

        $resolver->setDefault('attribute_filter', null);
        $resolver->setAllowedTypes('attribute_filter', ['callable', 'null']);
    }
}
