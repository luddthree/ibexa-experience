<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\View\CompanyEditView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyEditController extends Controller
{
    private CompanyFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        CompanyFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editAction(Request $request, Company $company)
    {
        /** @var \Symfony\Component\Form\Form $companyEditForm */
        $companyEditForm = $this->formFactory->getEditForm($company);

        $response = $this->handleForm($request, $company, $companyEditForm);

        if (null !== $response) {
            return $response;
        }

        return new CompanyEditView(
            '@ibexadesign/corporate_account/company/edit/edit_company.html.twig',
            $company,
            $companyEditForm
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editBasicInformationAction(Request $request, Company $company)
    {
        /** @var \Symfony\Component\Form\Form $companyBasicInformationEditForm */
        $companyBasicInformationEditForm = $this->formFactory->getBasicInformationEditForm($company);

        $response = $this->handleForm($request, $company, $companyBasicInformationEditForm, 'ibexa-tab-company_profile');

        if (null !== $response) {
            return $response;
        }

        return new CompanyEditView(
            '@ibexadesign/corporate_account/company/edit/edit_company_basic_information.html.twig',
            $company,
            $companyBasicInformationEditForm
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editBillingAddressAction(Request $request, Company $company)
    {
        /** @var \Symfony\Component\Form\Form $companyBillingAddressEditForm */
        $companyBillingAddressEditForm = $this->formFactory->getBillingAddressEditForm($company);

        $response = $this->handleForm($request, $company, $companyBillingAddressEditForm, 'ibexa-tab-address_book');

        if (null !== $response) {
            return $response;
        }

        return new CompanyEditView(
            '@ibexadesign/corporate_account/company/edit/edit_company_billing_address.html.twig',
            $company,
            $companyBillingAddressEditForm
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function editContactAction(Request $request, Company $company)
    {
        /** @var \Symfony\Component\Form\Form $companyContactEditForm */
        $companyContactEditForm = $this->formFactory->getContactEditForm($company);

        $response = $this->handleForm($request, $company, $companyContactEditForm, 'ibexa-tab-company_profile');

        if (null !== $response) {
            return $response;
        }

        return new CompanyEditView(
            '@ibexadesign/corporate_account/company/edit/edit_company_contact.html.twig',
            $company,
            $companyContactEditForm
        );
    }

    private function handleForm(
        Request $request,
        Company $company,
        Form $editForm,
        string $tab = null
    ): ?Response {
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && null !== $editForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $editForm,
                $editForm->getData(),
                $editForm->getClickedButton()->getName()
            );

            $this->notificationHandler->success(
                /** @Desc("Company '%name%' updated.") */
                'company.edit.success',
                ['%name%' => $company->getName()],
                'ibexa_corporate_account'
            );

            return $this->redirectToRoute('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => $tab,
            ]);
        }

        return null;
    }
}
