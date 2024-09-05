<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType as ApiContentType;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;

final class ContentTestDataProvider
{
    public static function getSimpleContentType(): ContentType
    {
        return new ContentType([
            'identifier' => 'example_ct',
        ]);
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     */
    public static function getContent(ApiContentType $contentType, array $fields): Content
    {
        return new Content([
            'versionInfo' => self::getVersionInfo(),
            'contentType' => $contentType,
            'internalFields' => $fields,
        ]);
    }

    public static function getVersionInfo(): VersionInfo
    {
        return new VersionInfo([
            'versionNo' => 1,
            'names' => [
                'eng-GB' => 'Updated example entry',
            ],
            'initialLanguageCode' => 'eng-GB',
            'contentInfo' => new ContentInfo([
                'id' => 1,
                'mainLanguageCode' => 'eng-GB',
            ]),
        ]);
    }

    public static function getContentInfo(): ContentInfo
    {
        return new ContentInfo([
            'contentType' => new ContentType([
                'identifier' => 'example_ct',
            ]),
            'id' => 1,
            'mainLanguageCode' => 'eng-GB',
        ]);
    }
}
