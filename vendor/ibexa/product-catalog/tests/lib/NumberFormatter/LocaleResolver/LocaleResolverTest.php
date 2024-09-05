<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\NumberFormatter\LocaleResolver;

use Ibexa\ProductCatalog\NumberFormatter\LocaleProvider\LocaleResolver;
use Locale;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LocaleResolverTest extends TestCase
{
    private const EXAMPLE_LOCALE = 'en';

    public function testGetCurrentLocaleWithForcedLocale(): void
    {
        $requestStack = $this->createMock(RequestStack::class);

        $resolver = new LocaleResolver($requestStack);
        $resolver->setLocale(self::EXAMPLE_LOCALE);

        self::assertEquals(self::EXAMPLE_LOCALE, $resolver->getCurrentLocale());
    }

    public function testGetCurrentLocaleFromRequest(): void
    {
        $request = new Request();
        $request->setLocale(self::EXAMPLE_LOCALE);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $resolver = new LocaleResolver($requestStack);

        self::assertEquals(self::EXAMPLE_LOCALE, $resolver->getCurrentLocale());
    }

    public function testGetCurrentLocaleFallback(): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn(null);

        $resolver = new LocaleResolver($requestStack);

        self::assertEquals(Locale::getDefault(), $resolver->getCurrentLocale());
    }
}
