<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Twig;

use Ibexa\Bundle\SiteContext\UserSettings\LocationPreview;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\SiteContext\Specification\IsContextAware;
use Ibexa\User\UserSetting\UserSettingService;

final class SiteContextRuntime
{
    private SiteContextServiceInterface $siteAccessService;

    private UserSettingService $userService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SiteContextServiceInterface $siteAccessService,
        UserSettingService $userService,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->userService = $userService;
        $this->configResolver = $configResolver;
    }

    public function getCurrentContext(): ?SiteAccess
    {
        return $this->siteAccessService->getCurrentContext();
    }

    public function isLocationPreviewEnabled(): bool
    {
        return $this->userService->getUserSetting(LocationPreview::IDENTIFIER)->value === LocationPreview::ENABLED_OPTION;
    }

    public function isLocationContextAware(Location $location): bool
    {
        return IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location);
    }
}
