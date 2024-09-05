<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\AttributeDefinitionUpdateMapper;
use Ibexa\Bundle\ProductCatalog\Form\FormMapper\AttributeDefinitionMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionUpdateType;
use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionUpdateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateController extends Controller
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private LanguageService $languageService;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private AttributeDefinitionUpdateMapper $attributeDefinitionUpdateMapper;

    private AttributeDefinitionMapper $attributeDefinitionMapper;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        LanguageService $languageService,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        AttributeDefinitionUpdateMapper $attributeDefinitionUpdateMapper,
        AttributeDefinitionMapper $attributeDefinitionMapper
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->languageService = $languageService;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->attributeDefinitionUpdateMapper = $attributeDefinitionUpdateMapper;
        $this->attributeDefinitionMapper = $attributeDefinitionMapper;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionUpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function executeAction(
        Request $request,
        AttributeDefinition $attributeDefinition,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $mainLanguageCode = $attributeDefinition->getLanguages()[0];
        $language = $language ?? $this->languageService->loadLanguage($mainLanguageCode);

        $attributeDefinitionUpdateData = $this->attributeDefinitionMapper->mapToFormData(
            $attributeDefinition,
            [
                'language' => $language,
                'baseLanguage' => $baseLanguage,
            ]
        );
        $form = $this->createUpdateForm(
            $attributeDefinition,
            $attributeDefinitionUpdateData,
            $mainLanguageCode
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handle = $this->submitHandler->handle(
                $form,
                function (AttributeDefinitionUpdateData $data) use ($attributeDefinition, $language): Response {
                    return $this->handleFormSubmission($attributeDefinition, $data, $language);
                }
            );

            if ($handle instanceof Response) {
                return $handle;
            }
        }

        return new AttributeDefinitionUpdateView(
            '@ibexadesign/product_catalog/attribute_definition/edit.html.twig',
            $attributeDefinition,
            $form,
            $language
        );
    }

    private function createUpdateForm(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateData $data,
        string $mainLanguageCode
    ): FormInterface {
        return $this->createForm(AttributeDefinitionUpdateType::class, $data, [
            'method' => Request::METHOD_POST,
            'language_code' => $data->getLanguage()->languageCode ?? null,
            'main_language_code' => $mainLanguageCode,
            'attribute_type' => $attributeDefinition->getType(),
        ]);
    }

    private function handleFormSubmission(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateData $data,
        Language $language
    ): RedirectResponse {
        $attributeDefinition = $this->attributeDefinitionService->updateAttributeDefinition(
            $attributeDefinition,
            $this->attributeDefinitionUpdateMapper->mapToStruct($data, $language)
        );

        $this->notificationHandler->success(
            /** @Desc("Attribute '%name%' updated.") */
            'attribute_definition.update.success',
            ['%name%' => $attributeDefinition->getName()],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.view', [
            'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
        ]);
    }
}
