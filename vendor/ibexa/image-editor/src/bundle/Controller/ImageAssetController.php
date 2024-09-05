<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImageEditor\Controller;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\ContentForms\Form\Type\Content\ContentFieldType;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\FieldType\Image\Value;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\ImageEditor\Output\ImageAssetCreate;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ImageAssetController
{
    private const CSRF_TOKEN_HEADER = 'X-CSRF-Token';

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Ibexa\AdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    public function __construct(
        Repository $repository,
        ContentService $contentService,
        LocationService $locationService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        CsrfTokenManagerInterface $csrfTokenManager,
        AssetMapper $assetMapper
    ) {
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->assetMapper = $assetMapper;
    }

    public function updateExistingImageAsset(
        Request $request,
        int $contentId,
        ?string $languageCode = null
    ) {
        if (!$this->isValidCsrfToken($request)) {
            return new JsonResponse('Invalid CSRF Token', Response::HTTP_FORBIDDEN);
        }

        $content = $this->contentService->loadContent($contentId, $languageCode ? [$languageCode] : null);

        $form = $this->buildImageFieldTypeForm($content, $languageCode);

        $form->submit($this->getJson($request));

        return $this->submitHandler->handleAjax($form, function (FieldData $data) use ($content, $languageCode): JsonResponse {
            $this->repository->beginTransaction();
            try {
                $this->createAndPublishImageDraft($content, $data, $languageCode);
                $this->repository->commit();
            } catch (\Exception $e) {
                $this->repository->rollback();
                throw $e;
            }

            return new JsonResponse();
        });
    }

    public function createNewImageAsset(
        Request $request,
        int $fromContentId,
        ?string $languageCode = null
    ) {
        if (!$this->isValidCsrfToken($request)) {
            return new JsonResponse('Invalid CSRF Token', Response::HTTP_FORBIDDEN);
        }

        $content = $this->contentService->loadContent($fromContentId, $languageCode ? [$languageCode] : null);

        $form = $this->buildImageFieldTypeForm($content, $languageCode);

        $form->submit($this->getJson($request));

        return $this->submitHandler->handleAjax($form, function (FieldData $data) use ($content, $languageCode): JsonResponse {
            $this->repository->beginTransaction();
            try {
                $newAsset = $this->contentService->copyContent(
                    $content->contentInfo,
                    $this->locationService->newLocationCreateStruct($content->contentInfo->getMainLocation()->parentLocationId)
                );

                $publishedAsset = $this->createAndPublishImageDraft($newAsset, $data, $languageCode);
                $this->repository->commit();
            } catch (\Exception $e) {
                $this->repository->rollback();
                throw $e;
            }

            $translatedName = $publishedAsset->getName($languageCode);
            // fallback to original translation if the image asset has been edited on the fly during related content edit
            $translatedName = $translatedName ?: $publishedAsset->getName($publishedAsset->contentInfo->mainLanguageCode);

            return new JsonResponse(
                new ImageAssetCreate(
                    $publishedAsset->id,
                    $publishedAsset->contentInfo->mainLocationId,
                    $translatedName
                )
            );
        });
    }

    private function isValidCsrfToken(Request $request): bool
    {
        $csrfTokenValue = $request->headers->get(self::CSRF_TOKEN_HEADER);

        return $this->csrfTokenManager->isTokenValid(
            new CsrfToken('authenticate', $csrfTokenValue)
        );
    }

    private function getJson(Request $request): array
    {
        return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    private function buildImageFieldTypeForm(
        Content $content,
        ?string $languageCode = null
    ): FormInterface {
        $fieldData = new FieldData([
            'field' => $this->assetMapper->getAssetField($content),
            'fieldDefinition' => $this->assetMapper->getAssetFieldDefinition(),
            'value' => new Value(),
        ]);

        $form = $this->formFactory->createBuilder(
            ContentFieldType::class,
            $fieldData,
            [
                'languageCode' => $languageCode ?? $content->contentInfo->mainLanguageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'csrf_protection' => false,
            ]
        )->getForm();

        $form->get('value')->add(
            'additionalData',
            CollectionType::class,
            [
                'entry_type' => TextType::class,
                'allow_add' => true,
            ]
        );

        return $form;
    }

    private function createAndPublishImageDraft(
        Content $newAsset,
        FieldData $fieldData,
        ?string $languageCode
    ): Content {
        $draft = $this->contentService->createContentDraft($newAsset->contentInfo);
        $updateStruct = $this->contentService->newContentUpdateStruct();
        $fieldIdentifier = $fieldData->fieldDefinition->identifier;

        $existingField = $newAsset->getField($fieldIdentifier);
        /** @var \Ibexa\Core\FieldType\Image\Value $value */
        $value = $fieldData->value;
        $value->alternativeText = $value->alternativeText ?? $existingField->value->alternativeText;
        $value->additionalData = $value->additionalData ?? $existingField->value->additionalData;
        $value->fileName = $existingField->value->fileName;

        $updateStruct->setField($fieldIdentifier, $value, $languageCode);

        $this->contentService->updateContent($draft->versionInfo, $updateStruct);

        return $this->contentService->publishVersion($draft->versionInfo);
    }
}

class_alias(ImageAssetController::class, 'Ibexa\Platform\Bundle\ImageEditor\Controller\ImageAssetController');
