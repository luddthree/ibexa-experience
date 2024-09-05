<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Creator;

use Ibexa\Contracts\Core\Repository\Values\Content\Content as ApiContent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as ApiVersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType as ApiContentType;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;

trait TestProductFactory
{
    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     */
    public function createContent(
        ApiContentType $contentType,
        ApiVersionInfo $versionInfo,
        array $fields
    ): ApiContent {
        return new Content(
            [
                'contentType' => $contentType,
                'versionInfo' => $versionInfo,
                'internalFields' => $fields,
            ]
        );
    }

    /**
     * @param array<string, string> $names
     */
    public function createContentType(
        int $contentTypeId,
        string $contentTypeIdentifier,
        string $language,
        array $names
    ): ApiContentType {
        return new ContentType(
            [
                'id' => $contentTypeId,
                'identifier' => $contentTypeIdentifier,
                'mainLanguageCode' => $language,
                'names' => $names,
            ]
        );
    }

    public function createVersionInfo(ContentInfo $contentInfo): ApiVersionInfo
    {
        return new VersionInfo(
            [
                'contentInfo' => $contentInfo,
            ]
        );
    }

    public function createContentInfo(
        int $contentId,
        string $mainLanguageCode,
        string $name
    ): ContentInfo {
        return new ContentInfo(
            [
                'id' => $contentId,
                'mainLanguage' => new Language(
                    [
                        'id' => 1,
                        'languageCode' => $mainLanguageCode,
                        'name' => $name,
                    ]
                ),
            ]
        );
    }
}
