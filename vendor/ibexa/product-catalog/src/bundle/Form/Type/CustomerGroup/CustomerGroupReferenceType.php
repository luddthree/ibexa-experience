<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CustomerGroupTransformer;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerGroupReferenceType extends AbstractType
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $productService)
    {
        $this->customerGroupService = $productService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CustomerGroupTransformer($this->customerGroupService));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
