<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\BlockAttribute;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\TargetedCatalogCustomerGroupMapAttributeTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\TargetedCatalogCustomerGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class TargetedCatalogCustomerGroupMapAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'matches',
            CollectionType::class,
            [
                'entry_type' => TargetedCatalogCustomerGroupType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'label' => false,
            ]
        );

        $builder->addModelTransformer(
            new TargetedCatalogCustomerGroupMapAttributeTransformer()
        );
    }

    public function getBlockPrefix(): string
    {
        return 'targeted_catalog_customer_group_map_attribute';
    }
}
