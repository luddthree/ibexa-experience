<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\CustomerGroupUpdateMapper;
use Ibexa\Bundle\ProductCatalog\Form\FormMapper\CustomerGroupMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupUpdateType;
use Ibexa\Bundle\ProductCatalog\View\CustomerGroupUpdateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Edit;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private LanguageService $languageService;

    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupUpdateMapper $customerGroupUpdateMapper;

    private CustomerGroupMapper $customerGroupMapper;

    private SubmitHandler $submitHandler;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        LanguageService $languageService,
        CustomerGroupServiceInterface $customerGroupService,
        CustomerGroupUpdateMapper $customerGroupUpdateMapper,
        CustomerGroupMapper $customerGroupMapper,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->languageService = $languageService;
        $this->customerGroupService = $customerGroupService;
        $this->customerGroupUpdateMapper = $customerGroupUpdateMapper;
        $this->customerGroupMapper = $customerGroupMapper;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\CustomerGroupUpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(
        Request $request,
        CustomerGroup $customerGroup,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->denyAccessUnlessGranted(new Edit());

        $mainLanguageCode = $customerGroup->getFirstLanguage();
        $language = $language ?? $this->languageService->loadLanguage($mainLanguageCode);

        $customerGroupData = $this->customerGroupMapper->mapToFormData($customerGroup, [
            'baseLanguage' => $baseLanguage,
            'language' => $language,
        ]);
        $form = $this->createForm(CustomerGroupUpdateType::class, $customerGroupData, [
            'action' => $this->generateUrl('ibexa.product_catalog.customer_group.update', [
                'customerGroupId' => $customerGroup->getId(),
                'fromLanguageCode' => $baseLanguage->languageCode ?? null,
                'toLanguageCode' => $language->languageCode,
            ]),
            'method' => Request::METHOD_POST,
            'translation_mode' => $mainLanguageCode !== $language->languageCode,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CustomerGroupUpdateData $data): Response {
                $customerGroupUpdateStruct = $this->customerGroupUpdateMapper->mapToStruct($data);
                $customerGroup = $this->customerGroupService->updateCustomerGroup($customerGroupUpdateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Customer Group '%name%' updated.") */
                    'customer_group.update.success',
                    ['%name%' => $customerGroup->getName()],
                    'ibexa_product_catalog'
                );

                return $this->redirectToRoute('ibexa.product_catalog.customer_group.view', [
                    'customerGroupId' => $customerGroup->getId(),
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new CustomerGroupUpdateView(
            '@ibexadesign/product_catalog/customer_group/edit.html.twig',
            $customerGroup,
            $form,
        );
    }
}
