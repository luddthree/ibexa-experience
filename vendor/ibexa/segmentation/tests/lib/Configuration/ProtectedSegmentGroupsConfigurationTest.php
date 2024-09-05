<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Configuration;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Segmentation\Configuration\ProtectedSegmentGroupsConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Configuration\ProtectedSegmentGroupsConfiguration
 */
final class ProtectedSegmentGroupsConfigurationTest extends TestCase
{
    public function testGetProtectedGroupIdentifiers(): void
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->with('segmentation.segment_groups.list')
            ->willReturn([
                'example_1' => ['protected' => true],
                'example_2' => ['protected' => false],
                'example_3' => ['protected' => true],
            ]);

        $protectedSegmentGroupsConfiguration = new ProtectedSegmentGroupsConfiguration($configResolver);

        $protectedSegmentGroupIdentifiers = $protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

        self::assertCount(2, $protectedSegmentGroupIdentifiers);
        self::assertSame('example_1', $protectedSegmentGroupIdentifiers[0]);
        self::assertSame('example_3', $protectedSegmentGroupIdentifiers[1]);
    }
}
