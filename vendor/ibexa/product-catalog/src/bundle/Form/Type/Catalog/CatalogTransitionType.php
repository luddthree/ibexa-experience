<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogTransitionData;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogTransitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('transition', CatalogTransitionChoiceType::class, [
            'catalog' => $options['catalog'],
        ]);
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogTransitionData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);

        $resolver->setRequired(['catalog']);
        $resolver->setAllowedTypes('catalog', CatalogInterface::class);
    }
}
