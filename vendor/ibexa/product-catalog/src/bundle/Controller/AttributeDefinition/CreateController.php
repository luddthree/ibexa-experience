<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\AttributeDefinitionCreateMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionCreateType;
use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionCreateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends Controller
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private AttributeDefinitionCreateMapper $attributeDefinitionCreateMapper;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        AttributeDefinitionCreateMapper $attributeDefinitionCreateMapper
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->attributeDefinitionCreateMapper = $attributeDefinitionCreateMapper;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionCreateView|\Symfony\Component\HttpFoundation\Response
     */
    public function executeAction(
        Request $request,
        AttributeTypeInterface $attributeType,
        Language $language,
        ?AttributeGroupInterface $attributeGroup
    ) {
        $form = $this->createCreateForm($attributeType, $language, $attributeGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handle = $this->submitHandler->handle(
                $form,
                fn (AttributeDefinitionCreateData $data): Response => $this->handleFormSubmission(
                    $attributeType,
                    $language,
                    $data,
                    $attributeGroup !== null
                )
            );

            if ($handle instanceof Response) {
                return $handle;
            }
        }

        return new AttributeDefinitionCreateView(
            '@ibexadesign/product_catalog/attribute_definition/create.html.twig',
            $form,
            $attributeType,
            $language
        );
    }

    private function createCreateForm(
        AttributeTypeInterface $attributeType,
        Language $sourceLanguage,
        ?AttributeGroupInterface $attributeGroup = null
    ): FormInterface {
        $attributeDefinitionCreateData = new AttributeDefinitionCreateData();
        $attributeDefinitionCreateData->setAttributeGroup($attributeGroup);
        $attributeDefinitionCreateData->setLanguage($sourceLanguage);

        return $this->createForm(
            AttributeDefinitionCreateType::class,
            $attributeDefinitionCreateData,
            [
                'attribute_type' => $attributeType,
                'method' => Request::METHOD_POST,
                'language' => $sourceLanguage->languageCode,
            ]
        );
    }

    private function handleFormSubmission(
        AttributeTypeInterface $attributeType,
        Language $sourceLanguage,
        AttributeDefinitionCreateData $data,
        bool $redirectToAttributeGroup
    ): RedirectResponse {
        $attributeDefinition = $this->attributeDefinitionService->createAttributeDefinition(
            $this->attributeDefinitionCreateMapper->mapToStruct($data, $sourceLanguage, $attributeType)
        );

        $this->notificationHandler->success(
            /** @Desc("Attribute '%name%' created.") */
            'attribute_definition.create.success',
            ['%name%' => $attributeDefinition->getName()],
            'ibexa_product_catalog'
        );

        if ($redirectToAttributeGroup) {
            return $this->redirectToRoute('ibexa.product_catalog.attribute_group.view', [
                'attributeGroupIdentifier' => $attributeDefinition->getGroup()->getIdentifier(),
            ]);
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.view', [
            'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
        ]);
    }
}
