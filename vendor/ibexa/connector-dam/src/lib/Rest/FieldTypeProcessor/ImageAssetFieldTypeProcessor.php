<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Rest\FieldTypeProcessor;

use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Rest\FieldTypeProcessor;
use Ibexa\Rest\FieldTypeProcessor\ImageAssetFieldTypeProcessor as BaseImageAssetFieldTypeProcessor;
use Symfony\Component\Routing\RouterInterface;

class ImageAssetFieldTypeProcessor extends FieldTypeProcessor
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Ibexa\Rest\FieldTypeProcessor\ImageAssetFieldTypeProcessor */
    private $innerProcessor;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry */
    private $transformationFactoryRegistry;

    public function __construct(
        BaseImageAssetFieldTypeProcessor $innerProcessor,
        RouterInterface $router,
        AssetService $assetService,
        TransformationFactoryRegistry $transformationFactoryRegistry
    ) {
        $this->innerProcessor = $innerProcessor;
        $this->router = $router;
        $this->assetService = $assetService;
        $this->transformationFactoryRegistry = $transformationFactoryRegistry;
    }

    public function postProcessValueHash($outgoingValueHash)
    {
        if (!\is_array($outgoingValueHash)) {
            return $outgoingValueHash;
        }

        if ($outgoingValueHash['destinationContentId'] === null) {
            return $outgoingValueHash;
        }

        if ($outgoingValueHash['source'] === null) {
            return $this->innerProcessor->postProcessValueHash($outgoingValueHash);
        }

        $asset = $this->assetService->get(
            new AssetIdentifier($outgoingValueHash['destinationContentId']),
            new AssetSource($outgoingValueHash['source'])
        );

        $transformationFactory = $this->transformationFactoryRegistry->getFactory($asset->getSource());

        $transformations = $transformationFactory->buildAll();

        foreach ($transformations as $transformation) {
            $outgoingValueHash['variations'][$transformation->getName()] = [
                'href' => $this->router->generate(
                    'ibexa.connector.dam.asset_variation',
                    [
                        'assetId' => $asset->getIdentifier()->getId(),
                        'assetSource' => $asset->getSource()->getSourceIdentifier(),
                        'transformationName' => $transformation->getName(),
                    ]
                ),
            ];
        }

        return $outgoingValueHash;
    }
}

class_alias(ImageAssetFieldTypeProcessor::class, 'Ibexa\Platform\Connector\Dam\Rest\FieldTypeProcessor\ImageAssetFieldTypeProcessor');
