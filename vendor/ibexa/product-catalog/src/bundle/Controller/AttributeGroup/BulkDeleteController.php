<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeGroup;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupBulkDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Delete;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private Repository $repository;

    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        Repository $repository,
        LocalAttributeGroupServiceInterface $attributeGroupService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->repository = $repository;
        $this->attributeGroupService = $attributeGroupService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\Exception\BadRequestException
     */
    public function executeAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Delete());

        $form = $this->createBulkDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (AttributeGroupBulkDeleteData $data): ?Response {
                    $this->handleFormSubmit($data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_group.list');
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(AttributeGroupBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.attribute_group.bulk_delete'),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function handleFormSubmit(AttributeGroupBulkDeleteData $data): void
    {
        $attributeGroupNames = [];
        $this->repository->beginTransaction();

        try {
            foreach ($data->getAttributeGroups() as $attributeGroup) {
                $this->attributeGroupService->deleteAttributeGroup($attributeGroup);
                $attributeGroupNames[] = $attributeGroup->getName();
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("{1}Attribute group '%deletedNames%' deleted.|]1,Inf[ Attribute groups '%deletedNames%' deleted.") */
            'attribute_group.delete.success',
            [
                '%deletedNames%' => implode("', '", $attributeGroupNames),
                '%count%' => count($attributeGroupNames),
            ],
            'ibexa_product_catalog'
        );
    }
}
