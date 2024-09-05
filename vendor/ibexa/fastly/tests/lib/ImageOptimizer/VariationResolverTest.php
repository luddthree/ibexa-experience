<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Fastly\ImageOptimizer;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Fastly\ImageOptimizer\PathGenerator\RawPathGenerator;
use Ibexa\Fastly\ImageOptimizer\VariationResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;

final class VariationResolverTest extends TestCase
{
    public function testVariationResolver(): void
    {
        $variationResolver = new VariationResolver(
            $this->getRequestContextMock(),
            new RawPathGenerator(
                $this->getConfigResolverMock()
            )
        );

        self::assertSame(
            'http://some_domain.com/test_base/some_path.png?width=123&height=456&fit=bounds&enable=upscale',
            $variationResolver->resolve(
                '/some_path.png',
                'some_variation'
            )
        );
    }

    public function testVariationResolverNoVariationFallback(): void
    {
        $variationResolver = new VariationResolver(
            $this->getRequestContextMock(),
            new RawPathGenerator(
                $this->getConfigResolverMock()
            )
        );

        self::assertSame(
            'http://some_domain.com/test_base/some_path.png',
            $variationResolver->resolve(
                '/some_path.png',
                'other_variation'
            )
        );
    }

    private function getRequestContextMock(): RequestContext
    {
        $mock = self::createMock(RequestContext::class);
        $mock
            ->method('getHttpPort')
            ->willReturn(80);

        $mock
            ->method('getScheme')
            ->willReturn('http');

        $mock
            ->method('getBaseUrl')
            ->willReturn('/test_base');

        $mock
            ->method('getHost')
            ->willReturn('some_domain.com');

        return $mock;
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
