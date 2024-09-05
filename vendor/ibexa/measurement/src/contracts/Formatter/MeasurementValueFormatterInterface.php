<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Formatter;

use Ibexa\Contracts\Measurement\Value\ValueInterface;

/**
 * @internal
 */
interface MeasurementValueFormatterInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function format(ValueInterface $value, ?string $strategy = null): string;
}
