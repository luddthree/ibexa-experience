<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\Host;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * @internal
 */
interface HostResolverInterface
{
    public function resolveUrl(Content $content, string $languageCode): ?string;
}
