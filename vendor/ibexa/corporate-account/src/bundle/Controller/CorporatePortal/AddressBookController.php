<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\ShippingAddressListAdapter;
use Ibexa\CorporateAccount\View\CorporatePortal\AddressBookView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final class AddressBookController extends Controller
{
    private ConfigResolverInterface $configResolver;

    private ShippingAddressFormFactory $formFactory;

    private MemberResolver $memberResolver;

    private ShippingAddressService $shippingAddressService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccountConfiguration,
        MemberResolver $memberResolver,
        ConfigResolverInterface $configResolver,
        ShippingAddressFormFactory $formFactory,
        ShippingAddressService $shippingAddressService
    ) {
        parent::__construct($corporateAccountConfiguration);
        $this->memberResolver = $memberResolver;
        $this->configResolver = $configResolver;
        $this->formFactory = $formFactory;
        $this->shippingAddressService = $shippingAddressService;
    }

    public function showAction(Request $request): BaseView
    {
        $member = $this->memberResolver->getCurrentMember();
        $company = $member->getCompany();

        $pagerfanta = new Pagerfanta(
            new ShippingAddressListAdapter(
                $this->shippingAddressService,
                $company
            )
        );
        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.addresses_limit'));
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        $formDefaultShippingAddressUpdate =
            $this->formFactory->getSetDefaultShippingAddressForm(
                $company,
                $this->shippingAddressService->getCompanyDefaultShippingAddress($company)
            );

        $formAddressBookItemDelete = $this->formFactory->getDeleteShippingAddressForm(
            $pagerfanta->getCurrentPageResults()
        );

        return new AddressBookView(
            '@ibexadesign/customer_portal/address_book/address_book.html.twig',
            $company,
            $pagerfanta,
            $formDefaultShippingAddressUpdate,
            $formAddressBookItemDelete
        );
    }
}
