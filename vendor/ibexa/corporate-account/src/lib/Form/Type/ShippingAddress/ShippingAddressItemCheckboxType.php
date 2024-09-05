<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\ShippingAddress;

use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\CorporateAccount\Form\DataTransformer\AddressBookItemTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShippingAddressItemCheckboxType extends AbstractType
{
    private ShippingAddressService $shippingAddressService;

    public function __construct(ShippingAddressService $shippingAddressService)
    {
        $this->shippingAddressService = $shippingAddressService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new AddressBookItemTransformer($this->shippingAddressService));
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
