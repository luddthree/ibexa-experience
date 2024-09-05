<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Fastly\ImageOptimizer;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Contracts\Core\Variation\VariationHandler as VariationHandlerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\Image\Value;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\Fastly\ImageOptimizer\PathGenerator\RawPathGenerator;
use Ibexa\Fastly\ImageOptimizer\VariationHandler;
use Ibexa\Fastly\ImageOptimizer\VariationResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;

final class VariationHandlerTest extends TestCase
{
    public function testVariationHandler(): void
    {
        $variationHandler = new VariationHandler(
            new VariationResolver(
                $this->getRequestContextMock(),
                new RawPathGenerator(
                    $this->getConfigResolverMock()
                )
            ),
            self::createMock(ContentService::class),
            self::createMock(AssetMapper::class),
            self::createMock(ConfigResolverInterface::class),
            self::createMock(VariationHandlerInterface::class)
        );

        $field = new Field([
            'value' => new Value([
                'uri' => '/some_image.png',
            ]),
        ]);

        $result = $variationHandler->getVariation(
            $field,
            self::createMock(VersionInfo::class),
            'some_variation',
            []
        );

        self::assertEquals(
            new ImageVariation([
                'uri' => 'http://some_domain.com/test_base/some_image.png?width=123&height=456&fit=bounds&enable=upscale',
                'name' => 'some_variation',
                'handler' => 'fastly',
                'isExternal' => true,
            ]),
            $result
        );
    }

    public function testVariationHandlerNoVariationFallback(): void
    {
        $variationHandler = new VariationHandler(
            new VariationResolver(
                $this->getRequestContextMock(),
                new RawPathGenerator(
                    $this->getConfigResolverMock()
                )
            ),
            self::createMock(ContentService::class),
            self::createMock(AssetMapper::class),
            self::createMock(ConfigResolverInterface::class),
            self::createMock(VariationHandlerInterface::class)
        );

        $field = new Field([
            'value' => new Value([
                'uri' => '/some_image.png',
            ]),
        ]);

        $result = $variationHandler->getVariation(
            $field,
            self::createMock(VersionInfo::class),
            'other_variation',
            []
        );

        self::assertEquals(
            new ImageVariation([
                'uri' => 'http://some_domain.com/test_base/some_image.png',
                'name' => 'other_variation',
                'handler' => 'fastly',
                'isExternal' => true,
            ]),
            $result
        );
    }

    public function testVariationHandlerUnsupportedFieldValue(): void
    {
        $variationHandler = new VariationHandler(
            new VariationResolver(
                $this->getRequestContextMock(),
                new RawPathGenerator(
                    $this->getConfigResolverMock()
                )
            ),
            self::createMock(ContentService::class),
            self::createMock(AssetMapper::class),
            self::createMock(ConfigResolverInterface::class),
            self::createMock(VariationHandlerInterface::class)
        );

        $field = new Field([
            'value' => self::createMock(BaseValue::class),
        ]);

        self::expectExceptionObject(
            new InvalidArgumentException(
                '$field',
                'Value of Field with ID 0 () cannot be used for generating an image variation.'
            )
        );

        $variationHandler->getVariation(
            $field,
            self::createMock(VersionInfo::class),
            'other_variation',
            []
        );
    }

    public function testVariationHandlerReferenceVariationCall(): void
    {
        $field = new Field([
            'value' => new Value([
                'uri' => '/some_image.png',
            ]),
        ]);

        $versionInfo = self::createMock(VersionInfo::class);

        $referenceVariationHandler = self::createMock(VariationHandlerInterface::class);
        $referenceVariationHandler
            ->expects(self::once())
            ->method('getVariation')
            ->with($field, $versionInfo, 'reference', [])
            ->willReturn(new ImageVariation([
                'uri' => '/reference_image.png',
            ]));

        $configResolver = $this->getConfigResolverMock();

        $variationHandler = new VariationHandler(
            new VariationResolver(
                $this->getRequestContextMock(),
                new RawPathGenerator(
                    $configResolver,
                )
            ),
            self::createMock(ContentService::class),
            self::createMock(AssetMapper::class),
            $configResolver,
            $referenceVariationHandler
        );

        $result = $variationHandler->getVariation(
            $field,
            self::createMock(VersionInfo::class),
            'reference_variation'
        );

        self::assertEquals(
            new ImageVariation([
                'uri' => 'http://some_domain.com/test_base/reference_image.png?width=456&height=789&fit=bounds&enable=upscale',
                'name' => 'reference_variation',
                'handler' => 'fastly',
                'isExternal' => true,
            ]),
            $result
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
                'reference_variation' => [
                    'reference' => 'reference',
                    'configuration' => [
                        'width' => 456,
                        'height' => 789,
                        'fit' => 'bounds',
                        'enable' => 'upscale',
                    ],
                ],
            ]);

        return $mock;
    }
}
