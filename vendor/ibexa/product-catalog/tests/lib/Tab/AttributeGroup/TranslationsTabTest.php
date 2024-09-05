<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab\AttributeGroup;

use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Tab\AttributeGroup\TranslationsTab;
use Ibexa\Tests\ProductCatalog\Tab\BaseTranslationsTabTest;
use stdClass;
use Symfony\Component\Form\FormView;

final class TranslationsTabTest extends BaseTranslationsTabTest
{
    /**
     * @return iterable<string,array{mixed,bool}>
     */
    public function providerForEvaluate(): iterable
    {
        yield 'translatable attribute group' => [
            new AttributeGroup(1, 'foo', 'name', 0, [], []),
            true,
        ];

        yield 'non-translatable attribute group' => [
            $this->getNonTranslatableAttributeGroup(),
            false,
        ];

        yield 'string' => [
            $this->getNonTranslatableAttributeGroup(),
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

    private function getNonTranslatableAttributeGroup(): AttributeGroupInterface
    {
        return new class() implements AttributeGroupInterface {
            public function getName(): string
            {
                return '';
            }

            public function getIdentifier(): string
            {
                return '';
            }

            public function getPosition(): int
            {
                return 0;
            }
        };
    }

    /**
     * @dataProvider providerForGetTemplateParameters
     *
     * @param array<\Ibexa\Bundle\ProductCatalog\UI\Language> $expectedTranslations
     */
    public function testGetTemplateParameters(
        AttributeGroup $attributeGroup,
        array $expectedTranslations
    ): void {
        $templateParameters = $this->translationTab->getTemplateParameters(['attribute_group' => $attributeGroup]);
        self::assertEquals(
            $attributeGroup,
            $templateParameters['attribute_group']
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
     *     \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup,
     *     array{\Ibexa\Bundle\ProductCatalog\UI\Language}
     * }>
     */
    public function providerForGetTemplateParameters(): iterable
    {
        $attributeGroup = $this->getAttributeGroup();

        yield 'translatable attribute group' => [
            $attributeGroup,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
            ],
        ];

        $multiLanguageAttributeGroup = $this->getAttributeGroup(['eng-GB', 'pol-PL']);

        yield 'translatable attribute group with multiple languages' => [
            $multiLanguageAttributeGroup,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
                new UILanguage(new Language(['id' => 4,  'languageCode' => 'pol-PL'])),
            ],
        ];
    }

    /**
     * @param string[] $languageCodes
     */
    private function getAttributeGroup(array $languageCodes = ['eng-GB']): AttributeGroup
    {
        return new AttributeGroup(1, 'foo', 'name', 0, $languageCodes, []);
    }

    public function getParameterKey(): string
    {
        return 'attribute_group';
    }

    public function getTranslationTabName(): string
    {
        return TranslationsTab::class;
    }
}
