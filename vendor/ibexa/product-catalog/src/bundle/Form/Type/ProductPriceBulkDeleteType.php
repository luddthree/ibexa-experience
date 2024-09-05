<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Price\ProductPriceReferenceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductPriceBulkDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('prices', CollectionType::class, [
            'entry_type' => ProductPriceReferenceType::class,
            'required' => false,
            'allow_add' => true,
            'label' => false,
            'entry_options' => [
                'label' => false,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPriceDeleteData::class,
        ]);
    }
}
