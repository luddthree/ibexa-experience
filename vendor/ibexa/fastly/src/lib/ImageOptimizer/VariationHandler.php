<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Fastly\ImageOptimizer;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Contracts\Core\Variation\Values\Variation;
use Ibexa\Contracts\Core\Variation\VariationHandler as VariationHandlerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;

final class VariationHandler implements VariationHandlerInterface
{
    public const IDENTIFIER = 'fastly';

    private ContentService $contentService;

    private AssetMapper $assetMapper;

    private VariationResolver $variationResolver;

    private ConfigResolverInterface $configResolver;

    private VariationHandlerInterface $referenceHandler;

    public function __construct(
        VariationResolver $variationResolver,
        ContentService $contentService,
        AssetMapper $assetMapper,
        ConfigResolverInterface $configResolver,
        VariationHandlerInterface $referenceHandler
    ) {
        $this->contentService = $contentService;
        $this->assetMapper = $assetMapper;
        $this->variationResolver = $variationResolver;
        $this->configResolver = $configResolver;
        $this->referenceHandler = $referenceHandler;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getVariation(
        Field $field,
        VersionInfo $versionInfo,
        $variationName,
        array $parameters = []
    ): Variation {
        $value = $field->getValue();

        if (!$this->supports($value)) {
            throw new InvalidArgumentException(
                '$field',
                sprintf(
                    'Value of Field with ID %d (%s) cannot be used for generating an image variation.',
                    $field->id,
                    $field->fieldDefIdentifier
                )
            );
        }

        $configuration = $this->configResolver->getParameter('fastly_variations');

        if ($value instanceof ImageAssetValue) {
            $destinationImage = $this->contentService->loadContent(
                (int)$value->destinationContentId
            );

            $value = $this->assetMapper->getAssetValue($destinationImage);
            $versionInfo = $destinationImage->versionInfo;
        }

        if (!empty($configuration[$variationName]['mime_types'])) {
            $mimeType = mime_content_type($value->uri);

            if (!in_array($mimeType, $configuration[$variationName]['mime_types'], true)) {
                return $this->referenceHandler->getVariation(
                    $field,
                    $versionInfo,
                    $variationName,
                    $parameters
                );
            }
        }

        if (isset($configuration[$variationName]['reference'])) {
            $reference = $this->referenceHandler->getVariation(
                $field,
                $versionInfo,
                $configuration[$variationName]['reference'],
                $parameters
            );

            $path = $reference->uri;
        } else {
            $path = $value->uri;
        }

        return new ImageVariation([
            'imageId' => $value->imageId,
            'name' => $variationName,
            'handler' => self::IDENTIFIER,
            'isExternal' => true,
            'lastModified' => $versionInfo->modificationDate,
            'uri' => $this->variationResolver->resolve(
                $path,
                $variationName
            ),
        ]);
    }

    private function supports(Value $value): bool
    {
        return $value instanceof ImageValue || $value instanceof ImageAssetValue;
    }
}
