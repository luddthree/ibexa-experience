<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Controller;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\AdminUi\View\ContentTranslateView;
use Ibexa\ContentForms\Content\View\ContentCreateView;
use Ibexa\ContentForms\Content\View\ContentEditView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\PageBuilder\Event\GenerateContentPreviewUrlEvent;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SiteAccessSelector;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\FieldTypePage\ApplicationConfig\Providers as PageFieldTypeApplicationConfig;
use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Ibexa\FieldTypePage\FieldType\LandingPage\Mapper\LandingPageFormMapper;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\PageBuilder\Data\RequestBlockConfiguration;
use Ibexa\PageBuilder\Form\Type\Block\RequestBlockConfigurationType;
use Ibexa\PageBuilder\PageBuilder\Timeline\Collector as TimelineEventsCollector;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageEditContext;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageTranslateContext;
use Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator;
use JMS\Serializer\Serializer;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PageController extends Controller
{
    public const EDITORIAL_MODE_PARAMETER = 'editorial_mode';
    private const PAGE_BUILDER_VISITED_PREFERENCE = 'page_builder_visited';

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitionsInterface */
    private $layoutDefinitionsProvider;

    /** @var \Ibexa\FieldTypePage\ApplicationConfig\Providers\BlockDefinitionsInterface */
    private $blockDefinitionsProvider;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter */
    private $converter;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Ibexa\AdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \Ibexa\AdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $pageFieldType;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\PageBuilder\PageBuilder\Timeline\Collector */
    private $timelineEventsCollector;

    /** @var \JMS\Serializer\Serializer */
    private $serializer;

    /** @var \Ibexa\PageBuilder\Security\EditorialMode\TokenAuthenticator|null */
    private $tokenAuthenticator;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    private SiteAccessSelector $siteAccessPicker;

    private SiteaccessResolverInterface $siteAccessResolver;

    private EventDispatcherInterface $eventDispatcher;

    private UserPreferenceService $userPreferenceService;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        PageFieldTypeApplicationConfig\BlockDefinitionsInterface $blockDefinitionsProvider,
        PageFieldTypeApplicationConfig\LayoutDefinitionsInterface $layoutDefinitionsProvider,
        PageConverter $converter,
        LanguageService $languageService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        Type $pageFieldType,
        RouterInterface $router,
        TimelineEventsCollector $timelineEventsCollector,
        Serializer $serializer,
        ?TokenAuthenticator $tokenAuthenticator,
        TranslationHelper $translationHelper,
        SiteAccessSelector $siteAccessPicker,
        SiteaccessResolverInterface $siteAccessResolver,
        EventDispatcherInterface $eventDispatcher,
        UserPreferenceService $userPreferenceService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->blockDefinitionsProvider = $blockDefinitionsProvider;
        $this->layoutDefinitionsProvider = $layoutDefinitionsProvider;
        $this->converter = $converter;
        $this->languageService = $languageService;
        $this->notificationHandler = $notificationHandler;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->pageFieldType = $pageFieldType;
        $this->router = $router;
        $this->timelineEventsCollector = $timelineEventsCollector;
        $this->serializer = $serializer;
        $this->tokenAuthenticator = $tokenAuthenticator;
        $this->translationHelper = $translationHelper;
        $this->siteAccessPicker = $siteAccessPicker;
        $this->siteAccessResolver = $siteAccessResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function previewAction(Request $request)
    {
        $this->markPageBuilderVisitedForCurrentUser();

        /** @var \Symfony\Component\Routing\Route $route */
        $route = $this->router->getRouteCollection()->get('ibexa.version.preview');
        $controller = $route->getDefaults()['_controller'];

        $contentId = (int) $request->attributes->get('contentId');
        $locationId = $request->attributes->get('locationId');
        $languageCode = $request->attributes->get('language');
        $locationId = $locationId === null ? null : (int) $locationId;
        $fallbackSiteAccessName = $request->attributes->get('siteAccessName');

        $content = $this->contentService->loadContent($contentId);
        $versionInfo = $content->getVersionInfo();
        $isAlwaysAvailable = $content->contentInfo->alwaysAvailable;

        if ($locationId === null && $versionInfo->isDraft()) {
            $parentLocations = $this->locationService->loadParentLocationsForDraftContent($versionInfo);
            $parentLocation = reset($parentLocations) ?: null;
            $siteAccessList = $parentLocation !== null
                ? $this
                    ->siteAccessResolver
                    ->getSiteAccessesListForLocation(
                        $parentLocation,
                        null,
                        $languageCode,
                    )
                : $this->siteAccessResolver->getSiteAccessesListForContent($content);
        } elseif ($locationId !== null) {
            $location = $this->locationService->loadLocation($locationId);
            $siteAccessList = $this
                ->siteAccessResolver
                ->getSiteAccessesListForLocation(
                    $location,
                    $versionInfo->versionNo,
                    $languageCode,
                );
        } else {
            $siteAccessList = $this->siteAccessResolver->getSiteAccessesListForContent($content);
        }

        $language = $this->languageService->loadLanguage($languageCode);
        $resolvedSiteAccess = $isAlwaysAvailable
            ? $fallbackSiteAccessName
            : $this->siteAccessPicker->selectSiteAccess(
                new Context(
                    $language,
                    $content,
                ),
                array_column($siteAccessList, 'name')
            );

        $queryParameters = [];
        if ($request->query->has('viewType')) {
            $queryParameters['viewType'] = $request->query->get('viewType');
        }
        $response = $this->forward(
            $controller,
            [
                'contentId' => $contentId,
                'versionNo' => $request->attributes->get('versionNo'),
                'language' => $request->attributes->get('language'),
                'siteAccessName' => $resolvedSiteAccess,
                'locationId' => $locationId,
            ],
            $queryParameters
        );

        $isEditorialMode = $request->attributes->get('editorial_mode', false);
        $response->headers->set('X-Editorial-Mode', (string) $isEditorialMode, true);

        return $response;
    }

    /**
     * Loops through fieldtypes and returns first occurence of `ezlandingpage`.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return string|null
     */
    private function getPageFieldIdentifier(ContentType $contentType): ?string
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($this->pageFieldType->getFieldTypeIdentifier() === $fieldDefinition->fieldTypeIdentifier) {
                return $fieldDefinition->identifier;
            }
        }

        return null;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     * @param string $targetSiteaccess
     * @param string|null $routeSiteaccess
     *
     * @return string
     */
    private function getDisplayUrl(
        ?Location $location,
        Content $content,
        VersionInfo $versionInfo,
        Language $language,
        string $targetSiteaccess,
        string $routeSiteaccess = null
    ): string {
        if (null !== $location) {
            return $this->router->generate(
                'ibexa.url.alias',
                [
                    'locationId' => $location->id,
                    'siteaccess' => $targetSiteaccess,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->generateUrl(
            'ibexa.page_builder.content.preview',
            [
                'contentId' => $content->id,
                'language' => $language->languageCode,
                'versionNo' => $versionInfo->versionNo,
                'siteAccessName' => $targetSiteaccess,
                'siteaccess' => $routeSiteaccess,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param \Ibexa\ContentForms\Content\View\ContentEditView $view
     *
     * @return \Ibexa\ContentForms\Content\View\ContentEditView
     *
     * @throws \Exception
     */
    public function editAction(Request $request, ContentEditView $view): ContentEditView
    {
        $content = $view->getContent();
        $contentType = $view->getParameter('content_type');
        $location = $view->getLocation();
        $language = $view->getLanguage();
        $versionInfo = $view->getContent()->getVersionInfo();
        $isContentWithoutLocation = false;
        $parentLocation = null;

        if ($location === null && $versionInfo->isDraft()) {
            $isContentWithoutLocation = true;
            $parentLocations = $this->locationService->loadParentLocationsForDraftContent($versionInfo);
            $parentLocation = reset($parentLocations) ?: null;
        }

        $siteAccessList = $this
            ->siteAccessResolver
            ->getSiteAccessesListForLocation(
                $isContentWithoutLocation ? $parentLocation : $location,
                null,
                $language->languageCode
            );

        $siteAccessName = $this->siteAccessPicker->selectSiteAccess(
            new Context(
                $language,
                $content,
                $isContentWithoutLocation ? $parentLocation : $location,
            ),
            array_column($siteAccessList, 'name')
        );

        try {
            $creator = $this->contentService->loadContent($content->contentInfo->ownerId);
        } catch (NotFoundException | UnauthorizedException $e) {
            $creator = null;
        }

        $pageFieldIdentifier = $this->getPageFieldIdentifier($contentType);
        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $pageFieldValue */
        $pageFieldValue = $content->getFieldValue($pageFieldIdentifier);

        $previewModeUrl = $this->generateUrl('ibexa.content.view', [
            'locationId' => $isContentWithoutLocation && $parentLocation !== null
                ? $parentLocation->id
                : $location->id,
            'contentId' => $content->id,
        ]);

        $context = $this->createEditContext(
            $isContentWithoutLocation && $parentLocation !== null ? $parentLocation : $location,
            $content,
            $versionInfo,
            $language,
            $pageFieldValue->getPage()
        );
        $referenceDate = $this->resolveReferenceDate($request);

        $previewUrl = $this->generateContentPreviewUrl(
            $content,
            [
                'contentId' => $content->id,
                'language' => $language->languageCode,
                'versionNo' => $content->getVersionInfo()->versionNo,
                'siteAccessName' => $siteAccessName,
                'locationId' => $location !== null ? $location->id : null,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $view->addParameters([
            'creator' => $creator,
            'siteaccesses' => $siteAccessList,
            'siteaccess' => $siteAccessName,
            'preview_url' => $previewUrl,
            'display_url' => $this->getDisplayUrl(
                $isContentWithoutLocation ? null : $location,
                $content,
                $versionInfo,
                $language,
                $siteAccessName
            ),
            'preview_mode_url' => $previewModeUrl,
            'landing_page_config' => [
                'blocks' => $this->blockDefinitionsProvider->getConfig($contentType),
                'layouts' => $this->layoutDefinitionsProvider->getConfig($contentType),
            ],
            'page' => $this->converter->encode($pageFieldValue->getPage()),
            'timeline_events' => $this->getSerializedTimelineEvents($context),
            'page_field_identifier' => $pageFieldIdentifier,
            'block_configuration_request_form' => $this->getBlockConfigurationRequestForm(
                $location,
                $content->getVersionInfo(),
                $pageFieldValue->getPage(),
                $view->getLanguage()
            )->createView(),
            'reference_timestamp' => $referenceDate->getTimestamp(),
            'editor_mode' => $this->getEditorMode($contentType),
        ]);

        return $view;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo|null $versionInfo
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page|null $page
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function getBlockConfigurationRequestForm(
        Location $location = null,
        VersionInfo $versionInfo = null,
        Page $page = null,
        Language $language = null
    ): FormInterface {
        if ($language === null) {
            $language = $this->languageService->loadLanguage($versionInfo->initialLanguageCode);
        }

        // @todo should pass $contentInfo in case of content create
        $data = new RequestBlockConfiguration(
            $location,
            $versionInfo,
            null !== $location
                ? $location->getContentInfo()
                : null,
            $page,
            null,
            $language
        );

        return $this->createForm(RequestBlockConfigurationType::class, $data, [
            'action' => $this->generateUrl('ibexa.page_builder.block.request_configuration_form'),
            'method' => Request::METHOD_POST,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Ibexa\ContentForms\Content\View\ContentCreateView $view
     *
     * @return \Ibexa\ContentForms\Content\View\ContentCreateView
     *
     * @throws \Exception
     */
    public function createAction(Request $request, ContentCreateView $view): ContentCreateView
    {
        $contentType = $view->getContentType();

        $siteAccessList = $this
            ->siteAccessResolver
            ->getSiteAccessesListForLocation(
                $view->getLocation(),
                null,
                $view->getLanguage()->languageCode
            );

        $siteaccessName = $this->siteAccessPicker->selectSiteAccess(
            new Context(
                $view->getLanguage(),
                null,
                $view->getLocation()
            ),
            array_column($siteAccessList, 'name')
        );

        $pageFieldIdentifier = $this->getPageFieldIdentifier($contentType);
        $pageValue = $contentType->getFieldDefinition($pageFieldIdentifier)->defaultValue;
        $referenceDate = $this->resolveReferenceDate($request);

        $view->addParameters([
            'content_type' => $contentType,
            'siteaccesses' => $siteAccessList,
            'siteaccess' => $siteaccessName,
            'page_field_identifier' => $pageFieldIdentifier,
            'field_value_json' => $this->converter->encode($pageValue->getPage()),
            'landing_page_config' => [
                'blocks' => $this->blockDefinitionsProvider->getConfig($contentType),
                'layouts' => $this->layoutDefinitionsProvider->getConfig($contentType),
            ],
            'block_configuration_request_form' => $this->getBlockConfigurationRequestForm(null, null, null, $view->getLanguage())->createView(),
            'reference_timestamp' => $referenceDate->getTimestamp(),
            'editor_mode' => $this->getEditorMode($contentType),
        ]);

        return $view;
    }

    /**
     * @deprecated Deprecated since Ibexa DXP 4.5.0.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $locationId
     * @param string|null $siteaccessName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createDraftAction(
        Request $request,
        int $locationId,
        ?string $requestSiteaccess = null
    ): Response {
        @trigger_error(
            sprintf(
                'The "%s" method is deprecated since Ibexa DXP 4.5.0.',
                __METHOD__,
            ),
            E_USER_DEPRECATED
        );

        /* @todo it shouldn't rely on keys from request */
        $requestKeys = $request->request->keys();
        $formName = reset($requestKeys) ?: null;

        $form = $this->formFactory->contentEdit(null, $formName);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ContentEditData $data) use ($locationId, $requestSiteaccess): ?Response {
                    $location = $data->getLocation();
                    $contentInfo = $location->getContentInfo();
                    $versionInfo = $data->getVersionInfo() ?? $location->getContent()->getVersionInfo();
                    $language = $data->getLanguage();
                    $versionNo = $versionInfo->versionNo;

                    if (!$versionInfo->isDraft()) {
                        $contentDraft = $this->contentService->createContentDraft($contentInfo, $versionInfo, null, $language);
                        $versionNo = $contentDraft->getVersionInfo()->versionNo;

                        $this->notificationHandler->success(
                            /** @Desc("Created new draft version of '%name%'.") */
                            'content.create_draft.success',
                            [
                                '%name%' => $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo),
                            ],
                            'ibexa_page_builder_edit'
                        );
                    }

                    $siteaccess = $this->siteaccessService->resolveSiteAccessForLocation(
                        $language,
                        $location,
                        $requestSiteaccess
                    )[0];

                    return $this->redirectToRoute('ibexa.content.draft.edit', [
                        'contentId' => $contentInfo->id,
                        'versionNo' => $versionNo,
                        'language' => $language->languageCode,
                        'locationId' => $locationId,
                        'siteaccessName' => $siteaccess,
                    ]);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        /** @var \Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData $data */
        $data = $form->getData();
        $contentInfo = $data->getContentInfo();

        if (null !== $contentInfo) {
            return $this->redirectToRoute('ibexa.content.view', [
                'contentId' => $contentInfo->id,
                'locationId' => $contentInfo->mainLocationId,
            ]);
        }

        return $this->redirectToRoute('ibexa.dashboard');
    }

    /**
     * @param \Ibexa\AdminUi\View\ContentTranslateView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Ibexa\AdminUi\View\ContentTranslateView
     *
     * @throws \Exception
     */
    public function translateAction(ContentTranslateView $view, Request $request): ContentTranslateView
    {
        $contentType = $view->getContentType();

        $content = $view->getContent();
        $location = $view->getLocation();
        $language = $view->getLanguage();
        $baseLanguage = $view->getBaseLanguage();
        $contentInfo = $content->contentInfo;
        $pageFieldIdentifier = $this->getPageFieldIdentifier($contentType);

        $pageFieldValue = null !== $baseLanguage
            ? $content->getField($pageFieldIdentifier, $baseLanguage->languageCode)->value
            : $contentType->getFieldDefinition($pageFieldIdentifier)->defaultValue;

        $siteAccessList = $this
            ->siteAccessResolver
            ->getSiteAccessesListForLocation(
                $location,
                null,
                $language->languageCode
            );

        $siteaccess = $this->siteAccessPicker->selectSiteAccess(
            new Context(
                $language,
                $content,
                $location,
            ),
            array_column($siteAccessList, 'name')
        );

        $previewUrl = null === $baseLanguage
            ? $this->generateUrl(
                'ibexa.page_builder.layout',
                [
                    'layoutId' => $pageFieldValue->getPage()->getLayout(),
                    'siteaccessName' => $siteaccess,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            : $this->generateContentPreviewUrl(
                $content,
                [
                    'contentId' => $content->id,
                    'language' => $baseLanguage->languageCode,
                    'versionNo' => $contentInfo->currentVersionNo,
                    'siteAccessName' => $siteaccess,
                    'locationId' => $location === null ? null : $location->id,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        $displayUrl = null !== $location
            ? $this->getDisplayUrl($location, $content, $content->versionInfo, $view->getLanguage(), $siteaccess)
            : $previewUrl;
        $context = $this->createTranslateContext(
            $location,
            $content,
            $language,
            $baseLanguage,
            $pageFieldValue->getPage()
        );
        $referenceDate = $this->resolveReferenceDate($request);

        $view->addParameters([
            'preview_url' => $previewUrl,
            'display_url' => $displayUrl,
            'preview_mode_url' => $this->generateUrl(
                'ibexa.content.view',
                [
                    'locationId' => $location->id,
                    'contentId' => $location->contentId,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'block_configuration_request_form' => $this->getBlockConfigurationRequestForm(
                $location,
                $content->getVersionInfo(),
                $pageFieldValue->getPage(),
                $language
            )->createView(),
            'landing_page_config' => [
                'blocks' => $this->blockDefinitionsProvider->getConfig($contentType),
                'layouts' => $this->layoutDefinitionsProvider->getConfig($contentType),
            ],
            'page_field_identifier' => $pageFieldIdentifier,
            'page' => $this->converter->encode($pageFieldValue->getPage()),
            'timeline_events' => $this->getSerializedTimelineEvents($context),
            'siteaccess_name' => $siteaccess,
            'siteaccess' => $siteaccess,
            'siteaccesses' => $siteAccessList,
            'reference_timestamp' => $referenceDate->getTimestamp(),
            'editor_mode' => $this->getEditorMode($contentType),
        ]);

        return $view;
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface $context
     *
     * @return string
     */
    private function getSerializedTimelineEvents(ContextInterface $context): string
    {
        return $this->serializer->serialize($this->timelineEventsCollector->collect($context), 'json');
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageEditContext
     */
    private function createEditContext(
        ?Location $location,
        Content $content,
        VersionInfo $versionInfo,
        Language $language,
        Page $page
    ): PageEditContext {
        return new PageEditContext($location, $content, $versionInfo, $language->languageCode, $page);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $baseLanguage
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageTranslateContext
     */
    private function createTranslateContext(
        ?Location $location,
        Content $content,
        Language $language,
        ?Language $baseLanguage,
        Page $page
    ): PageTranslateContext {
        return new PageTranslateContext(
            $location,
            $content,
            $language->languageCode,
            $baseLanguage ? $baseLanguage->languageCode : null,
            $page
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \DateTimeImmutable
     *
     * @throws \Exception
     */
    private function resolveReferenceDate(Request $request): DateTimeInterface
    {
        $referenceTimestamp = $request->query->get('reference_timestamp');

        if (null === $referenceTimestamp) {
            return new DateTimeImmutable();
        }

        return DateTimeImmutable::createFromFormat('U', $referenceTimestamp);
    }

    /**
     * Generate URL to content preview in Page Builder.
     *
     * @param array<mixed> $parameters
     *
     * @see \Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait::generateUrl
     */
    private function generateContentPreviewUrl(
        Content $content,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $routeName = 'ibexa.page_builder.content.preview';
        if ($this->tokenAuthenticator instanceof TokenAuthenticator) {
            $routeName = 'page_builder_pre_auth_content_preview';
        }
        $event = new GenerateContentPreviewUrlEvent($content, $routeName, $parameters, $referenceType);
        $this->eventDispatcher->dispatch($event, GenerateContentPreviewUrlEvent::NAME);

        return $this->generateUrl($event->getRouteName(), $event->getParameters(), $event->getReferenceType());
    }

    private function getEditorMode(ContentType $contentType): string
    {
        if (!$contentType->hasFieldDefinitionOfType('ezlandingpage')) {
            return LandingPageFormMapper::PAGE_VIEW_MODE;
        }

        $fieldDefinitionSettings = $contentType->getFirstFieldDefinitionOfType('ezlandingpage')->fieldSettings;

        return $fieldDefinitionSettings['editorMode'] ?? LandingPageFormMapper::PAGE_VIEW_MODE;
    }

    private function markPageBuilderVisitedForCurrentUser(): void
    {
        try {
            $this->userPreferenceService->getUserPreference(self::PAGE_BUILDER_VISITED_PREFERENCE);
        } catch (NotFoundException $e) {
            $struct = new UserPreferenceSetStruct();
            $struct->name = self::PAGE_BUILDER_VISITED_PREFERENCE;
            $struct->value = 'true';

            $this->userPreferenceService->setUserPreference([$struct]);
        } catch (UnauthorizedException $e) {
            //do nothing
        }
    }
}

class_alias(PageController::class, 'EzSystems\EzPlatformPageBuilderBundle\Controller\PageController');
