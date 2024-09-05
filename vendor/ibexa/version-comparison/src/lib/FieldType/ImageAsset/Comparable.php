<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\ImageAsset;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Core\FieldType\Image\Type;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\VersionComparison\ComparisonValue\BinaryFileComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;

final class Comparable implements ComparableInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    /** @var \Ibexa\Core\FieldType\Image\Type */
    private $imageType;

    public function __construct(
        ContentService $contentService,
        AssetMapper $assetMapper,
        Type $imageType
    ) {
        $this->contentService = $contentService;
        $this->assetMapper = $assetMapper;
        $this->imageType = $imageType;
    }

    /**
     * @param \Ibexa\Core\FieldType\ImageAsset\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        $assetValue = $this->imageType->getEmptyValue();
        try {
            $targetContent = $this->contentService->loadContent((int) $value->destinationContentId);
            $assetValue = $this->assetMapper->getAssetValue($targetContent);
        } catch (APINotFoundException $notFoundException) {
            // Use empty image value, when content is not found.
        }

        return new Value([
            'fileName' => new StringComparisonValue([
                'value' => $assetValue->fileName,
                'doNotSplit' => true,
            ]),
            'destinationContentId' => new IntegerComparisonValue([
                'value' => (int) $value->destinationContentId,
            ]),
            'file' => new BinaryFileComparisonValue([
                'path' => $assetValue->uri,
                'size' => $assetValue->fileSize,
            ]),
            'alternativeText' => new StringComparisonValue([
                'value' => $value->alternativeText ?? $assetValue->alternativeText,
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\ImageAsset\Comparable');
