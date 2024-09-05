<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\CustomerGroupCreateMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupCreateType;
use Ibexa\Bundle\ProductCatalog\View\CustomerGroupCreateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Create;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupCreateMapper $customerGroupCreateMapper;

    private SubmitHandler $submitHandler;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        CustomerGroupServiceInterface $customerGroupService,
        CustomerGroupCreateMapper $customerGroupCreateMapper,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->customerGroupService = $customerGroupService;
        $this->customerGroupCreateMapper = $customerGroupCreateMapper;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\CustomerGroupCreateView|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(Request $request)
    {
        $this->denyAccessUnlessGranted(new Create());

        $form = $this->createForm(CustomerGroupCreateType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.customer_group.create'),
            'method' => Request::METHOD_POST,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CustomerGroupCreateData $data): Response {
                $customerGroupCreateStruct = $this->customerGroupCreateMapper->mapToStruct($data);
                $customerGroup = $this->customerGroupService->createCustomerGroup($customerGroupCreateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Customer Group '%name%' created.") */
                    'customer_group.create.success',
                    ['%name%' => $customerGroup->getName()],
                    'ibexa_product_catalog'
                );

                return $this->redirectToRoute('ibexa.product_catalog.customer_group.view', [
                    'customerGroupId' => $customerGroup->getId(),
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new CustomerGroupCreateView('@ibexadesign/product_catalog/customer_group/create.html.twig', $form);
    }
}
