<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Content\Routing;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * @internal used only for generating URL for content to be fetched by recommendation engine
 */
interface UrlGeneratorInterface
{
    public function generate(
        Content $content,
        bool $useRemoteId,
        ?string $lang = null
    ): string;

    /**
     * @param array<int> $contentIds
     */
    public function generateForContentIds(
        array $contentIds,
        string $lang
    ): string;
}
