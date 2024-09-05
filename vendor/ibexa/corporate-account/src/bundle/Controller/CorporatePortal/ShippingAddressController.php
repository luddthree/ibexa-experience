<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\CorporateAccount\View\ShippingAddressCreateView;
use Ibexa\CorporateAccount\View\ShippingAddressEditView;
use LogicException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ShippingAddressController extends Controller
{
    private ShippingAddressFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private UrlGeneratorInterface $urlGenerator;

    private LanguageService $languageService;

    private ConfigResolverInterface $configResolver;

    private ContentService $contentService;

    private MemberResolver $memberResolver;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        MemberResolver $memberResolver,
        ShippingAddressFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        UrlGeneratorInterface $urlGenerator,
        LanguageService $languageService,
        ConfigResolverInterface $configResolver,
        ContentService $contentService
    ) {
        parent::__construct($corporateAccount);
        $this->memberResolver = $memberResolver;
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->urlGenerator = $urlGenerator;
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function createAction(
        Request $request
    ) {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        $languages = $this->configResolver->getParameter('languages');
        $language = $this->languageService->loadLanguage(reset($languages));

        $parentLocation = $this->contentService->loadContentInfo($company->getAddressBookId())->getMainLocation();

        if ($parentLocation === null) {
            throw new LogicException('No Location found for address book');
        }

        /** @var \Symfony\Component\Form\Form $createForm */
        $createForm = $this->formFactory->getCreateForm(
            $parentLocation,
            $language->languageCode
        );

        $createForm->handleRequest($request);
        if (
            $createForm->isSubmitted()
            && $createForm->isValid()
            && null !== $createForm->getClickedButton()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $createForm,
                $createForm->getData(),
                $createForm->getClickedButton()->getName(),
                ['company' => $company]
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.address_book')
            );
        }

        return new ShippingAddressCreateView(
            '@ibexadesign/corporate_account/shipping_address/create/create_shipping_address.html.twig',
            $createForm,
            $parentLocation,
            $language,
            $company
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(
        Request $request,
        ShippingAddress $shippingAddress
    ) {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        $shippingAddressEditForm = $this->formFactory->getEditForm($shippingAddress);

        $shippingAddressEditForm->handleRequest($request);

        if ($shippingAddressEditForm->isSubmitted()
            && $shippingAddressEditForm->isValid()
            && null !== $shippingAddressEditForm->getClickedButton()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $shippingAddressEditForm,
                $shippingAddressEditForm->getData(),
                $shippingAddressEditForm->getClickedButton()->getName(),
                ['company' => $company]
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.address_book')
            );
        }

        return new ShippingAddressEditView(
            '@ibexadesign/corporate_account/shipping_address/edit/edit_shipping_address.html.twig',
            $shippingAddress,
            $company,
            $shippingAddressEditForm
        );
    }

    public function setAsDefaultAction(
        Request $request
    ): Response {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        $form = $this->formFactory->getSetDefaultShippingAddressForm(
            $company
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                'set_as_default',
                ['company' => $company]
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.address_book')
        );
    }

    public function deleteAction(
        Request $request
    ): Response {
        $company = $this->memberResolver->getCurrentMember()->getCompany();

        $form = $this->formFactory->getDeleteShippingAddressForm();
        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $this->actionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                'delete',
                ['company' => $company]
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('ibexa.corporate_account.customer_portal.address_book')
        );
    }
}
