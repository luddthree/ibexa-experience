<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductsPreviewFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductsPreviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('search', ProductSearchType::class);
        $builder->add('filters', CatalogFilterCollectionType::class, [
            'filter_definitions' => $options['filter_definitions'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filter_definitions' => [],
            'translation_domain' => 'ibexa_product_catalog',
            'data_class' => ProductsPreviewFormData::class,
        ]);

        $resolver->setAllowedTypes('filter_definitions', 'iterable');
    }

    public function getBlockPrefix(): string
    {
        return 'products_preview';
    }
}
