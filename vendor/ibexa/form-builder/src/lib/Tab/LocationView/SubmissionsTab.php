<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Tab\LocationView;

use Ibexa\Contracts\AdminUi\Tab\AbstractTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Ibexa\FormBuilder\FieldType\FormFactory;
use Ibexa\FormBuilder\FieldType\Type;
use Ibexa\FormBuilder\Form\Data\Submission\SubmissionRemoveData;
use Ibexa\FormBuilder\Pagination\Pagerfanta\SubmissionsAdapter;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SubmissionsTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-location-view-submissions';

    /** @var \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface */
    private $formSubmissionService;

    /** @var \Ibexa\FormBuilder\FieldType\FormFactory */
    private $formFactory;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\FormBuilder\FieldType\Type */
    private $formBuilderType;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        FormSubmissionServiceInterface $formSubmissionService,
        FormFactory $formFactory,
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        Type $formBuilderType,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator);

        $this->formSubmissionService = $formSubmissionService;
        $this->formFactory = $formFactory;
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->formBuilderType = $formBuilderType;
        $this->configResolver = $configResolver;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'submissions';
    }

    /**
     * @return string
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function getName(): string
    {
        /** @Desc("Submissions") */
        return $this->translator->trans('tab.name.submissions', [], 'ibexa_locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 150;
    }

    /**
     * Get information about tab presence.
     *
     * @param array $parameters
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function evaluate(array $parameters): bool
    {
        $location = $parameters['location'];
        $contentType = $this->contentTypeService
            ->loadContentType($location->getContentInfo()->contentTypeId);

        return $this->hasFieldType($contentType);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     */
    private function hasFieldType(ContentType $contentType): bool
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($this->formBuilderType->getFieldTypeIdentifier() === $fieldDefinition->fieldTypeIdentifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function renderView(array $parameters): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $parameters['location'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'];
        $submissionsPaginationParams = [
            'limit' => $this->configResolver->getParameter('form_builder.pagination.submission_limit'),
            'page' => $parameters['paginationParams']['page'],
            'route_name' => $parameters['paginationParams']['route_name'],
            'route_params' => $parameters['paginationParams']['route_params'],
        ];

        $submissionsPagerfanta = new Pagerfanta(
            new SubmissionsAdapter(
                $this->formSubmissionService,
                $content->contentInfo,
                $content->prioritizedFieldLanguageCode ?? $content->contentInfo->mainLanguageCode
            )
        );

        $submissionsPagerfanta->setMaxPerPage($submissionsPaginationParams['limit']);
        $submissionsPagerfanta->setCurrentPage(min($submissionsPaginationParams['page'], $submissionsPagerfanta->getNbPages()));

        $removeSubmissionForm = $this->createSubmissionRemoveForm(
            $location,
            $submissionsPagerfanta->getCurrentPageResults()->getItems()
        );

        $languages = $this->loadContentLanguages($content);

        $viewParameters = [
            'submissions_pager' => $submissionsPagerfanta,
            'submissions_pagination_params' => $submissionsPaginationParams,
            'form_content_submission_remove' => $removeSubmissionForm->createView(),
            'content' => $content,
            'languages' => $languages,
        ];

        return $this->twig->render(
            '@ibexadesign/content/tab/submissions/tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    /**
     * Loads system languages with filtering applied.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return array
     */
    public function loadContentLanguages(Content $content): array
    {
        $contentLanguages = $content->versionInfo->languageCodes;

        $filter = static function (Language $language) use ($contentLanguages) {
            return $language->enabled && \in_array($language->languageCode, $contentLanguages, true);
        };

        $languagesByCode = [];

        foreach (array_filter($this->languageService->loadLanguages(), $filter) as $language) {
            $languagesByCode[$language->languageCode] = $language;
        }

        $saLanguages = [];

        foreach ($this->configResolver->getParameter('languages') as $languageCode) {
            if (!isset($languagesByCode[$languageCode])) {
                continue;
            }

            $saLanguages[] = $languagesByCode[$languageCode];
            unset($languagesByCode[$languageCode]);
        }

        return array_merge($saLanguages, array_values($languagesByCode));
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission[] $submissions
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createSubmissionRemoveForm(Location $location, array $submissions): FormInterface
    {
        return $this->formFactory->removeSubmission(
            new SubmissionRemoveData($location->getContentInfo(), $this->getSubmissionChoices($submissions))
        );
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission[] $submissions
     *
     * @return array
     */
    private function getSubmissionChoices(array $submissions): array
    {
        $submissionIds = array_map(static function ($submission) {
            /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission $submission */
            return $submission->getId();
        }, $submissions);

        return array_combine($submissionIds, array_fill_keys($submissionIds, false));
    }
}

class_alias(SubmissionsTab::class, 'EzSystems\EzPlatformFormBuilder\Tab\LocationView\SubmissionsTab');
