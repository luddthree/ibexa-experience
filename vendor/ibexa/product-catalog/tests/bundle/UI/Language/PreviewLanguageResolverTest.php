<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI\Language;

use Ibexa\Bundle\ProductCatalog\UI\Language\PreviewLanguageResolver;
use Ibexa\Bundle\ProductCatalog\UI\Language\PreviewLanguageResolverInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class PreviewLanguageResolverTest extends TestCase
{
    /**
     * @dataProvider provideForTestResolve
     */
    public function testResolve(?string $expectedLanguageCode, string $uri): void
    {
        $language = $this->getLanguageResolver($expectedLanguageCode, $uri)->resolve();
        if ($expectedLanguageCode === null) {
            self::assertNull($language);
        } else {
            self::assertNotNull($language);
            self::assertEquals($expectedLanguageCode, $language->languageCode);
        }
    }

    /**
     * @phpstan-return iterable<array{?string,string}>
     */
    public function provideForTestResolve(): iterable
    {
        yield [
            'ger-DE',
            'https://foo.com/?language=ger-DE#foo',
        ];

        yield [
            'fre-FR',
            'https://foo.com/?language=fre-FR#foo',
        ];

        yield [
            null,
            'https://foo.com',
        ];
    }

    private function getLanguageResolver(?string $languageCode, string $uri): PreviewLanguageResolverInterface
    {
        $request = Request::create($uri);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $languageService = $this->createMock(LanguageService::class);
        $languageService
            ->method('loadLanguage')
            ->with($languageCode)
            ->willReturn(
                new Language([
                    'id' => 1,
                    'languageCode' => $languageCode,
                    'enabled' => true,
                ]),
            );

        return new PreviewLanguageResolver($requestStack, $languageService);
    }
}
