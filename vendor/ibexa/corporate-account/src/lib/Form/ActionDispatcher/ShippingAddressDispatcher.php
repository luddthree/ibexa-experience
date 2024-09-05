<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\ActionDispatcher;

use Ibexa\ContentForms\Form\ActionDispatcher\AbstractActionDispatcher;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingAddressDispatcher extends AbstractActionDispatcher
{
    protected function getActionEventBaseName(): string
    {
        return DispatcherEvents::SHIPPING_ADDRESS_EDIT;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('company');
        $resolver->setRequired('company');
    }
}
