<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Storage\Handler;

interface PageHandlerInterface
{
    public function loadPageByContentId(int $contentId, int $versionNo, string $languageCode): array;

    public function loadPagesMappedToContent(int $contentId, int $versionNo, array $languageCodes): array;

    public function insertPage(int $contentId, int $versionNo, string $languageCode, array $page): int;

    public function removePage(int $pageId): void;
}
