<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinition\Translation\TranslationAddType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTranslationController extends Controller
{
    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    public function __construct(
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    public function renderAction(Request $request): Response
    {
        $form = $this->formFactory->createNamed('add-translation', TranslationAddType::class, new TranslationAddData());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationAddData $data) {
                /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition $attributeDefinition */
                $attributeDefinition = $data->getAttributeDefinition();
                /** @var \Ibexa\Contracts\Core\Persistence\Content\Language $language */
                $language = $data->getLanguage();
                $baseLanguage = $data->getBaseLanguage();

                return $this->redirectToRoute(
                    'ibexa.product_catalog.attribute_definition.update',
                    [
                        'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
                        'fromLanguageCode' => null !== $baseLanguage ? $baseLanguage->languageCode : null,
                        'toLanguageCode' => $language->languageCode,
                    ]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationAddData $data */
        $data = $form->getData();

        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition $attributeDefinition */
        $attributeDefinition = $data->getAttributeDefinition();

        return $this->redirectToRoute('ibexa.product_catalog.attribute_definition.view', [
            'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
        ]);
    }
}
