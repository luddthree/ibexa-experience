<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\ConfigResolver;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Seo\ConfigResolver\SeoTypesInterface;

/**
 * @internal
 */
final class SeoTypes implements SeoTypesInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function getEnabledTypes(): array
    {
        return $this->getTypesConfiguration();
    }

    /**
     * @return array<string, string[]>
     */
    private function getTypesConfiguration(): array
    {
        /** @var array<string, string[]> */
        return $this->configResolver->getParameter('seo.types');
    }
}
