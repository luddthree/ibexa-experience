<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\Commerce;

use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Contact;
use Ibexa\Bundle\Commerce\Eshop\Event\CustomerProfileData\EzErpCustomerProfileDataEvent;
use Ibexa\Bundle\Commerce\Eshop\Services\CustomerProfileData\EzErpCustomerProfileDataService;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\CorporateAccount\Commerce\Address\AddressMapper;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PopulateCommerceUserWithCompanyAddresses implements EventSubscriberInterface
{
    private CorporateAccountConfiguration $accountConfiguration;

    private SearchService $searchService;

    private MemberResolver $memberResolver;

    private AddressMapper $addressMapper;

    public function __construct(
        CorporateAccountConfiguration $accountConfiguration,
        SearchService $searchService,
        MemberResolver $memberResolver,
        AddressMapper $addressMapper
    ) {
        $this->accountConfiguration = $accountConfiguration;
        $this->searchService = $searchService;
        $this->memberResolver = $memberResolver;
        $this->addressMapper = $addressMapper;
    }

    public static function getSubscribedEvents()
    {
        return [EzErpCustomerProfileDataService::EVENT_PRE_GET_CUSTOMER => 'setAddresses'];
    }

    public function setAddresses(EzErpCustomerProfileDataEvent $event): void
    {
        try {
            $member = $this->memberResolver->getCurrentMember();
        } catch (InvalidArgumentException $exception) {
            return;
        }
        $company = $member->getCompany();
        $mainLocation = $company->getContent()->getVersionInfo()->getContentInfo()->getMainLocation();
        if ($mainLocation == null) {
            return;
        }
        /** @var \Ibexa\FieldTypeAddress\FieldType\Value $billingAddress */
        $billingAddress = $company->getContent()->getFieldValue('billing_address');

        $shippingAddresses = $this->searchService->findContent(
            new Query([
                'query' => new Criterion\LogicalAnd([
                    new Criterion\Subtree(
                        $mainLocation->pathString
                    ),
                    new Criterion\ContentTypeIdentifier($this->accountConfiguration->getShippingAddressContentTypeIdentifier()),
                ]),
            ])
        )->searchHits;

        $profile = $event->getCustomerProfileData();

        $buyerParty = $this->addressMapper->mapAddressFieldTypeToCommerce(
            (string)$company->getId(),
            $profile->buyerParties[0],
            $billingAddress,
            new Contact()
        );

        $deliveryParties = array_map(
            fn (SearchHit $searchHit) => $this->addressMapper->mapAddressFieldTypeToCommerce(
                (string)$searchHit->valueObject->id,
                $profile->deliveryParties[0],
                $searchHit->valueObject->getFieldValue('address'),
                $this->addressMapper->mapContactToCommerce($searchHit->valueObject)
            ),
            $shippingAddresses
        );

        $defaultShippingAddressId = $company->getDefaultAddressId();

        if ($defaultShippingAddressId !== null) {
            $deliveryParties = $this->setDefaultDeliveryParty($deliveryParties, (string) $defaultShippingAddressId);
        }

        $profile->removeAllParties();

        $profile->addBuyerParty($buyerParty);
        $profile->addInvoiceParty($buyerParty);
        foreach ($deliveryParties as $deliveryParty) {
            $profile->addDeliveryParty($deliveryParty);
        }

        $event->setCustomerProfileData($profile);
    }

    /**
     * @param \Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Party[] $deliveryParties
     *
     * @return \Ibexa\Bundle\Commerce\Eshop\Entities\Messages\Document\Party[]
     */
    private function setDefaultDeliveryParty(array $deliveryParties, string $defaultShippingAddressId): array
    {
        $defaultDeliveryParty = $deliveryParties[0];
        foreach ($deliveryParties as $deliveryParty) {
            foreach ($deliveryParty->PartyIdentification as $identification) {
                if ($identification->ID->value === $defaultShippingAddressId) {
                    $defaultDeliveryParty = $deliveryParty;
                    break 2;
                }
            }
        }

        return array_merge([0 => $defaultDeliveryParty] + $deliveryParties);
    }
}
