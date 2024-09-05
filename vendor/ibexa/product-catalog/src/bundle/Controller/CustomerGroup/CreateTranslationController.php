<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup\Translation\TranslationAddType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     */
    public function renderAction(CustomerGroupInterface $customerGroup, Request $request): Response
    {
        $data = new TranslationAddData($customerGroup);
        $form = $this->formFactory->createNamed('add-translation', TranslationAddType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationAddData $data): RedirectResponse {
                $customerGroup = $data->getCustomerGroup();
                /** @var \Ibexa\Contracts\Core\Persistence\Content\Language $language */
                $language = $data->getLanguage();
                $baseLanguage = $data->getBaseLanguage();

                return $this->redirectToRoute(
                    'ibexa.product_catalog.customer_group.update',
                    [
                        'customerGroupId' => $customerGroup->getId(),
                        'fromLanguageCode' => null !== $baseLanguage ? $baseLanguage->languageCode : null,
                        'toLanguageCode' => $language->languageCode,
                    ]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $customerGroup = $data->getCustomerGroup();

        return $this->redirectToRoute('ibexa.product_catalog.customer_group.view', [
            'customerGroupId' => $customerGroup->getId(),
        ]);
    }
}
