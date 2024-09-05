<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface as AttributeGroupHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute as SPIAttribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition as SPIAttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup as SPIAttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use Ibexa\ProductCatalog\Local\Repository\Variant\NameGeneratorInterface;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryProviderInterface;
use PHPUnit\Framework\TestCase;

final class DomainMapperTest extends TestCase
{
    private const LANGUAGE_CODE_EN = 'eng-GB';
    private const LANGUAGE_CODE_EN_NAME = 'name_en';
    private const LANGUAGE_CODE_PL = 'pol-PL';
    private const LANGUAGE_CODE_PL_NAME = 'name_pl';
    private const LANGUAGE_ID_EN = 2;
    private const LANGUAGE_ID_PL = 4;

    private const ATTRIBUTE_GROUP_ID = 1;
    private const ATTRIBUTE_GROUP_IDENTIFIER = 'attribute_group_identifier';
    private const ATTRIBUTE_GROUP_POSITION = 0;

    private const ATTRIBUTE_DEFINITION_ID = 1;
    private const ATTRIBUTE_DEFINITION_IDENTIFIER = 'attribute_definition_identifier';

    private const TYPE_IDENTIFIER = 'type_identifier';

    private DomainMapper $domainMapper;

    /** @var \Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $attributeGroupHandler;

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $attributeTypeService;

    /** @var \Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $attributeDefinitionHandler;

    protected function setUp(): void
    {
        $languageHandler = $this->createMock(LanguageHandlerInterface::class);
        $languageHandler
            ->method('loadListByLanguageCodes')
            ->with([self::LANGUAGE_CODE_EN, self::LANGUAGE_CODE_PL])
            ->willReturn([
                new Language(['id' => self::LANGUAGE_ID_EN, 'languageCode' => self::LANGUAGE_CODE_EN]),
                new Language(['id' => self::LANGUAGE_ID_PL, 'languageCode' => self::LANGUAGE_CODE_PL]),
            ]);
        $languageHandler
            ->method('loadList')
            ->willReturnCallback(static function (array $ids): array {
                $map = [
                    self::LANGUAGE_ID_PL => new Language([
                        'id' => self::LANGUAGE_ID_EN,
                        'languageCode' => self::LANGUAGE_CODE_EN,
                    ]),
                    self::LANGUAGE_ID_EN => new Language([
                        'id' => self::LANGUAGE_ID_PL,
                        'languageCode' => self::LANGUAGE_CODE_PL,
                    ]),
                ];

                return array_map(static fn (int $id): Language => $map[$id], $ids);
            });

        $languageResolver = $this->createMock(LanguageResolver::class);
        $languageResolver
            ->method('getPrioritizedLanguages')
            ->willReturn([self::LANGUAGE_CODE_EN, self::LANGUAGE_CODE_PL]);

        $this->attributeTypeService = $this->createMock(AttributeTypeServiceInterface::class);
        $this->attributeGroupHandler = $this->createMock(AttributeGroupHandlerInterface::class);
        $this->attributeDefinitionHandler = $this->createMock(AttributeDefinitionHandlerInterface::class);
        $productAvailabilityDelegate = $this->createMock(DomainMapper\ProductAvailabilityDelegate::class);
        $productPriceDelegate = $this->createMock(DomainMapper\ProductPriceDelegate::class);
        $productVariantsDelegate = $this->createMock(DomainMapper\ProductVariantsDelegate::class);

        $this->domainMapper = new DomainMapper(
            $languageHandler,
            $languageResolver,
            $this->attributeTypeService,
            $this->attributeGroupHandler,
            $this->attributeDefinitionHandler,
            $productAvailabilityDelegate,
            $productPriceDelegate,
            $productVariantsDelegate,
            $this->createMock(VatCategoryProviderInterface::class),
            $this->createMock(NameGeneratorInterface::class)
        );
    }

    /**
     * @dataProvider providerForCreateAttributeGroup
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[]|null $prioritizedLanguages
     */
    public function testCreateAttributeGroup(
        SPIAttributeGroup $spiAttributeGroup,
        ?iterable $prioritizedLanguages,
        AttributeGroup $expectedAttributeGroup
    ): void {
        self::assertEquals(
            $expectedAttributeGroup,
            $this->domainMapper->createAttributeGroup($spiAttributeGroup, $prioritizedLanguages)
        );
    }

