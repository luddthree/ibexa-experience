<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup\Translation\TranslationDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\Tab\AttributeDefinition\TranslationsTab;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTranslationController extends Controller
{
    private CustomerGroupServiceInterface $customerGroupService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    public function __construct(
        CustomerGroupServiceInterface $customerGroupService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        Repository $repository
    ) {
        $this->customerGroupService = $customerGroupService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     */
    public function executeAction(CustomerGroupInterface $customerGroup, Request $request): Response
    {
        $data = new TranslationDeleteData($customerGroup);
        $form = $this->formFactory->createNamed('delete-translations', TranslationDeleteType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationDeleteData $data): RedirectResponse {
                $customerGroup = $data->getCustomerGroup();
                $languageCodes = $data->getLanguageCodes() ?? [];

                $this->ensureAtLeastOneLanguageWillRemain($customerGroup->getLanguages(), $languageCodes);

                $this->repository->beginTransaction();
                try {
                    foreach ($languageCodes as $languageCode => $isChecked) {
                        $struct = new CustomerGroupDeleteTranslationStruct($customerGroup, $languageCode);
                        $this->customerGroupService->deleteCustomerGroupTranslation($struct);
                    }

                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                return $this->redirectToRoute(
                    'ibexa.product_catalog.customer_group.view',
                    [
                        'customerGroupId' => $customerGroup->getId(),
                        '_fragment' => TranslationsTab::URI_FRAGMENT,
                    ]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.customer_group.view', [
            'customerGroupId' => $customerGroup->getId(),
        ]);
    }

    /**
     * @param array<string> $customerGroupLanguages
     * @param array<string, bool> $languagesToRemove
     */
    private function ensureAtLeastOneLanguageWillRemain(array $customerGroupLanguages, array $languagesToRemove): void
    {
        $remainingLanguageCodes = array_diff($customerGroupLanguages, array_keys($languagesToRemove));

        if (empty($remainingLanguageCodes)) {
            throw new RuntimeException('Cannot remove all translations from Customer Group');
        }
    }
}
