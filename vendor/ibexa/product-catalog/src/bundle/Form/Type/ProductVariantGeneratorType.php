<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductVariantGeneratorData;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductVariantGeneratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('attributes', VariantFormCollectionType::class, [
            'attribute_filter' => $this->getAttributeFilter(),
            'product_type' => $options['product_type'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductVariantGeneratorData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);

        $resolver->setRequired('product_type');
        $resolver->setAllowedTypes('product_type', ProductTypeInterface::class);
    }

    /**
     * @return callable(AttributeDefinitionAssignmentInterface): bool
     */
    private function getAttributeFilter(): callable
    {
        return static function (AttributeDefinitionAssignmentInterface $attribute): bool {
            return $attribute->isDiscriminator();
        };
    }
}
