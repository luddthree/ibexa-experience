<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteController extends Controller
{
    private CustomerGroupServiceInterface $customerGroupService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    public function __construct(
        CustomerGroupServiceInterface $customerGroupService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler
    ) {
        $this->customerGroupService = $customerGroupService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
    }

    public function executeAction(Request $request, CustomerGroupInterface $customerGroup): Response
    {
        $form = $this->createDeleteForm($customerGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (CustomerGroupDeleteData $data): RedirectResponse {
                    return $this->handleFormSubmission($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.customer_group.list');
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function handleFormSubmission(CustomerGroupDeleteData $data): RedirectResponse
    {
        $customerGroup = $data->getCustomerGroup();

        $this->customerGroupService->deleteCustomerGroup($customerGroup);

        $this->notificationHandler->success(
            /** @Desc("{1}Customer Group '%deletedNames%' deleted.|]1,Inf[ Customer Groups '%deletedNames%' deleted.") */
            'customer_group.delete.success',
            [
                '%deletedNames%' => $customerGroup->getName(),
                '%count%' => 1,
            ],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.customer_group.list');
    }

    private function createDeleteForm(CustomerGroupInterface $customerGroup): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.customer_group.delete',
            [
                'customerGroupId' => $customerGroup->getId(),
            ]
        );

        return $this->createForm(
            CustomerGroupDeleteType::class,
            new CustomerGroupDeleteData($customerGroup),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
