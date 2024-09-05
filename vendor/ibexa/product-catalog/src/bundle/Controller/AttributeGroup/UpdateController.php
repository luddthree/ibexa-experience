<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeGroup;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\FormMapper\AttributeGroupMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupUpdateType;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupUpdateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Edit;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateController extends Controller
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private AttributeGroupMapper $attributeGroupMapper;

    private LanguageService $languageService;

    public function __construct(
        LocalAttributeGroupServiceInterface $attributeGroupService,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        AttributeGroupMapper $attributeGroupMapper,
        LanguageService $languageService
    ) {
        $this->attributeGroupService = $attributeGroupService;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->attributeGroupMapper = $attributeGroupMapper;
        $this->languageService = $languageService;
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\Exception\BadRequestException
     *
     * @return \Ibexa\Bundle\ProductCatalog\View\AttributeGroupUpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function executeAction(
        Request $request,
        AttributeGroup $attributeGroup,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->denyAccessUnlessGranted(new Edit());
        $mainLanguageCode = $attributeGroup->getLanguages()[0];
        $language = $language ?? $this->languageService->loadLanguage($mainLanguageCode);

        $attributeGroupUpdateData = $this->attributeGroupMapper->mapToFormData(
            $attributeGroup,
            [
                'language' => $language,
                'baseLanguage' => $baseLanguage,
            ]
        );
        $form = $this->createUpdateForm($attributeGroupUpdateData, $mainLanguageCode);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $response = $this->submitHandler->handle(
                $form,
                function (AttributeGroupUpdateData $data) use ($attributeGroup): Response {
                    return $this->handleFormSubmit($attributeGroup, $data);
                }
            );

            if ($response instanceof Response) {
                return $response;
            }
        }

        return new AttributeGroupUpdateView('@ibexadesign/product_catalog/attribute_group/edit.html.twig', $attributeGroup, $form);
    }

    private function createUpdateForm(AttributeGroupUpdateData $data, string $mainLanguageCode): FormInterface
    {
        return $this->createForm(AttributeGroupUpdateType::class, $data, [
            'method' => Request::METHOD_POST,
            'language_code' => $data->getLanguage()->languageCode ?? null,
            'main_language_code' => $mainLanguageCode,
        ]);
    }

    private function handleFormSubmit(
        AttributeGroupInterface $attributeGroup,
        AttributeGroupUpdateData $data
    ): Response {
        $updateStruct = $this->attributeGroupService->newAttributeGroupUpdateStruct($attributeGroup);
        $updateStruct->setIdentifier($data->getIdentifier());
        $updateStruct->setPosition($data->getPosition());
        $updateStruct->setNames([
            $data->getLanguage()->languageCode => $data->getName(),
        ]);

        $attributeGroup = $this->attributeGroupService->updateAttributeGroup($attributeGroup, $updateStruct);

        $this->notificationHandler->success(
            /** @Desc("Attribute Group '%name%' updated.") */
            'attribute_group.update.success',
            ['%name%' => $attributeGroup->getName()],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.attribute_group.view', [
            'attributeGroupIdentifier' => $attributeGroup->getIdentifier(),
        ]);
    }
}
