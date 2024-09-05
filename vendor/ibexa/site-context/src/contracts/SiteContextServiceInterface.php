<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteContext;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\MVC\Symfony\SiteAccess;

interface SiteContextServiceInterface
{
    public function getCurrentContext(): ?SiteAccess;

    public function setCurrentContext(?SiteAccess $siteAccess): void;

    public function setFullscreenMode(bool $state): void;

    public function isFullscreenModeEnabled(): bool;

    public function resolveContextLanguage(SiteAccess $siteAccess, Content $content): string;
}
