<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeGroup;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroup\Translation\TranslationDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\ProductCatalog\Tab\AttributeGroup\TranslationsTab;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTranslationController extends Controller
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    public function __construct(
        LocalAttributeGroupServiceInterface $attributeGroupService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        Repository $repository
    ) {
        $this->attributeGroupService = $attributeGroupService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
    }

    public function executeAction(Request $request): Response
    {
        $form = $this->formFactory->createNamed('delete-translations', TranslationDeleteType::class);

        $form->handleRequest($request);

        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation\TranslationDeleteData $data */
        $data = $form->getData();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface $attributeGroup */
        $attributeGroup = $data->getAttributeGroup();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationDeleteData $data) {
                /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface $attributeGroup */
                $attributeGroup = $data->getAttributeGroup();
                $languageCodes = $data->getLanguageCodes();

                $this->repository->beginTransaction();
                try {
                    if (is_iterable($languageCodes) && $attributeGroup !== null) {
                        foreach ($languageCodes as $languageCode => $isChecked) {
                            $this->attributeGroupService->deleteAttributeGroupTranslation($attributeGroup, $languageCode);
                        }
                    }
                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                return $this->redirectToRoute(
                    'ibexa.product_catalog.attribute_group.view',
                    [
                        'attributeGroupIdentifier' => $attributeGroup->getIdentifier(),
                        '_fragment' => TranslationsTab::URI_FRAGMENT,
                    ]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_group.view', [
            'attributeGroupIdentifier' => $attributeGroup->getIdentifier(),
        ]);
    }
}
