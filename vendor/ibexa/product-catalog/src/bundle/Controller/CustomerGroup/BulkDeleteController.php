<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\CustomerGroupBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup\CustomerGroupBulkDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Delete;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private CustomerGroupServiceInterface $customerGroupService;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        CustomerGroupServiceInterface $customerGroupService,
        SubmitHandler $submitHandler,
        Repository $repository
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->customerGroupService = $customerGroupService;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
    }

    public function executeAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Delete());

        $form = $this->createBulkDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CustomerGroupBulkDeleteData $data): ?Response {
                $customerGroupNames = [];
                $this->repository->beginTransaction();

                try {
                    foreach ($data->getCustomerGroups() as $customerGroup) {
                        $this->customerGroupService->deleteCustomerGroup($customerGroup);
                        $customerGroupNames[] = $customerGroup->getName();
                    }

                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                $this->notificationHandler->success(
                    /** @Desc("{1}Customer Group '%deletedNames%' deleted.|]1,Inf[ Customer Groups '%deletedNames%' deleted.") */
                    'customer_group.delete.success',
                    [
                        '%deletedNames%' => implode("', '", $customerGroupNames),
                        '%count%' => count($customerGroupNames),
                    ],
                    'ibexa_product_catalog'
                );

                return null;
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.product_catalog.customer_group.list'));
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->createForm(CustomerGroupBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.customer_group.bulk_delete'),
        ]);
    }
}
