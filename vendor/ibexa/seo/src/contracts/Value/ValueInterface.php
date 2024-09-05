<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Seo\Value;

interface ValueInterface extends \Stringable
{
    /**
     * @return \Ibexa\Seo\Value\SeoTypeValue[]
     */
    public function getSeoTypesValues(): array;
}
