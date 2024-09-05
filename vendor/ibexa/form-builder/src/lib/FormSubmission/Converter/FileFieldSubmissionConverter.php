<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Core\FieldType\BinaryFile\Value;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class FileFieldSubmissionConverter extends GenericFieldSubmissionConverter implements SiteAccess\SiteAccessAware
{
    /** @var string */
    private $sectionIdentifier;

    /** @var string */
    private $locationAttributeIdentifier;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess|null */
    private $currentSiteAccess;

    /** @var array */
    private $siteAccessGroups;

    /** @var \Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway */
    private $formSubmissionDataGateway;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider */
    private $repositoryConfigurationProvider;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * @param string $typeIdentifier
     * @param \Twig\Environment $twig
     * @param string $sectionIdentifier
     * @param string $locationAttributeIdentifier
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\SectionService $sectionService
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     * @param \Ibexa\FormBuilder\FormSubmission\Gateway\FormSubmissionDataGateway $formSubmissionDataGateway
     * @param array $siteAccessGroups
     * @param \Ibexa\Core\Helper\TranslationHelper $translationHelper
     * @param \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider $repositoryConfigurationProvider
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(
        string $typeIdentifier,
        Environment $twig,
        string $sectionIdentifier,
        string $locationAttributeIdentifier,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        SectionService $sectionService,
        Repository $repository,
        RouterInterface $router,
        ConfigResolverInterface $configResolver,
        FormSubmissionDataGateway $formSubmissionDataGateway,
        array $siteAccessGroups,
        TranslationHelper $translationHelper,
        RepositoryConfigurationProvider $repositoryConfigurationProvider,
        LoggerInterface $logger = null
    ) {
        parent::__construct($typeIdentifier, $twig);

        $this->sectionIdentifier = $sectionIdentifier;
        $this->locationAttributeIdentifier = $locationAttributeIdentifier;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->repository = $repository;
        $this->sectionService = $sectionService;
        $this->router = $router;
        $this->configResolver = $configResolver;
        $this->formSubmissionDataGateway = $formSubmissionDataGateway;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->translationHelper = $translationHelper;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
        $this->logger = $logger;
    }

    /**
     * @param string $persistenceValue
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|mixed
     */
    public function fromPersistenceValue(string $persistenceValue)
    {
        $contentId = parent::fromPersistenceValue($persistenceValue);

        if (empty($contentId)) {
            return null;
        }

        try {
            return $this->contentService->loadContentInfo($contentId);
        } catch (NotFoundException | UnauthorizedException $e) {
            return null;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $fieldValue
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     *
     * @return string
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function toPersistenceValue($fieldValue, Field $field, Form $form): string
    {
        if ($fieldValue === null) {
            return (string)$fieldValue;
        }

        /** @var \Ibexa\Core\Repository\Repository $repository */
        $repository = $this->repository;

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier('file');
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $form->getLanguageCode());

        $contentCreateStruct->setField('name', $fieldValue->getClientOriginalName());
        $contentCreateStruct->setField('file', new Value([
            'id' => null,
            'inputUri' => $fieldValue->getRealPath(),
            'fileName' => $fieldValue->getClientOriginalName(),
            'fileSize' => $fieldValue->getSize(),
            'mimeType' => $fieldValue->getMimeType(),
            'downloadCount' => 0,
            'uri' => $fieldValue->getRealPath(),
        ]));

        $parentLocationId = null;
        if ($field->hasAttribute($this->locationAttributeIdentifier)) {
            $parentLocationId = (int) $field->getAttributeValue($this->locationAttributeIdentifier);
        }

        $locationCreateStruct = $this->locationService->newLocationCreateStruct(
            $parentLocationId ?: (int) $this->configResolver->getParameter('form_builder.upload_location_id')
        );

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $repository->sudo(function () use ($contentCreateStruct, $locationCreateStruct) {
            $contentCreateStruct->sectionId = $this->sectionService->loadSectionByIdentifier($this->sectionIdentifier)->id;
            $draft = $this->contentService->createContent($contentCreateStruct, [$locationCreateStruct]);

            return $this->contentService->publishVersion($draft->versionInfo);
        });

        return json_encode($content->id);
    }

    /**
     * @param $fieldValue \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
     *
     * @return string
     */
    public function toDisplayValue($fieldValue): string
    {
        if ($fieldValue === null) {
            return (string)$fieldValue;
        }

        $isTrashed = $fieldValue->isTrashed();

        return $this->twig->render('@ibexadesign/form/file_submission_display_value.html.twig', [
            'url' => !$isTrashed ? $this->generateFileUrl($fieldValue) : null,
            'name' => $this->translationHelper->getTranslatedContentNameByContentInfo($fieldValue),
            'is_trashed' => $isTrashed,
        ]);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $fieldValue
     *
     * @return string
     */
    public function toExportValue($fieldValue): string
    {
        if ($fieldValue === null) {
            return (string)$fieldValue;
        }

        return !$fieldValue->isTrashed() ? $this->generateFileUrl($fieldValue) : '';
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue $fieldValue
     */
    public function removePersistenceValue($fieldValue): void
    {
        if (!$fieldValue->getValue()) {
            return;
        }
        try {
            $this->contentService->deleteContent($fieldValue->getValue());
        } catch (NotFoundException $e) {
            if ($this->logger) {
                $this->logger->error(sprintf('Content with ID %d does not exist anymore', $fieldValue->getValue()->id));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        $this->currentSiteAccess = $siteAccess;
    }

    /**
     * Generates absolute URL to file.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $fieldValue
     *
     * @return string
     */
    private function generateFileUrl(ContentInfo $fieldValue): string
    {
        $params = [
            'contentId' => $fieldValue->id,
            'locationId' => $fieldValue->mainLocationId,
            'siteaccess' => null,
        ];

        $isAdmin = new IsAdmin($this->siteAccessGroups);
        if ($this->currentSiteAccess !== null && !$isAdmin->isSatisfiedBy($this->currentSiteAccess)) {
            $currentRepository = $this->repositoryConfigurationProvider->getCurrentRepositoryAlias();

            $adminSiteaccesses = $this->siteAccessGroups[IbexaAdminUiBundle::ADMIN_GROUP_NAME];

            foreach ($adminSiteaccesses as $adminSiteaccess) {
                $adminSiteaccessRepository = $this->configResolver->getParameter('repository', null, $adminSiteaccess);
                $adminSiteaccessRepository = $adminSiteaccessRepository ?: $this->repositoryConfigurationProvider->getDefaultRepositoryAlias();

                if ($adminSiteaccessRepository === $currentRepository) {
                    $params['siteaccess'] = $adminSiteaccess;
                    break;
                }
            }
        }

        return $this->router->generate('ibexa.content.view', $params, RouterInterface::ABSOLUTE_URL);
    }

    /**
     * Nullify references to deleted content.
     *
     * @see \Ibexa\FormBuilder\Event\Subscriber\DeleteFileSubmissionEventSubscriber
     *
     * @param int $contentId
     */
    public function dropSubmissionContentReferences(int $contentId): void
    {
        $this->formSubmissionDataGateway->updateSubmissionValue($this->getTypeIdentifier(), (string)$contentId, null);
    }
}

class_alias(FileFieldSubmissionConverter::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\FileFieldSubmissionConverter');
