<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Formatter;

use NumberFormatter as IntlNumberFormatter;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NumberFormatter implements NumberFormatterInterface
{
    private const DEFAULT_NUMBER_FORMATTER_LOCALE = 'en';

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function shortenNumber(float $number, int $precision = 1): string
    {
        if ($this->requestStack->getCurrentRequest() instanceof Request) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();
        }

        $formatter = IntlNumberFormatter::create(
            $locale ?? self::DEFAULT_NUMBER_FORMATTER_LOCALE,
            IntlNumberFormatter::DECIMAL
        );
        $separator = $formatter->getSymbol(IntlNumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

        if (abs($number) < 900) {
            $numberFormat = $formatter->format($number);
            $suffix = '';
        } elseif ($number < 900000) {
            $formatter->setAttribute(IntlNumberFormatter::FRACTION_DIGITS, $precision);
            $numberFormat = $formatter->format($number / 1000);
            $suffix = $this->translator->trans(/** @Desc("K") */
                'number.format.thousand_abbreviation',
                [],
                'ibexa_personalization'
            );
        } else {
            $formatter->setAttribute(IntlNumberFormatter::FRACTION_DIGITS, $precision);
            $numberFormat = $formatter->format($number / 1000000);
            $suffix = $this->translator->trans(/** @Desc("M") */
                'number.format.million_abbreviation',
                [],
                'ibexa_personalization'
            );
        }

        if (!is_string($numberFormat)) {
            throw new RuntimeException('Failed to format number: ' . $number);
        }

        if ($precision > 0) {
            $numberFormat = str_replace(
                $separator . str_repeat('0', $precision),
                '',
                $numberFormat
            );
        }

        return $numberFormat . $suffix;
    }
}

class_alias(NumberFormatter::class, 'Ibexa\Platform\Personalization\Formatter\NumberFormatter');
