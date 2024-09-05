<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\UI\Tab;

use Ibexa\AdminUi\UI\Value\Content\Location\Mapper;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion\TaxonomyEntryId;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentAssignData;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentUnassignData;
use Ibexa\Taxonomy\Form\Type\TaxonomyContentAssignType;
use Ibexa\Taxonomy\Form\Type\TaxonomyContentUnassignType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class AssignedContentTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-location-view-assigned-content';

    private const PAGINATION_PARAM_NAME = 'assigned-content-tab-page';

    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyServiceInterface $taxonomyService;

    private SearchService $searchService;

    private Mapper $locationToUILocationMapper;

    private RequestStack $requestStack;

    private LanguageService $languageService;

    private FormFactoryInterface $formFactory;

    private ConfigResolverInterface $configResolver;

    private PermissionResolver $permissionResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyServiceInterface $taxonomyService,
        SearchService $searchService,
        Mapper $locationToUILocationMapper,
        RequestStack $requestStack,
        LanguageService $languageService,
        FormFactoryInterface $formFactory,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyService = $taxonomyService;
        $this->searchService = $searchService;
        $this->locationToUILocationMapper = $locationToUILocationMapper;
        $this->requestStack = $requestStack;
        $this->languageService = $languageService;
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
    }

    public function getIdentifier(): string
    {
        return 'assigned-content';
    }

    public function getName(): string
    {
        /** @Desc("Content") */
        return $this->translator->trans('tab.name.assigned_content', [], 'ibexa_taxonomy_content_view');
    }

    public function getOrder(): int
    {
        return 800;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/ibexa/taxonomy/taxonomy_entry/tab/assigned_content.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $taxonomyEntry */
        $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($content->id);

        $defaultPaginationLimit = $this->configResolver->getParameter('taxonomy.pagination.tab_assigned_content_limit');

        $query = new LocationQuery([
            'filter' => new LogicalAnd([
                new TaxonomyEntryId($taxonomyEntry->id),
                new IsMainLocation(IsMainLocation::MAIN),
            ]),
        ]);
        $pagination = new Pagerfanta(
            new LocationSearchAdapter(
                $query,
                $this->searchService
            )
        );

        $pagination->setMaxPerPage($defaultPaginationLimit);
        $pagination->setCurrentPage(max($this->getCurrentPage(), 1));
        $locationsArray = iterator_to_array($pagination);
        $locations = $this->locationToUILocationMapper->map($locationsArray);
        $taxonomyContentAssignForm = $this->createTaxonomyContentAssignForm($taxonomyEntry, $locations);
        $taxonomyContentUnassignForm = $this->createTaxonomyContentUnassignForm($taxonomyEntry, $locations);

        $viewParameters = [
            'languages' => $this->getLanguages(),
            'locations' => $locations,
            'taxonomy_entry' => $taxonomyEntry,
            'form_taxonomy_content_assign' => $taxonomyContentAssignForm->createView(),
            'form_taxonomy_content_unassign' => $taxonomyContentUnassignForm->createView(),
            'pagination' => $pagination,
            'pagination_options' => [
                'pageParameter' => sprintf('[%s]', self::PAGINATION_PARAM_NAME),
            ],
            'can_assign' => $taxonomyEntry->parent !== null
                && $this->permissionResolver->hasAccess('taxonomy', 'assign'),
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['contentType'];
        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return false;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'];
        $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($content->id);
        if ($taxonomyEntry->parent === null) {
            return false;
        }

        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);

        return (bool)$this->taxonomyConfiguration->getConfigForTaxonomy(
            $taxonomy,
            TaxonomyConfiguration::CONFIG_ASSIGNED_CONTENT_TAB
        );
    }

    /**
     * @param \Ibexa\Core\Repository\Values\Content\Location[] $locations
     */
    private function createTaxonomyContentAssignForm(TaxonomyEntry $taxonomyEntry, array $locations): FormInterface
    {
        return $this->formFactory->create(
            TaxonomyContentAssignType::class,
            new TaxonomyContentAssignData($taxonomyEntry, $locations),
            ['taxonomy' => $taxonomyEntry->name],
        );
    }

    /**
     * @param \Ibexa\Core\Repository\Values\Content\Location[] $locations
     */
    private function createTaxonomyContentUnassignForm(TaxonomyEntry $taxonomyEntry, array $locations): FormInterface
    {
        $contents = [];

        foreach ($locations as $location) {
            $contents[$location->contentId] = false;
        }

        return $this->formFactory->create(
            TaxonomyContentUnassignType::class,
            new TaxonomyContentUnassignData($taxonomyEntry, $contents),
            ['taxonomy' => $taxonomyEntry->name],
        );
    }

    private function getCurrentPage(): int
    {
        if (!$this->requestStack->getCurrentRequest()) {
            return 1;
        }

        return $this->requestStack->getCurrentRequest()->query->getInt(
            self::PAGINATION_PARAM_NAME,
            1
        );
    }

    /**
     * @return array<string, \Ibexa\Contracts\Core\Repository\Values\Content\Language>
     */
    private function getLanguages(): array
    {
        $languages = $this->languageService->loadLanguages();
        $languageCodes = array_column($languages, 'languageCode');

        $map = array_combine($languageCodes, $languages);

        return !$map ? [] : $map;
    }
}
