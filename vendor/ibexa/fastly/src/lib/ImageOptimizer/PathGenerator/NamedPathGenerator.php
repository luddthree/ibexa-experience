<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Fastly\ImageOptimizer\PathGenerator;

use Ibexa\Contracts\Core\Variation\VariationPathGenerator;

final class NamedPathGenerator implements VariationPathGenerator
{
    private string $propertyName;

    public function __construct(
        string $propertyName = 'class'
    ) {
        $this->propertyName = $propertyName;
    }

    public function getVariationPath($path, $variation): string
    {
        return sprintf('%s?%s=%s', $path, $this->propertyName, $variation);
    }
}
