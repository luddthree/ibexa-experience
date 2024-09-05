<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteContext\Matcher;

use Ibexa\AdminUi\Specification\UserMode\IsFocusModeEnabled;
use Ibexa\AdminUi\UserSetting\FocusMode;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\SiteContext\Specification\IsContextAware;
use Ibexa\User\UserSetting\UserSettingService;

/**
 * @internal
 */
final class IsFullscreen implements ViewMatcherInterface
{
    private bool $isFullscreen = true;

    private UserSettingService $userSettingService;

    private ConfigResolverInterface $configResolver;

    private SiteContextServiceInterface $siteContextService;

    public function __construct(
        UserSettingService $userSettingService,
        ConfigResolverInterface $configResolver,
        SiteContextServiceInterface $siteContextService
    ) {
        $this->userSettingService = $userSettingService;
        $this->configResolver = $configResolver;
        $this->siteContextService = $siteContextService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function setMatchingConfig($matchingConfig): void
    {
        if (!is_bool($matchingConfig)) {
            throw new InvalidArgumentException(
                '$matchConfig',
                sprintf(
                    '%s matcher expects boolean value, got a value of %s type',
                    self::class,
                    get_debug_type($matchingConfig)
                )
            );
        }

        $this->isFullscreen = $matchingConfig;
    }

    public function match(View $view): bool
    {
        $parameters = $view->getParameters();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $parameters['location'];

        $isFullscreen = $this->isFocusModeEnabled()
            && IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location)
            && $this->siteContextService->getCurrentContext() !== null
            && $this->siteContextService->isFullscreenModeEnabled();

        return $this->isFullscreen === $isFullscreen;
    }

    private function isFocusModeEnabled(): bool
    {
        return IsFocusModeEnabled
            ::fromUserSettings($this->userSettingService)
            ->isSatisfiedBy(FocusMode::FOCUS_MODE_ON);
    }
}
