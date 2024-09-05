<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\AssetTagData;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssetTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $options['product'];

        $builder->add('assets', AssetReferenceListType::class, [
            'product' => $product,
        ]);

        $builder->add('attributes', AttributeCollectionType::class, [
            'required' => false,
            'product_type' => $product->getProductType(),
            'attribute_filter' => $this->getAttributeFilter(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['product']);
        $resolver->setAllowedTypes('product', ProductInterface::class);

        $resolver->setDefaults([
            'data_class' => AssetTagData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
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
