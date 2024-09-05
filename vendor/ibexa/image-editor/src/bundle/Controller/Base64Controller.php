<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImageEditor\Controller;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\Core\FieldType\ImageAsset\Type;
use Ibexa\Core\FieldType\Value;

final class Base64Controller
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    public function __construct(
        ContentService $contentService,
        AssetMapper $assetMapper
    ) {
        $this->contentService = $contentService;
        $this->assetMapper = $assetMapper;
    }

    public function getBase64(
        int $contentId,
        string $fieldIdentifier,
        ?int $versionNo = null,
        ?string $languageCode = null
    ): Value {
        $content = $this->contentService->loadContent($contentId, null, $versionNo);
        if ($content->contentInfo->isTrashed()) {
            throw new NotFoundException('Content', $contentId);
        }

        $field = $content->getField($fieldIdentifier, $languageCode);
        if ($field === null) {
            throw new NotFoundException('Field', $fieldIdentifier);
        }

        if ($field->fieldTypeIdentifier === Type::FIELD_TYPE_IDENTIFIER) {
            if (isset($field->value->source)) {
                throw new InvalidArgumentException($field->value->source, 'Not implemented for external assets');
            }
            $content = $this->contentService->loadContent((int) $field->value->destinationContentId);
            $field = $this->assetMapper->getAssetField($content);
        }

        return $field->value;
    }
}

class_alias(Base64Controller::class, 'Ibexa\Platform\Bundle\ImageEditor\Controller\Base64Controller');
