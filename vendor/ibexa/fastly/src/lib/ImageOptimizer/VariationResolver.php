<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Fastly\ImageOptimizer;

use Ibexa\Bundle\Core\Variation\PathResolver;

final class VariationResolver extends PathResolver
{
    public function resolve($path, $variation): string
    {
        return sprintf(
            '%s%s',
            $path[0] === '/' ? $this->getBaseUrl() : '',
            $this->getFilePath($path, $variation)
        );
    }
}
