<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller;

use Ibexa\Bundle\AdminUi\Controller\ContentViewController as AdminUiContentViewController;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryCreateData;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryDeleteData;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryMoveData;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryCreateType;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryDeleteType;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryMoveType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContentViewController extends Controller
{
    private AdminUiContentViewController $contentViewController;

    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyConfiguration $taxonomyConfiguration;

    private LocationService $locationService;

    private LanguageService $languageService;

    private FormFactoryInterface $formFactory;

    private TranslatorInterface $translator;

    public function __construct(
        AdminUiContentViewController $contentViewController,
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyConfiguration $taxonomyConfiguration,
        LocationService $locationService,
        LanguageService $languageService,
        FormFactoryInterface $formFactory,
        TranslatorInterface $translator
    ) {
        $this->contentViewController = $contentViewController;
        $this->taxonomyService = $taxonomyService;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }

    public function locationViewAction(Request $request, ContentView $view): ContentView
    {
        $contentView = $this->contentViewController->locationViewAction($request, $view);
        $parameters = $contentView->getParameters();

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['content_type'];
        $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($view->getContent()->id);
        $taxonomyName = $taxonomyEntry->taxonomy;
        $treeRootLocation = $this->getTreeRootLocation($taxonomyName);

        /** @phpstan-var array<\Ibexa\Contracts\Core\Repository\Values\Content\Language> */
        $languages = $this->languageService->loadLanguages();
        $language = 1 === \count($languages)
            ? array_shift($languages)
            : null;
        $taxonomyEntryCreateForm = $this->formFactory->create(
            TaxonomyEntryCreateType::class,
            new TaxonomyEntryCreateData(
                $contentType,
                $treeRootLocation,
                $language,
                $taxonomyEntry,
            ),
            ['taxonomy' => $taxonomyName],
        );
        $taxonomyEntryDeleteForm = $this->formFactory->create(
            TaxonomyEntryDeleteType::class,
            new TaxonomyEntryDeleteData($taxonomyEntry),
            ['taxonomy' => $taxonomyName]
        );
        $taxonomyEntryMoveForm = $this->formFactory->create(
            TaxonomyEntryMoveType::class,
            new TaxonomyEntryMoveData($taxonomyEntry),
            ['taxonomy' => $taxonomyName],
        );
        $taxonomyPath = $this->taxonomyService->getPath($taxonomyEntry);

        $parameters = array_merge(
            $parameters,
            [
                'form_taxonomy_entry_create' => $taxonomyEntryCreateForm->createView(),
                'form_taxonomy_entry_move' => $taxonomyEntryMoveForm->createView(),
                'form_taxonomy_entry_delete' => $taxonomyEntryDeleteForm->createView(),
                'taxonomy_entry' => $taxonomyEntry,
                'taxonomy_label' => $this->translator->trans(
                    /** @Ignore */
                    sprintf('taxonomy.%s', $taxonomyEntry->taxonomy),
                    [],
                    'ibexa_taxonomy'
                ),
                'taxonomy_path' => [...$taxonomyPath],
            ]
        );

        $contentView->setParameters($parameters);

        return $contentView;
    }

    public function redirectToRootEntryAction(Request $request, ContentView $view): RedirectResponse
    {
        $url = $this->generateUrl(
            'ibexa.content.view',
            [
                'contentId' => $view->getParameter('rootEntryContentId'),
            ],
        );

        return new RedirectResponse($url);
    }

    private function getTreeRootLocation(string $taxonomyName): Location
    {
        $parentRemoteId = $this->taxonomyConfiguration->getConfigForTaxonomy(
            $taxonomyName,
            TaxonomyConfiguration::CONFIG_PARENT_LOCATION_REMOTE_ID
        );

        return $this->locationService->loadLocationByRemoteId($parentRemoteId);
    }
}
