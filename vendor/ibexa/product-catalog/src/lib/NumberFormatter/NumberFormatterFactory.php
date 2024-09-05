<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\NumberFormatter;

use Ibexa\ProductCatalog\NumberFormatter\LocaleProvider\LocaleResolverInterface;
use NumberFormatter;

final class NumberFormatterFactory implements NumberFormatterFactoryInterface
{
    private LocaleResolverInterface $localeProvider;

    private int $style;

    private string $pattern;

    /** @var array<int,int> */
    private array $attributes;

    /**
     * @param array<int,int> $attributes
     */
    public function __construct(
        LocaleResolverInterface $localeProvider,
        int $style,
        string $pattern = '',
        array $attributes = []
    ) {
        $this->localeProvider = $localeProvider;
        $this->style = $style;
        $this->pattern = $pattern;
        $this->attributes = $attributes;
    }

    public function createNumberFormatter(?string $locale = null): NumberFormatter
    {
        $formatter = new NumberFormatter(
            $locale ?? $this->localeProvider->getCurrentLocale(),
            $this->style,
            $this->pattern
        );

        foreach ($this->attributes as $attribute => $value) {
            $formatter->setAttribute($attribute, $value);
        }

        return $formatter;
    }
}
