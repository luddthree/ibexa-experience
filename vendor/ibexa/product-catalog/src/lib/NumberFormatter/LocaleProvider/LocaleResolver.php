<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\NumberFormatter\LocaleProvider;

use Locale;
use Symfony\Component\HttpFoundation\RequestStack;

final class LocaleResolver implements LocaleResolverInterface
{
    private RequestStack $requestStack;

    private ?string $locale;

    public function __construct(RequestStack $requestStack, ?string $locale = null)
    {
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    public function getCurrentLocale(): string
    {
        if ($this->locale !== null) {
            return $this->locale;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            return $request->getLocale();
        }

        return Locale::getDefault();
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
