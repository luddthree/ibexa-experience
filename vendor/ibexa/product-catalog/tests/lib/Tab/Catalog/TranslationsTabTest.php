<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab\Catalog;

use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;
use Ibexa\ProductCatalog\Tab\Catalog\TranslationsTab;
use Ibexa\Tests\ProductCatalog\Tab\BaseTranslationsTabTest;
use stdClass;
use Symfony\Component\Form\FormView;

final class TranslationsTabTest extends BaseTranslationsTabTest
{
    private const CATALOG_ID = 1;
    private const CATALOG_IDENTIFIER = 'foo';

    /**
     * @return iterable<string,array{mixed,bool}>
     */
    public function providerForEvaluate(): iterable
    {
        yield 'translatable catalog' => [
            new Catalog(
                self::CATALOG_ID,
                self::CATALOG_IDENTIFIER,
                'name',
                ['pol-PL'],
                $this->getTestUser(),
                1647865210,
                1647865251,
                'draft',
                new LogicalAnd([]),
                'description',
            ),
            true,
        ];

        yield 'non-translatable catalog' => [
            $this->createMock(CatalogInterface::class),
            false,
        ];

        yield 'string' => [
            'bar',
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

    /**
     * @dataProvider providerForGetTemplateParameters
     *
     * @param array<\Ibexa\Bundle\ProductCatalog\UI\Language> $expectedTranslations
     */
    public function testGetTemplateParameters(
        Catalog $catalog,
        array $expectedTranslations
    ): void {
        $templateParameters = $this->translationTab->getTemplateParameters(['catalog' => $catalog]);
        self::assertEquals(
            $catalog,
            $templateParameters['catalog']
        );
        self::assertEquals(
            $expectedTranslations,
            $templateParameters['translations']
        );
        self::assertInstanceOf(FormView::class, $templateParameters['form_translation_add']);
        self::assertInstanceOf(FormView::class, $templateParameters['form_translation_remove']);
    }

    /**
     * @return iterable<string,array{
     *     \Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog,
     *     array{\Ibexa\Bundle\ProductCatalog\UI\Language}
     * }>
     */
    public function providerForGetTemplateParameters(): iterable
    {
        $catalog = $this->getCatalog();

        yield 'translatable catalog' => [
            $catalog,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
            ],
        ];

        $multiLanguageCatalog = $this->getCatalog(['eng-GB', 'pol-PL']);

        yield 'translatable catalog with multiple languages' => [
            $multiLanguageCatalog,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
                new UILanguage(new Language(['id' => 4,  'languageCode' => 'pol-PL'])),
            ],
        ];
    }

    /**
     * @param string[] $languageCodes
     */
    private function getCatalog(array $languageCodes = ['eng-GB']): Catalog
    {
        return new Catalog(
            self::CATALOG_ID,
            self::CATALOG_IDENTIFIER,
            'name',
            $languageCodes,
            $this->getTestUser(),
            1647865210,
            1647865251,
            'draft',
            new LogicalAnd([]),
            'description',
        );
    }

    public function getParameterKey(): string
    {
        return 'catalog';
    }

    public function getTranslationTabName(): string
    {
        return TranslationsTab::class;
    }

    private function getTestUser(): User
    {
        return $this->createMock(User::class);
    }
}
