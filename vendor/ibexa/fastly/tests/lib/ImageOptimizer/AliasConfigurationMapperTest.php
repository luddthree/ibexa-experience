<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Fastly\ImageOptimizer;

use Ibexa\Bundle\Core\Imagine\Filter\Loader\BorderFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\CropFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleDownOnlyFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleExactFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleHeightDownOnlyFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleHeightFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScalePercentFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleWidthDownOnlyFilterLoader;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleWidthFilterLoader;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Fastly\ImageOptimizer\AliasConfigurationMapper;
use PHPUnit\Framework\TestCase;

final class AliasConfigurationMapperTest extends TestCase
{
    public function testAliasConfigurationMapper(): void
    {
        $configResolver = $this->getConfigResolverMock();
        $configurationMapper = new AliasConfigurationMapper($configResolver);

        self::assertSame(
            [
                'reference' => 'original',
                'configuration' => [
                    'width' => 123,
                    'height' => 456,
                    'fit' => 'cover',
                    'enable' => 'upscale',
                ],
            ],
            $configurationMapper->map('some_variation')
        );

        self::assertSame(
            [
                'reference' => 'some_variation',
                'configuration' => [
                    'crop' => '234,345,x12,y34',
                ],
            ],
            $configurationMapper->map('other_variation')
        );
    }

    public function testAliasConfigurationMapperConfiguration(): void
    {
        $configResolver = $this->getConfigResolverMock();
        $configurationMapper = new AliasConfigurationMapper($configResolver);

        self::assertSame(
            [
                'reference' => 'original',
                'configuration' => [
                    'width' => 123,
                    'height' => 456,
                    'fit' => 'cover',
                    'enable' => 'upscale',
                ],
            ],
            $configurationMapper->mapConfiguration([
                'reference' => null,
                'filters' => [
                    'geometry/scale' => [
                        123,
                        456,
                    ],
                ],
            ])
        );

        self::assertSame(
            [
                'reference' => 'some_variation',
                'configuration' => [
                    'crop' => '234,345,x12,y34',
                ],
            ],
            $configurationMapper->mapConfiguration([
                'reference' => 'some_variation',
                'filters' => [
                    'geometry/crop' => [
                        234,
                        345,
                        12,
                        34,
                    ],
                ],
            ])
        );
    }

    /**
     * @dataProvider getFilterConfigurations
     *
     * @param array<string, array{array<string, int|string>, string, array<int|string>}> $expect
     */
    public function testAliasConfigurationFilter(
        array $expect,
        string $filter,
        array $parameters = []
    ): void {
        $configResolver = $this->getConfigResolverMock();
        $configurationMapper = new AliasConfigurationMapper($configResolver);

        self::assertSame(
            $expect,
            $configurationMapper->mapFilter($filter, $parameters)
        );
    }

    public function testAliasConfigurationUnsupportedFilter(): void
    {
        $configResolver = $this->getConfigResolverMock();
        $configurationMapper = new AliasConfigurationMapper($configResolver);

        self::expectExceptionObject(
            new InvalidArgumentException(
                'filter',
                'Filter "test/unupported" is not compatible with Fastly Image Optimizer.'
            )
        );

        $configurationMapper->mapFilter('test/unupported', [1, 2, 3]);
    }

    /**
     * @return array<string, array{array<string, string|int>, string, array<int|string>}>
     */
    public static function getFilterConfigurations(): array
    {
        return [
            'test geometry/scaledownonly' => [
                ['width' => 123, 'height' => 456, 'fit' => 'bounds'],
                ScaleDownOnlyFilterLoader::IDENTIFIER,
                [123, 456],
            ],
            'test geometry/scalewidthdownolny' => [
                ['width' => 123, 'height' => '100p', 'fit' => 'bounds'],
                ScaleWidthDownOnlyFilterLoader::IDENTIFIER,
                [123],
            ],
            'test geometry/scaleheightdownolny' => [
                ['width' => '100p', 'height' => 456, 'fit' => 'bounds'],
                ScaleHeightDownOnlyFilterLoader::IDENTIFIER,
                [456],
            ],
            'test geometry/scale' => [
                ['width' => 123, 'height' => 456, 'fit' => 'cover', 'enable' => 'upscale'],
                ScaleFilterLoader::IDENTIFIER,
                [123, 456],
            ],
            'test geometry/scalewidth' => [
                ['width' => 123, 'enable' => 'upscale'],
                ScaleWidthFilterLoader::IDENTIFIER,
                [123],
            ],
            'test geometry/scaleheight' => [
                ['height' => 456, 'enable' => 'upscale'],
                ScaleHeightFilterLoader::IDENTIFIER,
                [456],
            ],
            'test geometry/scaleexact' => [
                ['width' => 123, 'height' => 456, 'enable' => 'upscale'],
                ScaleExactFilterLoader::IDENTIFIER,
                [123, 456],
            ],
            'test resize' => [
                ['width' => 123, 'height' => 456, 'enable' => 'upscale'],
                'resize',
                [123, 456],
            ],
            'test geometry/scalepercent' => [
                ['width' => '150p', 'height' => '300p', 'enable' => 'upscale'],
                ScalePercentFilterLoader::IDENTIFIER,
                [150, 300],
            ],
            'test geometry/crop' => [
                ['crop' => '123,456,x12,y34'],
                CropFilterLoader::IDENTIFIER,
                [123, 456, 12, 34],
            ],
            'test border with background color' => [
                ['pad' => '123,456', 'bg-color' => 'c3c3c3'],
                BorderFilterLoader::IDENTIFIER,
                [123, 456, '#c3c3c3'],
            ],
            'test border without background color' => [
                ['pad' => '123,456', 'bg-color' => '000'],
                BorderFilterLoader::IDENTIFIER,
                [123, 456],
            ],
        ];
    }

    private function getConfigResolverMock(): ConfigResolverInterface
    {
        $mock = self::createMock(ConfigResolverInterface::class);
        $mock
            ->method('getParameter')
            ->willReturn([
                'some_variation' => [
                    'reference' => null,
                    'filters' => [
                        'geometry/scale' => [
                            123,
                            456,
                        ],
                    ],
                ],
                'other_variation' => [
                    'reference' => 'some_variation',
                    'filters' => [
                        'geometry/crop' => [
                            234,
                            345,
                            12,
                            34,
                        ],
                    ],
                ],
            ]);

        return $mock;
    }
}
