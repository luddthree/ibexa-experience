<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Form\Data;

use Ibexa\Core\MVC\Symfony\SiteAccess;

final class ChangeSiteContextData
{
    private ?SiteAccess $siteAccess;

    public function __construct(?SiteAccess $siteAccess = null)
    {
        $this->siteAccess = $siteAccess;
    }

    public function getSiteAccess(): ?SiteAccess
    {
        return $this->siteAccess;
    }

    public function setSiteAccess(?SiteAccess $siteAccess): void
    {
        $this->siteAccess = $siteAccess;
    }
}
