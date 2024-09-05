<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteController extends Controller
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
    }

    public function executeAction(Request $request, AttributeDefinitionInterface $attributeDefinition): Response
    {
        $form = $this->createDeleteForm($attributeDefinition);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (AttributeDefinitionDeleteData $data): RedirectResponse {
                    return $this->handleFormSubmission($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.list');
    }

    private function createDeleteForm(AttributeDefinitionInterface $attributeDefinition): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.attribute_definition.delete',
            [
                'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
            ]
        );

        return $this->createForm(
            AttributeDefinitionDeleteType::class,
            new AttributeDefinitionDeleteData($attributeDefinition),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function handleFormSubmission(AttributeDefinitionDeleteData $data): RedirectResponse
    {
        $attributeDefinition = $data->getAttributeDefinition();

        $this->attributeDefinitionService->deleteAttributeDefinition($attributeDefinition);

        $this->notificationHandler->success(
            /** @Desc("Attribute definition '%name%' deleted.") */
            'attribute_definition.delete.success',
            ['%name%' => $attributeDefinition->getName()],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.list');
    }
}
