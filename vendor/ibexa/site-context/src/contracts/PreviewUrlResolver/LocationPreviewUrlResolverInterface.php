<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteContext\PreviewUrlResolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

interface LocationPreviewUrlResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @throws \Ibexa\Contracts\SiteContext\Exception\UnresolvedPreviewUrlException
     */
    public function resolveUrl(Location $location, array $context = []): string;
}
