<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;

final class PercentEntry extends AbstractEntry
{
    private Percent $value;

    public function __construct(Percent $value)
    {
        $this->value = $value;
    }

    public function getValue(): Percent
    {
        return $this->value;
    }

    public function getCompleteness(): Percent
    {
        return $this->value;
    }
}
