<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinition\Translation\TranslationDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\ProductCatalog\Tab\AttributeDefinition\TranslationsTab;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTranslationController extends Controller
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        Repository $repository
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
    }

    public function executeAction(Request $request): Response
    {
        $form = $this->formFactory->createNamed('delete-translations', TranslationDeleteType::class);

        $form->handleRequest($request);

        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationDeleteData $data */
        $data = $form->getData();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface $attributeDefinition */
        $attributeDefinition = $data->getAttributeDefinition();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationDeleteData $data): Response {
                /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface $attributeDefinition */
                $attributeDefinition = $data->getAttributeDefinition();
                $languageCodes = $data->getLanguageCodes();

                $this->repository->beginTransaction();
                try {
                    if (is_iterable($languageCodes) && $attributeDefinition !== null) {
                        foreach ($languageCodes as $languageCode => $isChecked) {
                            $this->attributeDefinitionService->deleteAttributeDefinitionTranslation($attributeDefinition, $languageCode);
                        }
                    }
                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                return $this->redirectToRoute(
                    'ibexa.product_catalog.attribute_definition.view',
                    [
                        'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
                        '_fragment' => TranslationsTab::URI_FRAGMENT,
                    ]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.view', [
            'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
        ]);
    }
}
