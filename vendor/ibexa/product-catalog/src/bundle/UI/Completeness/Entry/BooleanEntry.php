<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;

final class BooleanEntry extends AbstractEntry
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function getCompleteness(): Percent
    {
        return $this->value ? Percent::hundred() : Percent::zero();
    }
}
