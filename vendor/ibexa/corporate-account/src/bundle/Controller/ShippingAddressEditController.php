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
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\CorporateAccount\View\ShippingAddressEditView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;

class ShippingAddressEditController extends Controller
{
    private ShippingAddressFormFactory $formFactory;

    private ActionDispatcherInterface $actionDispatcher;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        ShippingAddressFormFactory $formFactory,
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
    public function editAction(
        Request $request,
        Company $company,
        ShippingAddress $shippingAddress
    ) {
        /** @var \Symfony\Component\Form\Form $shippingAddressEditForm */
        $shippingAddressEditForm = $this->formFactory->getEditForm($shippingAddress);

        $shippingAddressEditForm->handleRequest($request);
        if ($shippingAddressEditForm->isSubmitted() && $shippingAddressEditForm->isValid() && null !== $shippingAddressEditForm->getClickedButton()) {
            $this->actionDispatcher->dispatchFormAction(
                $shippingAddressEditForm,
                $shippingAddressEditForm->getData(),
                $shippingAddressEditForm->getClickedButton()->getName(),
                [
                    'company' => $company,
                ]
            );

            $this->notificationHandler->success(
                /** @Desc("Shipping address '%name%' updated.") */
                'shipping_address.edit.success',
                ['%name%' => $shippingAddress->getName()],
                'ibexa_corporate_account'
            );

            return $this->redirectToRoute('ibexa.corporate_account.company.details', [
                'companyId' => $company->getId(),
                '_fragment' => 'ibexa-tab-address_book',
            ]);
        }

        return new ShippingAddressEditView(
            '@ibexadesign/corporate_account/shipping_address/edit/edit_shipping_address.html.twig',
            $shippingAddress,
            $company,
            $shippingAddressEditForm
        );
    }
}
