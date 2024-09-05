<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\News;

/**
 * @internal
 */
interface FeedInterface
{
    /**
     * @return \Ibexa\Dashboard\News\Values\NewsItem[]
     *
     * @throws \Ibexa\Dashboard\News\FeedException
     */
    public function fetch(string $url, int $limit): array;
}
