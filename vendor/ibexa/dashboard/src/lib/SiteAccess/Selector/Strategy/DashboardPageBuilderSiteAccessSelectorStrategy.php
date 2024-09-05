<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\SiteAccess\Selector\Strategy;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SelectorStrategy;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;

/**
 * @internal
 */
final class DashboardPageBuilderSiteAccessSelectorStrategy implements SelectorStrategy
{
    private SiteAccessServiceInterface $siteAccessService;

    /** @var array<string, string[]> */
    private array $siteAccessGroups;

    private ConfigResolverInterface $configResolver;

    /**
     * @param array<string, string[]> $siteAccessGroups
     */
    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigResolverInterface $configResolver,
        array $siteAccessGroups
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->configResolver = $configResolver;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function selectSiteAccess(Context $context, array $siteAccessNameList): ?string
    {
        $content = $context->content;
        $currentSiteAccess = $this->siteAccessService->getCurrent();
        if (
            $content === null || $currentSiteAccess === null
            || !(new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($currentSiteAccess)
            || !(new IsDashboardContentType($this->configResolver))->isSatisfiedBy($content->getContentType())
        ) {
            return null;
        }

        return in_array($currentSiteAccess->name, $siteAccessNameList, true)
            ? $currentSiteAccess->name
            : null;
    }
}
