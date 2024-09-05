<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $filter = $options['attribute_filter'];
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface $type */
        $type = $options['product_type'];

        foreach ($type->getAttributesDefinitions() as $assignment) {
            $definition = $assignment->getAttributeDefinition();

            if (is_callable($filter) && !($filter)($assignment)) {
                continue;
            }

            $builder->add(
                $definition->getIdentifier(),
                AttributeType::class,
                [
                    'attribute_definition' => $assignment,
                    'translation_mode' => $options['translation_mode'],
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['product_type']);
        $resolver->setAllowedTypes('product_type', ProductTypeInterface::class);

        $resolver->setDefault('attribute_filter', null);
        $resolver->setAllowedTypes('attribute_filter', ['callable', 'null']);

        $resolver->setDefault('translation_mode', false);
        $resolver->setAllowedTypes('translation_mode', 'bool');
    }
}
