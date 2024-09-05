<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Fastly\ImageOptimizer\PathGenerator;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Variation\VariationPathGenerator;

final class RawPathGenerator implements VariationPathGenerator
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function getVariationPath($path, $variation): string
    {
        $fastly = $this->configResolver->getParameter('fastly_variations');

        $configuration = $fastly[$variation]['configuration'] ?? null;

        if (empty($configuration)) {
            return $path;
        }

        return sprintf(
            '%s?%s',
            $path,
            http_build_query($configuration)
        );
    }
}
