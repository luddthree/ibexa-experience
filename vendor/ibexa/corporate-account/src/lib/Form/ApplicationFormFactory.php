<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form;

use Ibexa\ContentForms\Data\Mapper\ContentCreateMapper;
use Ibexa\ContentForms\Data\Mapper\ContentUpdateMapper;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Data\Application\ApplicationsDeleteData;
use Ibexa\CorporateAccount\Form\Type\Application\ApplicationEditInternalType;
use Ibexa\CorporateAccount\Form\Type\Application\ApplicationEditType;
use Ibexa\CorporateAccount\Form\Type\Application\ApplicationsDeleteType;
use Ibexa\CorporateAccount\Form\Type\Application\ApplicationSearchType;
use Ibexa\CorporateAccount\Form\Type\Register\CorporateAccountApplicationCreateType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ApplicationFormFactory extends ContentFormFactory
{
    private LocationService $locationService;

    private ContentTypeService $contentTypeService;

    public function __construct(
        FormFactoryInterface $formFactory,
        CorporateAccountConfiguration $corporateAccount,
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        parent::__construct($formFactory, $corporateAccount);

        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
    }

    public function getCreateForm(Location $parentLocation, string $languageCode): FormInterface
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccount->getContentTypeIdentifier('application')
        );

        $corporateAccountApplicationCreateData = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocation->id),
        ]);

        return $this->createContentEditForm(CorporateAccountApplicationCreateType::class, $corporateAccountApplicationCreateData, [
            'languageCode' => $languageCode,
            'mainLanguageCode' => $languageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getSearchForm(): FormInterface
    {
        return $this->formFactory->create(ApplicationSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }

    public function getEditForm(Application $application): FormInterface
    {
        $applicationContent = $application->getContent();
        $mainLanguageCode = $applicationContent->getVersionInfo()->getContentInfo()->mainLanguageCode;

        $applicationUpdateData = (new ContentUpdateMapper())->mapToFormData($application->getContent(), [
            'languageCode' => $mainLanguageCode,
            'contentType' => $applicationContent->getContentType(),
        ]);

        return $this->createContentEditForm(ApplicationEditType::class, $applicationUpdateData, [
            'languageCode' => $mainLanguageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getEditInternalForm(Application $application): FormInterface
    {
        $applicationContent = $application->getContent();
        $mainLanguageCode = $applicationContent->getVersionInfo()->getContentInfo()->mainLanguageCode;

        $applicationUpdateData = (new ContentUpdateMapper())->mapToFormData($application->getContent(), [
            'languageCode' => $mainLanguageCode,
            'contentType' => $applicationContent->getContentType(),
        ]);

        return $this->createContentEditForm(ApplicationEditInternalType::class, $applicationUpdateData, [
            'languageCode' => $mainLanguageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'drafts_enabled' => false,
        ]);
    }

    public function getDeleteApplicationsForm(?ApplicationsDeleteData $data = null): FormInterface
    {
        return $this->formFactory->create(ApplicationsDeleteType::class, $data);
    }
}
