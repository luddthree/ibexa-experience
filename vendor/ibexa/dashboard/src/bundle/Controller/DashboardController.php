<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Controller;

use Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData;
use Ibexa\AdminUi\Form\Type\Content\Draft\ContentEditType;
use Ibexa\AdminUi\Specification\Location\HasChildren;
use Ibexa\Bundle\Dashboard\Form\Data\DashboardCustomizeData;
use Ibexa\Bundle\Dashboard\Form\Type\DashboardChangeActiveType;
use Ibexa\Bundle\Dashboard\Form\Type\DashboardCustomizeType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Dashboard\Specification\IsPredefinedDashboard;
use Ibexa\Dashboard\UI\DashboardBanner;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class DashboardController extends Controller
{
    private FormFactoryInterface $formFactory;

    private ConfigResolverInterface $configResolver;

    private PermissionResolver $permissionResolver;

    private UserPreferenceService $userPreferenceService;

    private LocationService $locationService;

    public function __construct(
        FormFactoryInterface $formFactory,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver,
        UserPreferenceService $userPreferenceService,
        LocationService $locationService
    ) {
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
        $this->userPreferenceService = $userPreferenceService;
        $this->locationService = $locationService;
    }

    public function dashboardAction(ContentView $view): ContentView
    {
        $view->setCacheEnabled(false);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $view->getLocation();

        $isPredefinedDashboard = $this->isPredefinedDashboard($location);
        $canCustomizeDashboard = $this->canCustomizeDashboard($location);

        $parameters = [];
        if ($isPredefinedDashboard && $canCustomizeDashboard && !$this->hasCustomizedDashboard()) {
            $parameters['form_dashboard_customize'] = $this->getDashboardCustomizeForm($location)->createView();
        }

        $customizedDashboardLocation = $this->getCustomizedDashboardLocation();
        if ($customizedDashboardLocation !== null) {
            $parameters['form_content_edit'] = $this->createContentEditForm($customizedDashboardLocation)->createView();
        }

        $parameters['is_dashboard_block_hidden'] = $this->isDashboardBannerHidden();

        $parameters['edit_active_dashboard'] = $customizedDashboardLocation !== null
            && $customizedDashboardLocation->id === $location->id;

        $parameters['change_active_dashboard'] = $this->createDashboardChangeActiveType($location)->createView();

        $view->addParameters($parameters);

        return $view;
    }

    private function getDashboardCustomizeForm(Location $location): FormInterface
    {
        return $this->formFactory->createNamed(
            'customize-dashboard',
            DashboardCustomizeType::class,
            new DashboardCustomizeData($location)
        );
    }

    private function createContentEditForm(
        Location $location
    ): FormInterface {
        $versionInfo = $location->getContent()->getVersionInfo();

        return $this->formFactory->createNamed(
            'content_edit',
            ContentEditType::class,
            new ContentEditData(
                $location->getContentInfo(),
                $versionInfo,
                $location->getContentInfo()->getMainLanguage(),
            ),
        );
    }

    private function createDashboardChangeActiveType(Location $location): FormInterface
    {
        return $this->createForm(
            DashboardChangeActiveType::class,
            null,
            [
                'method' => Request::METHOD_POST,
                'current_dashboard' => $location,
            ]
        );
    }

    private function getCustomizedDashboardLocation(): ?Location
    {
        try {
            $userDashboardsRemoteId = $this->userPreferenceService
                ->getUserPreference('user_dashboards')
                ->value;
            $location = $this->locationService->loadLocationByRemoteId($userDashboardsRemoteId);

            if ($this->locationService->getLocationChildCount($location) > 0) {
                $locationList = $this->locationService->loadLocationChildren($location);

                return $locationList->locations[0];
            }
        } catch (NotFoundException|UnauthorizedException $exception) {
            // Do nothing
        }

        return null;
    }

    private function hasCustomizedDashboard(): bool
    {
        $hasCustomizedDashboard = false;
        try {
            $userDashboardsRemoteId = $this->userPreferenceService
                ->getUserPreference('user_dashboards')
                ->value;
            $location = $this->locationService->loadLocationByRemoteId($userDashboardsRemoteId);
            $hasCustomizedDashboard = (new HasChildren($this->locationService))->isSatisfiedBy($location);
        } catch (NotFoundException|UnauthorizedException $exception) {
            // Do nothing
        }

        return $hasCustomizedDashboard;
    }

    private function canCustomizeDashboard(Location $location): bool
    {
        return $this->permissionResolver->canUser(
            'dashboard',
            'customize',
            $location
        );
    }

    private function isPredefinedDashboard(Location $location): bool
    {
        $predefinedContainerRemoteId = $this->configResolver->getParameter(
            'dashboard.predefined_container_remote_id'
        );

        return (new IsPredefinedDashboard($predefinedContainerRemoteId))->isSatisfiedBy($location);
    }

    private function isDashboardBannerHidden(): bool
    {
        try {
            return $this->userPreferenceService->getUserPreference(
                DashboardBanner::USER_PREFERENCE_IDENTIFIER
            )->value === 'true';
        } catch (NotFoundException $e) {
            return false;
        }
    }
}
