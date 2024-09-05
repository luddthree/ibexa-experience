<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\Controller;

use Ibexa\Connector\Dam\FieldType\ImageAsset\Value;
use Ibexa\Connector\Dam\Variation\AssetVariationGeneratorRegistry;
use Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariation;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Rest\Server\Controller\BinaryContent;
use Ibexa\Rest\Server\Values\CachedValue;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AssetVariationController extends AbstractController
{
    /** @var \Ibexa\Rest\Server\Controller\BinaryContent */
    private $innerController;

    /** @var \Ibexa\Contracts\Connector\Dam\AssetService */
    private $assetService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Connector\Dam\Variation\TransformationFactoryRegistry */
    private $transformationFactoryRegistry;

    /** @var \Ibexa\Connector\Dam\Variation\AssetVariationGeneratorRegistry */
    private $assetVariationGeneratorRegistry;

    public function __construct(
        BinaryContent $innerController,
        AssetService $assetService,
        ContentService $contentService,
        TransformationFactoryRegistry $transformationFactoryRegistry,
        AssetVariationGeneratorRegistry $assetVariationGeneratorRegistry
    ) {
        $this->assetService = $assetService;
        $this->innerController = $innerController;
        $this->contentService = $contentService;
        $this->transformationFactoryRegistry = $transformationFactoryRegistry;
        $this->assetVariationGeneratorRegistry = $assetVariationGeneratorRegistry;
    }

    public function getImageVariation(string $imageId, string $variationIdentifier)
    {
        list($contentId, $fieldId, $versionNumber) = $this->parseImageId($imageId);

        $content = $this->contentService->loadContent($contentId, null, $versionNumber);
        $field = null;
        foreach ($content->getFields() as $contentFields) {
            if ($contentFields->id === $fieldId) {
                $field = $contentFields;
                break;
            }
        }

        if ($field === null) {
            throw new Exceptions\NotFoundException("No image Field with ID $fieldId found");
        }

        if (($field->value instanceof Value && $field->value->source === null) ||
            $field->value instanceof \Ibexa\Core\FieldType\Image\Value
        ) {
            return $this->innerController->getImageVariation($imageId, $variationIdentifier);
        }
        /** @var \Ibexa\Connector\Dam\FieldType\ImageAsset\Value $value */
        $value = $field->value;

        $asset = $this->assetService->get(
            new AssetIdentifier($value->destinationContentId),
            new AssetSource($value->source)
        );

        return new CachedValue(
            $this->getAssetVariation($asset, $variationIdentifier),
            ['locationId' => $content->contentInfo->mainLocationId]
        );
    }

    public function getExternalAssetVariation(
        string $assetId,
        string $assetSource,
        string $transformationName
    ): AssetVariation {
        $asset = $this->assetService->get(
            new AssetIdentifier($assetId),
            new AssetSource($assetSource)
        );

        return $this->getAssetVariation($asset, $transformationName);
    }

    private function parseImageId($imageId): array
    {
        $idArray = explode('-', $imageId);
        $idArray = array_map('intval', $idArray);

        if (\count($idArray) == 2) {
            return array_merge($idArray, [null]);
        }

        return $idArray;
    }

    private function getAssetVariation(
        Asset $asset,
        string $variationIdentifier
    ): AssetVariation {
        $transformation = $this
            ->transformationFactoryRegistry
            ->getFactory($asset->getSource())
            ->build($variationIdentifier);

        return $this
            ->assetVariationGeneratorRegistry
            ->getVariationGenerator($asset->getSource())
            ->generate($asset, $transformation);
    }
}

class_alias(AssetVariationController::class, 'Ibexa\Platform\Bundle\Connector\Dam\Controller\AssetVariationController');
