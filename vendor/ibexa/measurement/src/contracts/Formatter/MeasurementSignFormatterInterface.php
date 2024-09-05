<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Formatter;

/**
 * @internal
 */
interface MeasurementSignFormatterInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\Measurement\Value\Definition\Sign::SIGN_* $sign
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function format(string $sign): string;
}
