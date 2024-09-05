<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionBulkDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Delete;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private TransactionHandler $transactionHandler;

    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private SubmitHandler $submitHandler;

    public function __construct(
        TransactionHandler $transactionHandler,
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        SubmitHandler $submitHandler
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->submitHandler = $submitHandler;
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
                function (AttributeDefinitionBulkDeleteData $data): ?Response {
                    $this->handleFormSubmit($data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.list');
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->createForm(AttributeDefinitionBulkDeleteType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.attribute_definition.bulk_delete'),
            'method' => Request::METHOD_POST,
        ]);
    }

    /**
     * @throws \Exception
     */
    private function handleFormSubmit(AttributeDefinitionBulkDeleteData $data): void
    {
        $this->transactionHandler->beginTransaction();
        try {
            foreach ($data->getAttributesDefinitions() as $attributeDefinition) {
                $this->attributeDefinitionService->deleteAttributeDefinition($attributeDefinition);
            }

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }
    }
}
