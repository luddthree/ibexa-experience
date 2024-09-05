<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Formatter;

use Ibexa\Contracts\Measurement\Formatter\MeasurementSignFormatterInterface;
use Ibexa\Contracts\Measurement\Value\Definition\Sign;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class MeasurementSignFormatter implements MeasurementSignFormatterInterface
{
    /** @var array<\Ibexa\Contracts\Measurement\Value\Definition\Sign::SIGN_*,string> */
    private array $map;

    /**
     * @param array<\Ibexa\Contracts\Measurement\Value\Definition\Sign::SIGN_*,string> $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function format(string $sign): string
    {
        if (!Sign::isValidSign($sign)) {
            throw new InvalidArgumentException('$sign', 'undefined sign');
        }

        return $this->map[$sign] ?? $sign;
    }
}
