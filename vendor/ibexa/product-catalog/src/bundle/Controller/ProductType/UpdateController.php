<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Edit;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UpdateController extends Controller
{
    private ContentTypeService $contentTypeService;

    private LanguageService $languageService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private TranslatorInterface $translator;

    private UserService $userService;

    private PermissionResolver $permissionResolver;

    public function __construct(
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    public function updateAction(
        ProductTypeInterface $productType,
        ?string $languageCode = null,
        ?string $baseLanguageCode = null
    ): RedirectResponse {
        if (!($productType instanceof ProductType)) {
            return $this->redirectToRoute('ibexa.product_catalog.product_type.list');
        }

        $this->denyAccessUnlessGranted(new Edit());

        $language = $this->languageService->loadLanguage(
            $languageCode ?? $productType->getContentType()->mainLanguageCode
        );

        $baseLanguage = null;
        if ($baseLanguageCode !== null) {
            $baseLanguage = $this->languageService->loadLanguage($baseLanguageCode);
        }

        $contentType = $productType->getContentType();

        try {
            $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($contentType->id, true);
        } catch (NotFoundException $e) {
            $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
        }

        if ($contentTypeDraft->modifierId !== $this->permissionResolver->getCurrentUserReference()->getUserId()) {
            $this->notificationHandler->error(
                /** @Desc("Draft of Product Type '%name%' already exists and is locked by '%userContentName%'") */
                'product_type.edit.error.already_exists',
                [
                    '%name%' => $contentType->getName(),
                    '%userContentName%' => $this->getUserNameById($contentTypeDraft->modifierId),
                ],
                'ibexa_product_catalog'
            );

            return $this->redirectToRoute('ibexa.product_catalog.product_type.view', [
                'productTypeIdentifier' => $productType->getIdentifier(),
            ]);
        }

        return $this->redirectToRoute('ibexa.content_type.update', [
            'contentTypeId' => $contentTypeDraft->id,
            'contentTypeGroupId' => $contentTypeDraft->contentTypeGroups[0]->id,
            'toLanguageCode' => $language->languageCode,
            'fromLanguageCode' => $baseLanguage ? $baseLanguage->languageCode : null,
        ]);
    }

    private function getUserNameById(int $userId): ?string
    {
        try {
            $user = $this->userService->loadUser($userId);
        } catch (NotFoundException $e) {
            return $this->translator->trans(
                /** @Desc("another user") */
                'product_type.user_name.can_not_be_fetched',
                [],
                'ibexa_product_catalog'
            );
        }

        return $user->getName();
    }
}
