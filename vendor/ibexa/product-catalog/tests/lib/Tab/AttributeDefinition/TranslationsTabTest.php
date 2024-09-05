<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab\AttributeDefinition;

use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use Ibexa\ProductCatalog\Tab\AttributeDefinition\TranslationsTab;
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
        yield 'translatable attribute definition' => [
            new AttributeDefinition(
                1,
                'foo',
                $this->createMock(AttributeTypeInterface::class),
                $this->createMock(AttributeGroupInterface::class),
                'name',
                0,
                [],
                null,
                [],
                [],
            ),
            true,
        ];

        yield 'non-translatable attribute definition' => [
            $this->getNonTranslatableAttributeDefinition(),
            false,
        ];

        yield 'string' => [
            $this->getNonTranslatableAttributeDefinition(),
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

    private function getNonTranslatableAttributeDefinition(): AttributeDefinitionInterface
    {
        return new class() implements AttributeDefinitionInterface {
            public function getId(): int
            {
                return 1;
            }

            public function getName(): string
            {
                return 'name';
            }

            public function getIdentifier(): string
            {
                return 'identifier';
            }

            public function getPosition(): int
            {
                return 0;
            }

            public function getDescription(): ?string
            {
                return 'description';
            }

            public function getType(): AttributeTypeInterface
            {
                return new AttributeType('identifier');
            }

            public function getGroup(): AttributeGroupInterface
            {
                return new AttributeGroup(1, 'identifier', 'name', 0, [], []);
            }

            public function getOptions(): OptionsBag
            {
                return new AttributeDefinitionOptions([]);
            }
        };
    }

    /**
     * @dataProvider providerForGetTemplateParameters
     *
     * @param array<\Ibexa\Bundle\ProductCatalog\UI\Language> $expectedTranslations
     */
    public function testGetTemplateParameters(
        AttributeDefinition $attributeDefinition,
        array $expectedTranslations
    ): void {
        $templateParameters = $this->translationTab->getTemplateParameters(['attribute_definition' => $attributeDefinition]);
        self::assertEquals(
            $attributeDefinition,
            $templateParameters['attribute_definition']
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
     *     \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition,
     *     array{\Ibexa\Bundle\ProductCatalog\UI\Language}
     * }>
     */
    public function providerForGetTemplateParameters(): iterable
    {
        $attributeDefinition = $this->getAttributeDefinition();

        yield 'translatable attribute definition' => [
            $attributeDefinition,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
            ],
        ];

        $multiLanguageAttributeDefinition = $this->getAttributeDefinition(['eng-GB', 'pol-PL']);

        yield 'translatable attribute definition with multiple languages' => [
            $multiLanguageAttributeDefinition,
            [
                new UILanguage(new Language(['id' => 2,  'languageCode' => 'eng-GB'])),
                new UILanguage(new Language(['id' => 4,  'languageCode' => 'pol-PL'])),
            ],
        ];
    }

    /**
     * @param string[] $languageCodes
     */
    private function getAttributeDefinition(array $languageCodes = ['eng-GB']): AttributeDefinition
    {
        return new AttributeDefinition(
            1,
            'foo',
            $this->createMock(AttributeTypeInterface::class),
            $this->createMock(AttributeGroupInterface::class),
            'name',
            0,
            $languageCodes,
            null,
            [],
            [],
        );
    }

    public function getParameterKey(): string
    {
        return 'attribute_definition';
    }

    public function getTranslationTabName(): string
    {
        return TranslationsTab::class;
    }
}
