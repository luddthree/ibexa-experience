<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Templating\Twig;

use Ibexa\Personalization\Formatter\NumberFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class NumberExtension extends AbstractExtension
{
    private NumberFormatterInterface $numberFormatter;

    public function __construct(NumberFormatterInterface $numberFormatter)
    {
        $this->numberFormatter = $numberFormatter;
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_personalization_short_number',
                [$this, 'shortenNumber'],
            ),
        ];
    }

    public function shortenNumber(float $number, int $precision = 1): string
    {
        return $this->numberFormatter->shortenNumber($number, $precision);
    }
}

class_alias(NumberExtension::class, 'Ibexa\Platform\Bundle\Personalization\Templating\Twig\NumberExtension');
