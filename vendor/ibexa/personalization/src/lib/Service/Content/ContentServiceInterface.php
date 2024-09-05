<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * @internal
 */
interface ContentServiceInterface
{
    /**
     * @param array<string>|null $languageCodes
     */
    public function updateContent(Content $content, ?array $languageCodes = null): void;

    /**
     * All Content items should be configured as included_item_types.
     * Content items can be available under several SiteAccesses.
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItems
     */
    public function updateContentItems(array $contentItems): void;

    /**
     * @param array<string> $languageCodes
     */
    public function deleteContent(Content $content, array $languageCodes): void;

    /**
     * All Content items should be configured as included_item_types.
     * Content items can be available under several SiteAccesses.
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItems
     */
    public function deleteContentItems(array $contentItems): void;
}
