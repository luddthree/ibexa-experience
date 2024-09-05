<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingAddressDeleteController extends Controller
{
    private ShippingAddressFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    public function __construct(
        ShippingAddressFormFactory $formFactory,
        ActionDispatcherInterface $actionDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->actionDispatcher = $actionDispatcher;
    }

    public function deleteAction(
        Company $company,
        Request $request
    ): Response {
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

        return $this->redirectToRoute('ibexa.corporate_account.company.details', [
            'companyId' => $company->getId(),
            '_fragment' => 'ibexa-tab-address_book',
        ]);
    }
}
