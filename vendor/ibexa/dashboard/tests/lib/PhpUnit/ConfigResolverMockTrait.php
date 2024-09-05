<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\PhpUnit;

use Ibexa\Bundle\Dashboard\DependencyInjection\Parser\Dashboard;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
trait ConfigResolverMockTrait
{
    protected function mockConfigResolver(): ConfigResolverInterface
    {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->with(IsDashboardContentType::CONTENT_TYPE_IDENTIFIER_PARAM_NAME)
            ->willReturn(Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER)
        ;

        return $configResolverMock;
    }

    /**
     * @param array<int, mixed> $map
     */
    protected function mockConfigResolverWithMap(array $map): ConfigResolverInterface
    {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->willReturnMap($map)
        ;

        return $configResolverMock;
    }
}
