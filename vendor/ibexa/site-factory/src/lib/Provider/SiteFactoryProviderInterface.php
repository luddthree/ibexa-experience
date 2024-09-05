<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Provider;

use Traversable;

interface SiteFactoryProviderInterface
{
    /**
     * @return \Ibexa\Contracts\SiteFactory\Values\Design\TemplateConfiguration[]
     */
    public function getTemplatesConfiguration(): Traversable;
}

class_alias(SiteFactoryProviderInterface::class, 'EzSystems\EzPlatformSiteFactory\Provider\SiteFactoryProviderInterface');
