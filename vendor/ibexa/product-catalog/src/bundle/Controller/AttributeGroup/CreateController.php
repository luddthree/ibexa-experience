<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeGroup;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupCreateType;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupCreateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Create;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends Controller
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        LocalAttributeGroupServiceInterface $attributeGroupService,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->attributeGroupService = $attributeGroupService;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\Exception\BadRequestException
     *
     * @return \Ibexa\Bundle\ProductCatalog\View\AttributeGroupCreateView|\Symfony\Component\HttpFoundation\Response
     */
    public function executeAction(Request $request)
    {
        $this->denyAccessUnlessGranted(new Create());

        $form = $this->createForm(AttributeGroupCreateType::class, new AttributeGroupCreateData());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (AttributeGroupCreateData $data): Response {
                    return $this->handleFormSubmit($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new AttributeGroupCreateView('@ibexadesign/product_catalog/attribute_group/create.html.twig', $form);
    }

    private function handleFormSubmit(AttributeGroupCreateData $data): Response
    {
        $createStruct = $this->attributeGroupService->newAttributeGroupCreateStruct(
            $data->getIdentifier(),
        );
        $createStruct->setNames([
            $data->getLanguage()->languageCode => $data->getName(),
        ]);
        $createStruct->setPosition($data->getPosition());

        $attributeGroup = $this->attributeGroupService->createAttributeGroup($createStruct);

        $this->notificationHandler->success(
            /** @Desc("Attribute Group '%name%' created.") */
            'attribute_group.create.success',
            ['%name%' => $attributeGroup->getName()],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.attribute_group.view', [
            'attributeGroupIdentifier' => $attributeGroup->getIdentifier(),
        ]);
    }
}
