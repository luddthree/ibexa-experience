<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\CustomerGroupBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ReferenceCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerGroupBulkDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer_groups', ReferenceCollectionType::class, [
            'entry_type' => CustomerGroupReferenceType::class,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerGroupBulkDeleteData::class,
        ]);
    }
}
