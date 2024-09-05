<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Formatter;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\Formatter\Strategy\FormattingStrategyInterface;

final class MeasurementValueFormatter implements MeasurementValueFormatterInterface
{
    /** @var iterable<string,\Ibexa\Measurement\Formatter\Strategy\FormattingStrategyInterface> */
    private iterable $strategies;

    private FormattingStrategyInterface $defaultStrategy;

    /**
     * @param iterable<string,\Ibexa\Measurement\Formatter\Strategy\FormattingStrategyInterface> $strategies
     */
    public function __construct(iterable $strategies, FormattingStrategyInterface $defaultStrategy)
    {
        $this->strategies = $strategies;
        $this->defaultStrategy = $defaultStrategy;
    }

    public function format(ValueInterface $value, ?string $strategy = null): string
    {
        return $this->getStrategy($strategy)->format($value);
    }

    private function getStrategy(?string $needle): FormattingStrategyInterface
    {
        if ($needle === null) {
            return $this->defaultStrategy;
        }

        foreach ($this->strategies as $name => $strategy) {
            if ($needle === $name) {
                return $strategy;
            }
        }

        throw new InvalidArgumentException('$strategy', 'Unknown formatting strategy: ' . $needle);
    }
}
