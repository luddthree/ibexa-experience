<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\UI;

use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;

final class ConfigProvider implements ProviderInterface
{
    private SiteContextServiceInterface $siteAccessService;

    private ConfigResolverInterface $configResolver;

    public function __construct(SiteContextServiceInterface $siteAccessService, ConfigResolverInterface $configResolver)
    {
        $this->siteAccessService = $siteAccessService;
        $this->configResolver = $configResolver;
    }

    /**
     * @return array{ current: string|null }
     */
    public function getConfig(): array
    {
        $context = $this->siteAccessService->getCurrentContext();

        return [
            'current' => $context !== null ? $context->name : null,
            'excludedPaths' => $this->configResolver->getParameter('site_context.excluded_paths'),
        ];
    }
}
