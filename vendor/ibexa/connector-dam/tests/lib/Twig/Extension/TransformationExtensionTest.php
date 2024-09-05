<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\Twig\Extension;

use Ibexa\Connector\Dam\Twig\Extension\TransformationExtension;
use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;
use Ibexa\Contracts\Connector\Dam\Variation\TransformationFactory;
use PHPUnit\Framework\TestCase;

class TransformationExtensionTest extends TestCase
{
    /**
     * @dataProvider transformationProvider
     */
    public function testGetTransformationByName(Transformation $transformation): void
    {
        $source = new AssetSource('test_source');
        $factory = $this->createMock(TransformationFactory::class);
        $factory
            ->method('build')
            ->with($transformation->getName(), $transformation->getTransformationProperties())
            ->willReturn($transformation);

        $factoryRegistry = $this->createMock(TransformationFactoryRegistry::class);
        $factoryRegistry
            ->method('getFactory')
            ->with($source)
            ->willReturn($factory);

        $extension = new TransformationExtension($factoryRegistry);

        $result = $extension->getTransformation(
            $source->getSourceIdentifier(),
            $transformation->getName(),
            $transformation->getTransformationProperties()
        );

        $this->assertInstanceOf(
            Transformation::class,
            $result
        );

        $this->assertEquals(
            $transformation,
            $result
        );
    }

    public function transformationProvider()
    {
        return [
            [
                new Transformation('test_name', ['param_1' => 'value_1']),
            ],
            [
                new Transformation(null, ['param_1' => 'value_1']),
            ],
        ];
    }
}

class_alias(TransformationExtensionTest::class, 'Ibexa\Platform\Tests\Connector\Dam\Twig\Extension\TransformationExtensionTest');
