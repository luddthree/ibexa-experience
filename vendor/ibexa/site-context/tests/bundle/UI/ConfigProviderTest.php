<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\UI;

use Ibexa\Bundle\SiteContext\UI\ConfigProvider;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    private const EXAMPLE_SITE_ACCESS_NAME = 'example';

    public function testGetConfig(): void
    {
        $siteAccess = $this->createMock(SiteAccess::class);
        $siteAccess->name = self::EXAMPLE_SITE_ACCESS_NAME;

        $siteContextService = $this->createMock(SiteContextServiceInterface::class);
        $siteContextService->method('getCurrentContext')->willReturn($siteAccess);

        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $excludedPaths = ['/1/2/', '/4/5/6/'];
        $configResolver->method('getParameter')->with('site_context.excluded_paths')->willReturn($excludedPaths);

        self::assertEquals(
            [
                'current' => self::EXAMPLE_SITE_ACCESS_NAME,
                'excludedPaths' => $excludedPaths,
            ],
            (new ConfigProvider($siteContextService, $configResolver))->getConfig()
        );
    }
}
