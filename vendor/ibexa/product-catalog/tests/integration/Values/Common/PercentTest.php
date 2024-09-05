<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Values\Common;

use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use PHPUnit\Framework\TestCase;

final class PercentTest extends TestCase
{
    /**
     * @dataProvider provideForTestGetValueAsFloat
     */
    public function testGetValueAsFloat(float $value, float $expectedValue): void
    {
        $percent = new Percent($value);

        self::assertEquals($expectedValue, $percent->getValueAsFloat());
    }

    /**
     * @phpstan-return iterable<array{float, float }>
     */
    public function provideForTestGetValueAsFloat(): iterable
    {
        yield [50, 0.5];
        yield [0, 0.0];
        yield [100, 1.0];
    }

    /**
     * @dataProvider provideForTestEquals
     *
     * @phpstan-param array{
     *     value: float,
     *     toCompare: float,
     * } $parameters
     */
    public function testEquals(bool $result, array $parameters, float $epsilon = null): void
    {
        $percent = new Percent($parameters['value']);
        $toComparePercent = new Percent($parameters['toCompare']);

        if ($epsilon !== null) {
            self::assertEquals($result, $percent->equals($toComparePercent, $epsilon));
        } else {
            self::assertEquals($result, $percent->equals($toComparePercent));
        }
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         bool,
     *         array{
     *             value: float,
     *             toCompare: float,
     *         },
     *         2?:float
     *     }
     * >
     */
    public function provideForTestEquals(): iterable
    {
        yield [
            true,
            [
                'value' => 50,
                'toCompare' => 50,
            ],
            0.001,
        ];

        yield [
            false,
            [
                'value' => 50,
                'toCompare' => 20,
            ],
            0.01,
        ];

        yield [
            true,
            [
                'value' => 50,
                'toCompare' => 50,
            ],
        ];

        yield [
            false,
            [
                'value' => 50,
                'toCompare' => 30,
            ],
        ];
    }
}
