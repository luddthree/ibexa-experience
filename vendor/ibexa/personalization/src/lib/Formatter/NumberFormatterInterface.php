<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Formatter;

/**
 * @internal
 */
interface NumberFormatterInterface
{
    public function shortenNumber(float $number, int $precision = 1): string;
}

class_alias(NumberFormatterInterface::class, 'Ibexa\Platform\Personalization\Formatter\NumberFormatterInterface');
