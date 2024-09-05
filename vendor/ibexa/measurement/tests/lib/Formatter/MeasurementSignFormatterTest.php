<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\Formatter;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Measurement\Value\Definition\Sign;
use Ibexa\Measurement\Formatter\MeasurementSignFormatter;
use PHPUnit\Framework\TestCase;

final class MeasurementSignFormatterTest extends TestCase
{
    private const CUSTOM_SIGN = '~';

    private MeasurementSignFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new MeasurementSignFormatter([
            Sign::SIGN_NONE => '',
            Sign::SIGN_GT => '>',
            Sign::SIGN_LT => '<',
            Sign::SIGN_GTE => '≥',
            Sign::SIGN_LTE => '≤',
            Sign::SIGN_PM => '±',
        ]);
    }

    public function testFormat(): void
    {
        self::assertEquals('≥', $this->formatter->format(Sign::SIGN_GTE));
    }

    public function testFormatThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$sign' is invalid: undefined sign");

        $this->formatter->format(self::CUSTOM_SIGN);
    }
}
