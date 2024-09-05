<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;

interface EntryInterface
{
    /**
     * @phpstan-return mixed
     */
    public function getValue();

    public function getCompleteness(): Percent;

    public function isComplete(): bool;
}
