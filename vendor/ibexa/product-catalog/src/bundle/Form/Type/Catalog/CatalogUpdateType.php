<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogUpdateType extends AbstractType
{
    public function getParent(): string
    {
        return CatalogType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogUpdateData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);

        $resolver->setRequired(['catalog']);
        $resolver->setAllowedTypes('catalog', CatalogInterface::class);
    }
}
