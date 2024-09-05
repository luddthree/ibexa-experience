<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\View\CompanyEditView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BillingAddressController extends Controller
{
    private CompanyFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private UrlGeneratorInterface $urlGenerator;

    private MemberResolver $memberResolver;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        MemberResolver $memberResolver,
        CompanyFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($corporateAccount);
        $this->memberResolver = $memberResolver;
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(Request $request)
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();
        $companyBillingAddressEditForm = $this->formFactory->getBillingAddressEditForm($company);

        $companyBillingAddressEditForm->handleRequest($request);

        if (
            $companyBillingAddressEditForm->isSubmitted()
            && $companyBillingAddressEditForm->isValid()
            && null !== $companyBillingAddressEditForm->getClickedButton()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $companyBillingAddressEditForm,
                $companyBillingAddressEditForm->getData(),
                $companyBillingAddressEditForm->getClickedButton()->getName()
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.contact')
            );
        }

        return new CompanyEditView(
            '@ibexadesign/customer_portal/edit/billing_address.html.twig',
            $company,
            $companyBillingAddressEditForm
        );
    }
}