    /**
     * @phpstan-return iterable<string,array{
     *     \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup,
     *     \Ibexa\Contracts\Core\Persistence\Content\Language[]|null,
     *     \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup
     * }>
     */
    public function providerForCreateAttributeGroup(): iterable
    {
        yield 'attribute group' => [
            new SPIAttributeGroup([
                'id' => 1,
                'identifier' => 'foo',
                'position' => 0,
                'names' => [
                    self::LANGUAGE_ID_EN => self::LANGUAGE_CODE_EN_NAME,
                    self::LANGUAGE_ID_PL => self::LANGUAGE_CODE_PL_NAME,
                ],
            ]),
            [new Language(['id' => self::LANGUAGE_ID_EN, 'languageCode' => self::LANGUAGE_CODE_EN])],
            new AttributeGroup(
                1,
                'foo',
                self::LANGUAGE_CODE_EN_NAME,
                0,
                [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
                [
                    self::LANGUAGE_CODE_PL => self::LANGUAGE_CODE_PL_NAME,
                    self::LANGUAGE_CODE_EN => self::LANGUAGE_CODE_EN_NAME,
                ],
            ),
        ];

        yield 'missing language in prioritizedLanguages' => [
            new SPIAttributeGroup([
                'id' => 1,
                'identifier' => 'foo',
                'position' => 0,
                'names' => [
                    self::LANGUAGE_ID_EN => self::LANGUAGE_CODE_EN_NAME,
                    self::LANGUAGE_ID_PL => self::LANGUAGE_CODE_PL_NAME,
                ],
            ]),
            [new Language(['id' => 8, 'languageCode' => 'ger-DE'])],
            new AttributeGroup(
                1,
                'foo',
                '',
                0,
                [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
                [
                    self::LANGUAGE_CODE_PL => self::LANGUAGE_CODE_PL_NAME,
                    self::LANGUAGE_CODE_EN => self::LANGUAGE_CODE_EN_NAME,
                ],
            ),
        ];

        yield 'null as prioritizedLanguages' => [
            new SPIAttributeGroup([
                'id' => 1,
                'identifier' => 'foo',
                'position' => 0,
                'names' => [
                    self::LANGUAGE_ID_EN => self::LANGUAGE_CODE_EN_NAME,
                    self::LANGUAGE_ID_PL => self::LANGUAGE_CODE_PL_NAME,
                ],
            ]),
            null,
            new AttributeGroup(
                1,
                'foo',
                self::LANGUAGE_CODE_EN_NAME,
                0,
                [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
                [
                    self::LANGUAGE_CODE_PL => self::LANGUAGE_CODE_PL_NAME,
                    self::LANGUAGE_CODE_EN => self::LANGUAGE_CODE_EN_NAME,
                ],
            ),
        ];
    }

    /**
     * @dataProvider providerForCreateAttributeDefinition
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[]|null $prioritizedLanguages
     */
    public function testCreateAttributeDefinition(
        SPIAttributeDefinition $spiAttributeDefinition,
        ?iterable $prioritizedLanguages,
        AttributeDefinition $expectedAttributeDefinition
    ): void {
        $this->attributeGroupHandler
            ->method('load')
            ->with(self::ATTRIBUTE_GROUP_ID)
            ->willReturn(new SPIAttributeGroup([
                'id' => self::ATTRIBUTE_GROUP_ID,
                'identifier' => self::ATTRIBUTE_GROUP_IDENTIFIER,
                'position' => self::ATTRIBUTE_GROUP_POSITION,
                'names' => [
                    self::LANGUAGE_ID_EN => self::LANGUAGE_CODE_EN_NAME,
                    self::LANGUAGE_ID_PL => self::LANGUAGE_CODE_PL_NAME,
                ],
            ]));

        $this->attributeTypeService
            ->method('getAttributeType')
            ->with(self::TYPE_IDENTIFIER)
            ->willReturn(new AttributeType(self::TYPE_IDENTIFIER));

        self::assertEquals(
            $expectedAttributeDefinition,
            $this->domainMapper->createAttributeDefinition($spiAttributeDefinition, $prioritizedLanguages)
        );
    }

    /**
     * @phpstan-return iterable<string,array{
     *     \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition,
     *     \Ibexa\Contracts\Core\Persistence\Content\Language[]|null,
     *     \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition
     * }>
     */
    public function providerForCreateAttributeDefinition(): iterable
    {
        $spiAttributeDefinition = new SPIAttributeDefinition();
        $spiAttributeDefinition->id = self::ATTRIBUTE_DEFINITION_ID;
        $spiAttributeDefinition->identifier = self::ATTRIBUTE_DEFINITION_IDENTIFIER;
        $spiAttributeDefinition->type = self::TYPE_IDENTIFIER;
        $spiAttributeDefinition->attributeGroupId = self::ATTRIBUTE_GROUP_ID;
        $spiAttributeDefinition->position = self::ATTRIBUTE_GROUP_POSITION;
        $spiAttributeDefinition->setName(self::LANGUAGE_ID_EN, self::LANGUAGE_CODE_EN_NAME);
        $spiAttributeDefinition->setName(self::LANGUAGE_ID_PL, self::LANGUAGE_CODE_PL_NAME);

        yield 'attribute definition' => [
            $spiAttributeDefinition,
            [new Language(['id' => self::LANGUAGE_ID_EN, 'languageCode' => self::LANGUAGE_CODE_EN])],
            new AttributeDefinition(
                self::ATTRIBUTE_DEFINITION_ID,
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                new AttributeType(self::TYPE_IDENTIFIER),
                $this->getAttributeGroup(self::LANGUAGE_CODE_EN_NAME),
                self::LANGUAGE_CODE_EN_NAME,
                self::ATTRIBUTE_GROUP_POSITION,
                [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
                null,
                [self::LANGUAGE_CODE_EN => self::LANGUAGE_CODE_EN_NAME],
                [self::LANGUAGE_CODE_EN => null],
            ),
        ];

        yield 'missing language in prioritizedLanguages' => [
            $spiAttributeDefinition,
            [new Language(['id' => 8, 'languageCode' => 'ger-DE'])],
            new AttributeDefinition(
                self::ATTRIBUTE_DEFINITION_ID,
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                new AttributeType(self::TYPE_IDENTIFIER),
                $this->getAttributeGroup(''),
                '',
                self::ATTRIBUTE_GROUP_POSITION,
                [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
                null,
                [],
                [],
            ),
        ];

        yield 'null as prioritizedLanguages' => [
            $spiAttributeDefinition,
            null,
            new AttributeDefinition(
                self::ATTRIBUTE_DEFINITION_ID,
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                new AttributeType(self::TYPE_IDENTIFIER),
                $this->getAttributeGroup(self::LANGUAGE_CODE_EN_NAME),
                self::LANGUAGE_CODE_EN_NAME,
                self::ATTRIBUTE_GROUP_POSITION,
                [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
                null,
                [
                    self::LANGUAGE_CODE_EN => self::LANGUAGE_CODE_EN_NAME,
                    self::LANGUAGE_CODE_PL => self::LANGUAGE_CODE_PL_NAME,
                ],
                [
                    self::LANGUAGE_CODE_EN => null,
                    self::LANGUAGE_CODE_PL => null,
                ],
            ),
        ];
    }

    private function getAttributeGroup(
        string $name
    ): AttributeGroup {
        $names = [
            self::LANGUAGE_CODE_PL => self::LANGUAGE_CODE_PL_NAME,
            self::LANGUAGE_CODE_EN => self::LANGUAGE_CODE_EN_NAME,
        ];
        $languages = [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN];

        return new AttributeGroup(
            self::ATTRIBUTE_GROUP_ID,
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            $name,
            self::ATTRIBUTE_GROUP_POSITION,
            $languages,
            $names,
        );
    }

    public function testAttributesAreSortedByPosition(): void
    {
        $this->setupAttributeHandler();

        $this->attributeGroupHandler
            ->method('load')
            ->with(self::ATTRIBUTE_GROUP_ID)
            ->willReturn(new SPIAttributeGroup([
                'id' => self::ATTRIBUTE_GROUP_ID,
                'identifier' => self::ATTRIBUTE_GROUP_IDENTIFIER,
                'position' => self::ATTRIBUTE_GROUP_POSITION,
            ]));

        $product = $this->domainMapper->createProduct(
            $this->createMock(ProductTypeInterface::class),
            $this->getProductContentWithSpecificationField(),
            'code'
        );

        self::assertEquals(['first', 'second', 'third'], array_keys((array)$product->getAttributes()));
    }

    public function testSPIAttributesAreSortedByPosition(): void
    {
        $this->setupAttributeHandler();

        $this->attributeGroupHandler
            ->method('load')
            ->with(self::ATTRIBUTE_GROUP_ID)
            ->willReturn(new SPIAttributeGroup([
                'id' => self::ATTRIBUTE_GROUP_ID,
                'identifier' => self::ATTRIBUTE_GROUP_IDENTIFIER,
                'position' => self::ATTRIBUTE_GROUP_POSITION,
            ]));

        $product = $this->domainMapper->createProduct(
            $this->createMock(ProductTypeInterface::class),
            $this->getProductContentWithSpecificationField(),
            'code',
            [
                new SPIAttribute(
                    1,
                    1,
                    'selection',
                    'attr 1 value'
                ),
                new SPIAttribute(
                    2,
                    2,
                    'selection',
                    'attr 2 value'
                ),
                new SPIAttribute(
                    3,
                    3,
                    'selection',
                    'attr 3 value'
                ),
            ]
        );

        self::assertEquals(['first', 'second', 'third'], array_keys((array)$product->getAttributes()));
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content&\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getProductContentWithSpecificationField(): Content
    {
        $content = $this->createMock(Content::class);
        $content->method(
            'getFields'
        )->willReturn(
            [
                new Field([
                    'fieldDefIdentifier' => 'specification',
                    'value' => new Value(
                        null,
                        null,
                        [
                            1 => 'attr 1 value',
                            2 => 'attr 2 value',
                            3 => 'attr 3 value',
                        ]
                    ),
                ]),
            ]
        );

        return $content;
    }

    protected function setupAttributeHandler(): void
    {
        $spiAttributeDefinition1 = new SPIAttributeDefinition();
        $spiAttributeDefinition1->id = 1;
        $spiAttributeDefinition1->type = self::TYPE_IDENTIFIER;
        $spiAttributeDefinition1->attributeGroupId = self::ATTRIBUTE_GROUP_ID;
        $spiAttributeDefinition1->identifier = 'first';
        $spiAttributeDefinition1->position = 0;

        $spiAttributeDefinition2 = new SPIAttributeDefinition();
        $spiAttributeDefinition2->id = 3;
        $spiAttributeDefinition2->type = self::TYPE_IDENTIFIER;
        $spiAttributeDefinition2->attributeGroupId = self::ATTRIBUTE_GROUP_ID;
        $spiAttributeDefinition2->identifier = 'third';
        $spiAttributeDefinition2->position = 10;

        $spiAttributeDefinition3 = new SPIAttributeDefinition();
        $spiAttributeDefinition3->id = 2;
        $spiAttributeDefinition3->type = self::TYPE_IDENTIFIER;
        $spiAttributeDefinition3->attributeGroupId = self::ATTRIBUTE_GROUP_ID;
        $spiAttributeDefinition3->identifier = 'second';
        $spiAttributeDefinition3->position = 5;

        $this->attributeDefinitionHandler->method('load')->willReturn(
            $spiAttributeDefinition1,
            $spiAttributeDefinition2,
            $spiAttributeDefinition3
        );
    }
}
