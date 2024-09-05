<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View;

use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Ibexa\Core\MVC\Symfony\View\View;

/**
 * @internal
 */
final class AssetViewBuilder implements ViewBuilder
{
    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry */
    private $transformationFactoryRegistry;

    /** @var \Ibexa\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \Ibexa\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    public function __construct(
        AssetService $assetService,
        TransformationFactoryRegistry $transformationFactoryRegistry,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->assetService = $assetService;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->transformationFactoryRegistry = $transformationFactoryRegistry;
    }

    public function matches($argument): bool
    {
        return 'Ibexa\Bundle\Connector\Dam\Controller\AssetViewController::viewAction' === $argument;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     *
     * @return \Ibexa\Connector\Dam\View\AssetView|\Ibexa\Connector\Dam\View\ForwardView|\Ibexa\Core\MVC\Symfony\View\View
     */
    public function buildView(array $parameters): View
    {
        $view = new AssetView();

        if (!isset($parameters['assetSource']) || $parameters['assetSource'] === null) {
            $parameters['contentId'] = $parameters['destinationContentId'];
            $view = new ForwardView(
                $parameters['template'] ?? null,
                $parameters,
                $parameters['viewType'] ?? 'full'
            );

            $view->setForwardTo(
                'ibexa_content:viewAction'
            );

            return $view;
        }

        $assetSource = new AssetSource($parameters['assetSource']);

        $asset = $this->assetService->get(
            new AssetIdentifier($parameters['destinationContentId']),
            $assetSource
        );

        $transformation = null;

        if (isset($parameters['transformation'])) {
            $transformation = $this
                ->transformationFactoryRegistry
                ->getFactory($assetSource)
                ->build($parameters['transformation']);
        }

        if (isset($parameters['params']['parameters']['transformation'])
            && $parameters['params']['parameters']['transformation'] instanceof Transformation
        ) {
            $transformation = $parameters['params']['parameters']['transformation'];
        }

        if ($transformation !== null) {
            $asset = $this->assetService->transform(
                $asset,
                $parameters['params']['parameters']['transformation']
            );
        }

        $view->setAsset($asset);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }
}

class_alias(AssetViewBuilder::class, 'Ibexa\Platform\Connector\Dam\View\AssetViewBuilder');
