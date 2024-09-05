<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypeAddress\Controller;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Data\Mapper\ContentCreateMapper;
use Ibexa\ContentForms\Data\Mapper\ContentUpdateMapper;
use Ibexa\ContentForms\Form\Type\Content\ContentEditType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\FieldTypeAddress\FieldType\Value;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddressController extends Controller
{
    private ContentTypeService $contentTypeService;

    private LocationService $locationService;

    private ContentService $contentService;

    private FormFactoryInterface $formFactory;

    private Repository $repository;

    private PermissionResolver $permissionResolver;

    public function __construct(
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        ContentService $contentService,
        FormFactoryInterface $formFactory,
        PermissionResolver $permissionResolver,
        Repository $repository
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
    }

    public function countryCreateFormAction(Request $request, string $formName): Response
    {
        $languageCode = $request->get('languageCode');
        $fieldIdentifier = $request->get('fieldIdentifier');

        $contentData = $this->getContentCreateData($request);

        $options = [
            'languageCode' => $languageCode,
            'mainLanguageCode' => $languageCode,
            'contentCreateStruct' => $contentData,
            'drafts_enabled' => false,
        ];

        $form = $this->permissionResolver->sudo(
            function () use ($formName, $contentData, $options): FormInterface {
                return $this->formFactory->createNamed($formName, ContentEditType::class, $contentData, $options);
            },
            $this->repository
        );

        return $this->render('@ibexadesign/address/field_type/content_edit_form/address_country_form.html.twig', [
            'data' => $contentData,
            'form' => $form->createView(),
            'field' => $fieldIdentifier,
        ]);
    }

    public function countryUpdateFormAction(Request $request, string $formName): Response
    {
        $languageCode = $request->get('languageCode');
        $fieldIdentifier = $request->get('fieldIdentifier');
        $contentId = (int)$request->get('contentId');

        $draft = $this->contentService->loadContent($contentId);

        $contentData = $this->getContentUpdateData($request, $draft);

        $options = [
            'languageCode' => $languageCode,
            'mainLanguageCode' => $languageCode,
            'content' => $draft,
            'contentUpdateStruct' => $contentData,
            'drafts_enabled' => false,
        ];

        $form = $this->formFactory->createNamed($formName, ContentEditType::class, $contentData, $options);

        return $this->render('@ibexadesign/address/field_type/content_edit_form/address_country_form.html.twig', [
            'data' => $contentData,
            'form' => $form->createView(),
            'field' => $fieldIdentifier,
        ]);
    }

    private function getContentCreateData(Request $request): ContentCreateData
    {
        $contentCreateMapper = new ContentCreateMapper();
        $fieldIdentifier = $request->get('fieldIdentifier');
        $languageCode = $request->get('languageCode');
        $parentLocationId = (int)$request->get('parentLocationId');

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $request->get('contentTypeIdentifier')
        );

        $contentData = $contentCreateMapper->mapToFormData(
            $contentType,
            [
                'mainLanguageCode' => $languageCode,
                'parentLocation' => $this->locationService->newLocationCreateStruct(
                    $parentLocationId
                ),
            ]
        );

        $contentData->addFieldData(
            new FieldData([
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldIdentifier,
                    'languageCode' => $languageCode,
                ]),
                'fieldDefinition' => $contentType->getFieldDefinition($fieldIdentifier),
                'value' => new Value(
                    $request->get('name'),
                    $request->get('country')
                ),
            ])
        );

        return $contentData;
    }

    private function getContentUpdateData(Request $request, Content $draft): ContentUpdateData
    {
        $contentUpdateMapper = new ContentUpdateMapper();
        $fieldIdentifier = $request->get('fieldIdentifier');
        $languageCode = $request->get('languageCode');

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $request->get('contentTypeIdentifier')
        );

        $contentData = $contentUpdateMapper->mapToFormData(
            $draft,
            [
                'contentType' => $contentType,
                'languageCode' => $languageCode,
            ]
        );

        $contentData->addFieldData(
            new FieldData([
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldIdentifier,
                    'languageCode' => $languageCode,
                ]),
                'fieldDefinition' => $contentType->getFieldDefinition($fieldIdentifier),
                'value' => new Value(
                    $request->get('name'),
                    $request->get('country')
                ),
            ])
        );

        return $contentData;
    }
}
