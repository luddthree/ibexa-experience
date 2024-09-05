<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class TargetedCatalogCustomerGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'customer_group',
                CustomerGroupChoiceType::class,
                [
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'catalog',
                CatalogChoiceType::class,
                [
                    'label' => false,
                    'required' => false,
                ]
            );
    }
}
