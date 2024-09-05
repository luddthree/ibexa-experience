<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Common;

use Stringable;

/**
 * VO representing percent value e.g. 25%.
 */
final class Percent implements Stringable
{
    private const DEFAULT_EPSILON = 0.00001;

    public float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getValueAsFloat(): float
    {
        return $this->value / 100.0;
    }

    public function equals(Percent $percent, float $epsilon = self::DEFAULT_EPSILON): bool
    {
        return abs($percent->value - $this->value) <= $epsilon;
    }

    public function __toString(): string
    {
        return $this->value . '%';
    }

    public function fraction(float $a, float $b): Percent
    {
        if ($b === 0.0) {
            self::zero();
        }

        return new Percent(
            min(max($a / $b, 0.0), 100.0)
        );
    }

    public static function zero(): self
    {
        static $value = null;
        if ($value === null) {
            $value = new Percent(0.0);
        }

        return $value;
    }

    public static function hundred(): self
    {
        static $value = null;
        if ($value === null) {
            $value = new Percent(100.0);
        }

        return $value;
    }
}
