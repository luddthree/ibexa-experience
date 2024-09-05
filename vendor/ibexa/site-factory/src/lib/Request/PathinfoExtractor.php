<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Request;

final class PathinfoExtractor
{
    public static function getFirstPathElement(string $pathInfo): ?string
    {
        $pathElements = explode('/', $pathInfo);

        if ($pathElements === false) {
            return null;
        }

        return $pathElements[1] !== '' ? $pathElements[1] : null;
    }
}

class_alias(PathinfoExtractor::class, 'EzSystems\EzPlatformSiteFactory\Request\PathinfoExtractor');
