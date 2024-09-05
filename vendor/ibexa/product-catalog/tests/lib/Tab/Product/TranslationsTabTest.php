<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab\Product;

use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Tab\Product\TranslationsTab;
use Ibexa\Tests\ProductCatalog\Tab\BaseTranslationsTabTest;
use stdClass;
use Symfony\Component\Form\FormView;

final class TranslationsTabTest extends BaseTranslationsTabTest
{
    /**
     * @dataProvider providerForGetTemplateParameters
     *
     * @param \Ibexa\Bundle\ProductCatalog\UI\Language[] $expectedTranslations
     */
    public function testGetTemplateParameters(ProductInterface $product, array $expectedTranslations): void
    {
        $templateParameters = $this->translationTab->getTemplateParameters([
            'product' => $product,
        ]);

        self::assertEquals($product, $templateParameters['product']);
        self::assertEquals($expectedTranslations, $templateParameters['translations']);
        self::assertInstanceOf(FormView::class, $templateParameters['form_translation_add']);
        self::assertInstanceOf(FormView::class, $templateParameters['form_translation_remove']);
    }

    /**
     * @return iterable<string,array{
     *     \Ibexa\Contracts\ProductCatalog\Values\ProductInterface,
     *     array{\Ibexa\Bundle\ProductCatalog\UI\Language}
     * }>
     */
    public function providerForGetTemplateParameters(): iterable
    {
        $product = $this->getProduct(['eng-GB']);

        yield 'translatable product' => [
            $product,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
            ],
        ];

        $multiLanguageProduct = $this->getProduct(['eng-GB', 'pol-PL']);

        yield 'translatable product with multiple languages' => [
            $multiLanguageProduct,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
                new UILanguage(new Language(['id' => 4,  'languageCode' => 'pol-PL'])),
            ],
        ];
    }

    /**
     * @return iterable<string,array{mixed,bool}>
     */
    public function providerForEvaluate(): iterable
    {
        yield 'translatable product' => [
            $this->getProduct(),
            true,
        ];

        yield 'non-translatable product' => [
            $this->createMock(ProductInterface::class),
            false,
        ];

        yield 'stdClass' => [
            new stdClass(),
            false,
        ];

        yield 'null' => [
            null,
            false,
        ];

        yield 'bool' => [
            true,
            false,
        ];
    }

    public function getParameterKey(): string
    {
        return 'product';
    }

    public function getTranslationTabName(): string
    {
        return TranslationsTab::class;
    }

    /**
     * @param string[] $languageCodes
     */
    private function getProduct(array $languageCodes = []): Product
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo->method('__get')->with('languageCodes')->willReturn($languageCodes);

        $content = $this->createMock(Content::class);
        $content->method('__get')->with('versionInfo')->willReturn($versionInfo);

        return new Product(
            $this->createMock(ProductTypeInterface::class),
            $content,
            '0001'
        );
    }
}
