<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Controller;

use Ibexa\AdminUi\Form\Data\Content\Draft\ContentCreateData;
use Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData;
use Ibexa\AdminUi\Form\Data\User\UserEditData;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\ContentEditTranslationChoiceLoader;
use Ibexa\AdminUi\Permission\LookupLimitationsTransformer;
use Ibexa\AdminUi\Specification\ContentIsUser;
use Ibexa\Bundle\SiteContext\UI\Tabs\PreviewTab;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Traversable;

final class FullscreenPreviewController extends Controller
{
    private FormFactory $formFactory;

    private LanguageService $languageService;

    private PermissionResolver $permissionResolver;

    private LookupLimitationsTransformer $limitationsTransformer;

    private LocationService $locationService;

    private SiteContextServiceInterface $siteContextService;

    private UserService $userService;

    public function __construct(
        FormFactory $formFactory,
        LanguageService $languageService,
        PermissionResolver $permissionResolver,
        LookupLimitationsTransformer $limitationsTransformer,
        LocationService $locationService,
        SiteContextServiceInterface $siteContextService,
        UserService $userService
    ) {
        $this->formFactory = $formFactory;
        $this->languageService = $languageService;
        $this->permissionResolver = $permissionResolver;
        $this->limitationsTransformer = $limitationsTransformer;
        $this->locationService = $locationService;
        $this->siteContextService = $siteContextService;
        $this->userService = $userService;
    }

    public function locationViewAction(Request $request, ContentView $view): ContentView
    {
        $location = $view->getLocation();
        $content = $view->getContent();
        $versionInfo = $content->getVersionInfo();

        $contentCreateType = $this->formFactory->createContent(
            $this->getContentCreateData($location)
        );

        $contentEditType = $this->createContentEditForm(
            $content->contentInfo,
            $versionInfo,
            null,
            $location
        );

        $view->addParameters([
            'form_content_create' => $contentCreateType->createView(),
            'form_content_edit' => $contentEditType->createView(),
            'siteaccess' => $this->siteContextService->getCurrentContext()->name ?? '',
        ]);

        if ((new ContentIsUser($this->userService))->isSatisfiedBy($content)) {
            $userEditType = $this->formFactory->editUser(
                new UserEditData($content->contentInfo, $versionInfo, null, $location)
            );

            $view->addParameters([
                'form_user_edit' => $userEditType->createView(),
            ]);
        }

        return $view;
    }

    public function toggleFullscreenAction(Request $request, int $locationId): Response
    {
        $location = $this->locationService->loadLocation($locationId);

        $isEnabled = $this->siteContextService->isFullscreenModeEnabled();
        $this->siteContextService->setFullscreenMode(!$isEnabled);
        $uriFragment = $isEnabled ? PreviewTab::URI_FRAGMENT : '';

        return $this->redirectToLocation($location, $uriFragment);
    }

    private function getContentCreateData(?Location $location): ContentCreateData
    {
        $languages = $this->languageService->loadLanguages();
        if ($languages instanceof Traversable) {
            $languages = iterator_to_array($languages);
        }

        $language = count($languages) === 1
            ? array_shift($languages)
            : null;

        return new ContentCreateData(null, $location, $language);
    }

    private function createContentEditForm(
        ?ContentInfo $contentInfo = null,
        ?VersionInfo $versionInfo = null,
        ?Language $language = null,
        ?Location $location = null
    ): FormInterface {
        $languageCodes = $versionInfo->languageCodes ?? [];

        return $this->formFactory->contentEdit(
            new ContentEditData($contentInfo, null, $language, $location),
            null,
            [
                'choice_loader' => new ContentEditTranslationChoiceLoader(
                    $this->languageService,
                    $this->permissionResolver,
                    $contentInfo,
                    $this->limitationsTransformer,
                    $languageCodes,
                    $this->locationService,
                    $location
                ),
            ]
        );
    }
}
