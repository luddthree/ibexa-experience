<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Twig;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteContext\PreviewUrlResolver\LocationPreviewUrlResolverInterface;

final class PreviewUrlRuntime
{
    private LocationPreviewUrlResolverInterface $previewUrlResolver;

    public function __construct(LocationPreviewUrlResolverInterface $previewUrlResolver)
    {
        $this->previewUrlResolver = $previewUrlResolver;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function getPreviewUrl(Location $location, array $context = []): string
    {
        return $this->previewUrlResolver->resolveUrl($location, $context);
    }
}
