<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;

abstract class AbstractEntry implements EntryInterface
{
    public function isComplete(): bool
    {
        return $this->getCompleteness()->equals(Percent::hundred());
    }
}
