<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event\Listener;

use Ibexa\Contracts\CorporateAccount\Event\Application\MapCompanyCreateStructEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\MapShippingAddressUpdateStructEvent;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ApplicationMapperSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            MapShippingAddressUpdateStructEvent::class => 'mapShippingAddressUpdateStruct',
            MapCompanyCreateStructEvent::class => 'mapCompanyCreateStruct',
        ];
    }

    public function mapShippingAddressUpdateStruct(MapShippingAddressUpdateStructEvent $event): void
    {
        $shippingAddressUpdateStruct = $event->getShippingAddressUpdateStruct();

        $content = $event->getApplication()->getContent();

        $shippingAddressUpdateStruct->setField('email', $content->getFieldValue('user'));
        $shippingAddressUpdateStruct->setField('phone', $content->getFieldValue('phone_number'));

        $event->setShippingAddressUpdateStruct($shippingAddressUpdateStruct);
    }

    public function mapCompanyCreateStruct(MapCompanyCreateStructEvent $event): void
    {
        $companyCreateStruct = $event->getCompanyCreateStruct();

        $content = $event->getApplication()->getContent();
        $customerGroup = $event->getCustomerGroup();
        $salesRepId = $event->getSalesRepId();

        $companyCreateStruct->setField('active', new CheckboxValue(true));
        $companyCreateStruct->setField('name', $content->getFieldValue('name'));
        $companyCreateStruct->setField('vat', $content->getFieldValue('vat'));
        $companyCreateStruct->setField('billing_address', $content->getFieldValue('address'));
        $companyCreateStruct->setField('website', $content->getFieldValue('website'));
        $companyCreateStruct->setField('customer_group', new CustomerGroupValue($customerGroup->getId()));
        $companyCreateStruct->setField('sales_rep', new RelationValue($salesRepId));

        $event->setCompanyCreateStruct($companyCreateStruct);
    }
}
