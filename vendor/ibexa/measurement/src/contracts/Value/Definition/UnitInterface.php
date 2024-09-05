<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Value\Definition;

use Stringable;

interface UnitInterface extends Stringable
{
    public function getIdentifier(): string;

    public function getSymbol(): string;

    public function isBaseUnit(): bool;

    public function equals(UnitInterface $unit): bool;
}
