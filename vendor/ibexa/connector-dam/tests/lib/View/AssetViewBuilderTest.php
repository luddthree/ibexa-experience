<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\View;

use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Connector\Dam\View\AssetViewBuilder;
use Ibexa\Connector\Dam\View\ForwardView;
use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetMetadata;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\AssetUri;
use Ibexa\Contracts\Connector\Dam\View\AssetView;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use PHPUnit\Framework\TestCase;

class AssetViewBuilderTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\View\Configurator|\PHPUnit\Framework\MockObject\MockObject */
    private $viewConfigurator;

    /** @var \Ibexa\Core\MVC\Symfony\View\ParametersInjector|\PHPUnit\Framework\MockObject\MockObject */
    private $parametersInjector;

    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $transformationFactoryRegistry;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetService|\PHPUnit\Framework\MockObject\MockObject */
    private $assetService;

    /** @var \Ibexa\Connector\Dam\View\AssetViewBuilder */
    private $assetViewBuilder;

    protected function setUp(): void
    {
        $this->assetService = $this->createMock(AssetService::class);
        $this->transformationFactoryRegistry = $this->createMock(TransformationFactoryRegistry::class);
        $this->viewConfigurator = $this->createMock(Configurator::class);
        $this->parametersInjector = $this->createMock(ParametersInjector::class);

        $this->assetViewBuilder = new AssetViewBuilder(
            $this->assetService,
            $this->transformationFactoryRegistry,
            $this->viewConfigurator,
            $this->parametersInjector,
        );
    }

    public function testBuildWithSource()
    {
        $this->assetService
            ->method('get')
            ->with(
                new AssetIdentifier('test_id'),
                new AssetSource('test_source')
            )
            ->willReturn(
                new Asset(
                    new AssetIdentifier('test_id'),
                    new AssetSource('test_source'),
                    new AssetUri(''),
                    new AssetMetadata([])
                )
            );

        $view = $this->assetViewBuilder->buildView([
            'assetSource' => 'test_source',
            'destinationContentId' => 'test_id',
        ]);

        $this->assertInstanceOf(
            AssetView::class,
            $view
        );

        $this->assertInstanceOf(
            Asset::class,
            $view->getParameters()['asset']
        );
    }

    public function testBuildWithoutSource()
    {
        $this->assetService
            ->expects($this->never())
            ->method('get');

        $view = $this->assetViewBuilder->buildView([
            'destinationContentId' => 'test_id',
        ]);

        $this->assertInstanceOf(
            ForwardView::class,
            $view
        );

        $this->assertEquals(
            'test_id',
            $view->getParameters()['contentId']
        );
    }

    public function testBuildWithNullSource()
    {
        $this->assetService
            ->expects($this->never())
            ->method('get');

        $view = $this->assetViewBuilder->buildView([
            'destinationContentId' => 'test_id',
            'assetSource' => null,
        ]);

        $this->assertInstanceOf(
            ForwardView::class,
            $view
        );

        $this->assertEquals(
            'test_id',
            $view->getParameters()['contentId']
        );
    }
}

class_alias(AssetViewBuilderTest::class, 'Ibexa\Platform\Tests\Connector\Dam\View\AssetViewBuilderTest');
