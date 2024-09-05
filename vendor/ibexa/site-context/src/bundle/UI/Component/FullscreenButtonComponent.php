<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\UI\Component;

use Ibexa\AdminUi\Specification\UserMode\IsFocusModeEnabled;
use Ibexa\AdminUi\UserSetting\FocusMode;
use Ibexa\Bundle\SiteContext\Specification\IsAdmin;
use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\SiteContext\Specification\IsContextAware;
use Ibexa\User\UserSetting\UserSettingService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class FullscreenButtonComponent implements Renderable
{
    private Environment $twig;

    private RequestStack $requestStack;

    private ConfigResolverInterface $configResolver;

    private SiteAccessServiceInterface $siteAccessService;

    private UserSettingService $userSettingService;

    public function __construct(
        Environment $twig,
        RequestStack $requestStack,
        ConfigResolverInterface $configResolver,
        SiteAccessServiceInterface $siteAccessService,
        UserSettingService $userSettingService
    ) {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->configResolver = $configResolver;
        $this->siteAccessService = $siteAccessService;
        $this->userSettingService = $userSettingService;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return '';
        }

        $view = $request->attributes->get('view');
        if (!$view instanceof ContentView || !$this->isAdminSiteAccess()) {
            return '';
        }

        if ($this->isFocusModeDisabled()) {
            return '';
        }

        $location = $view->getLocation();
        if (!$location instanceof Location || !$this->isContextAware($location)) {
            return '';
        }

        return $this->twig->render(
            '@ibexadesign/site_context/fullscreen_button.html.twig',
            [
                'location' => $location,
            ]
        );
    }

    private function isAdminSiteAccess(): bool
    {
        $siteAccess = $this->siteAccessService->getCurrent();
        if ($siteAccess === null) {
            return false;
        }

        return (new IsAdmin())->isSatisfiedBy($siteAccess);
    }

    private function isContextAware(Location $location): bool
    {
        return IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location);
    }

    private function isFocusModeDisabled(): bool
    {
        return IsFocusModeEnabled::fromUserSettings($this->userSettingService)->isSatisfiedBy(FocusMode::FOCUS_MODE_OFF);
    }
}
