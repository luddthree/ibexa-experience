<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Value;

interface SimpleValueInterface extends ValueInterface
{
    public function getValue(): float;

    public function equals(?SimpleValueInterface $value): bool;
}
