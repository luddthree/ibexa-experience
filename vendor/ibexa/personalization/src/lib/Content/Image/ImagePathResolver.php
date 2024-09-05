<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Content\Image;

use Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Ibexa\Core\FieldType\Value;
use Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface;

/**
 * @internal
 */
final class ImagePathResolver implements ImagePathResolverInterface
{
    private ContentService $contentService;

    private OutputTypeAttributesMapperInterface $outputTypeAttributesMapper;

    private string $webRootDir;

    public function __construct(
        ContentService $contentService,
        OutputTypeAttributesMapperInterface $outputTypeAttributesMapper,
        string $webRootDir
    ) {
        $this->contentService = $contentService;
        $this->outputTypeAttributesMapper = $outputTypeAttributesMapper;
        $this->webRootDir = $webRootDir;
    }

    public function imageExists(string $recommendedImage): bool
    {
        return file_exists($this->webRootDir . $recommendedImage);
    }

    public function resolveImagePathByContentId(
        int $customerId,
        string $recommendedImage,
        int $contentId
    ): ?string {
        try {
            return $this->getImageUri(
                $customerId,
                $this->contentService->loadContent($contentId)
            );
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    public function resolveImagePathByContentRemoteId(
        int $customerId,
        string $recommendedImage,
        string $remoteId
    ): ?string {
        try {
            return $this->getImageUri(
                $customerId,
                $this->contentService->loadContentByRemoteId($remoteId)
            );
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    private function getImageUri(int $customerId, Content $content): ?string
    {
        $imageFieldName = $this->getImageFieldName($customerId, $content->getContentType()->id);
        if (null === $imageFieldName) {
            return null;
        }

        $field = $content->getField($imageFieldName);
        if (null === $field) {
            return null;
        }

        $value = $field->value;
        if ($value instanceof ImageValue) {
            return $value->uri;
        }

        $destinationContentId = $this->getDestinationContentId($value);
        if (null !== $destinationContentId) {
            return $this->getImageUriFromDestinationContent($destinationContentId);
        }

        return null;
    }

    private function getDestinationContentId(Value $value): ?int
    {
        if ($value instanceof RelationListValue) {
            $id = current($value->destinationContentIds);

            return false !== $id ? (int) $id : null;
        }

        if (
            $value instanceof ImageAssetValue
            || $value instanceof RelationValue
        ) {
            return (int) $value->destinationContentId;
        }

        return null;
    }

    private function getImageUriFromDestinationContent(int $destinationContentId): ?string
    {
        $destinationContent = $this->contentService->loadContent($destinationContentId);
        $field = $destinationContent->getField('image');
        if (null === $field) {
            return null;
        }

        return $field->value->uri;
    }

    private function getImageFieldName(int $customerId, int $contentTypeId): ?string
    {
        return $this->outputTypeAttributesMapper->reverseMapAttribute(
            $customerId,
            $contentTypeId,
            Personalization::IMAGE_ATTR_NAME
        );
    }
}
