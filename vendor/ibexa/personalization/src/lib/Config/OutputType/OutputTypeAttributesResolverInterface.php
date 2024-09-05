<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\OutputType;

/**
 * @internal
 */
interface OutputTypeAttributesResolverInterface
{
    /**
     * @return array<array<string, string>>
     */
    public function resolve(int $customerId): array;
}
