<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Fastly\ImageOptimizer\PathGenerator;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Fastly\ImageOptimizer\PathGenerator\RawPathGenerator;
use PHPUnit\Framework\TestCase;

final class RawPathGeneratorTest extends TestCase
{
    public function testRawPathGenerator(): void
    {
        $configResolver = $this->getConfigResolverMock();
        $pathGenerator = new RawPathGenerator($configResolver);

        self::assertSame(
            'https://somewhere.com/example_file.png?width=123&height=456&fit=bounds&enable=upscale',
            $pathGenerator->getVariationPath('https://somewhere.com/example_file.png', 'some_variation')
        );
    }

    public function testRawPathGeneratorMissingVariationFallback(): void
    {
        $configResolver = $this->getConfigResolverMock();
        $pathGenerator = new RawPathGenerator($configResolver);

        self::assertSame(
            'https://somewhere.com/example_file.png',
            $pathGenerator->getVariationPath('https://somewhere.com/example_file.png', 'other_variation')
        );
    }

    private function getConfigResolverMock(): ConfigResolverInterface
    {
        $mock = self::createMock(ConfigResolverInterface::class);
        $mock
            ->method('getParameter')
            ->willReturn([
                'some_variation' => [
                    'reference' => 'original',
                    'configuration' => [
                        'width' => 123,
                        'height' => 456,
                        'fit' => 'bounds',
                        'enable' => 'upscale',
                    ],
                ],
            ]);

        return $mock;
    }
}
