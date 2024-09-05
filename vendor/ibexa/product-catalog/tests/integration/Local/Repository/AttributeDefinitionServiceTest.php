<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\AttributeGroupIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\SortClause\FieldValueSortClause;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\AttributeDefinitionService
 *
 * @group attribute-definition-service
 */
final class AttributeDefinitionServiceTest extends BaseAttributeDefinitionServiceTest
{
    private const ATTRIBUTE_DEFINITION_TRANSLATED_NAME = 'la traduction';

    public function testGetAttributeDefinition(): void
    {
        $attributeDefinition = self::getAttributeDefinitionService()->getAttributeDefinition('foo');

        self::assertEquals('foo', $attributeDefinition->getIdentifier());
        self::assertEquals('Foo', $attributeDefinition->getName());
        self::assertEquals('Description foo', $attributeDefinition->getDescription());
        self::assertEquals('dimensions', $attributeDefinition->getGroup()->getIdentifier());
        self::assertEquals('integer', $attributeDefinition->getType()->getIdentifier());
        self::assertEquals('0', $attributeDefinition->getPosition());
        self::assertEquals([
            'min' => 10,
            'max' => 100,
        ], $attributeDefinition->getOptions()->all());
    }

    public function testGetAttributeDefinitionNonExistingDefinition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition' with identifier 'non-existing'");

        self::getAttributeDefinitionService()->getAttributeDefinition('non-existing');
    }

    /**
     * @dataProvider dataProviderForTestFindAttributesDefinitions
     *
     * @param string[] $expectedIdentifiers
     */
    public function testFindAttributesDefinitions(
        ?AttributeDefinitionQuery $query,
        array $expectedIdentifiers,
        int $expectedTotalCount
    ): void {
        $this->assertAttributeDefinitionList(
            self::getAttributeDefinitionService()->findAttributesDefinitions($query),
            $expectedIdentifiers,
            $expectedTotalCount
        );
    }

    public function testFindAttributesDefinitionsWithAttributeGroupFilter(): void
    {
        $query = new AttributeDefinitionQuery(
            new AttributeGroupIdentifierCriterion('dimensions'),
        );

        $this->assertAttributeDefinitionList(
            self::getAttributeDefinitionService()->findAttributesDefinitions($query),
            ['foo', 'bar', 'baz'],
            3
        );
    }

    /**
     * @return iterable<array{AttributeDefinitionQuery,string[],int}>
     */
    public function dataProviderForTestFindAttributesDefinitions(): iterable
    {
        yield 'empty' => [
            new AttributeDefinitionQuery(),
            ['foo', 'foobar', 'bar', 'foobarbaz', 'foobaz', 'baz', 'empty', 'foo_boolean', 'foo_color', 'foo_integer', 'foo_float', 'foo_selection'],
            12,
        ];

        $query = new AttributeDefinitionQuery(
            new NameCriterion('Ba', 'STARTS_WITH'),
        );

        yield 'filter by prefix' => [
            $query,
            ['bar', 'baz'],
            2,
        ];

        $query = new AttributeDefinitionQuery(
            new NameCriterion('ba', 'STARTS_WITH'),
        );

        yield 'filter by prefix (case insensitivity)' => [
            $query,
            ['bar', 'baz'],
            2,
        ];

        yield 'pagination' => [
            new AttributeDefinitionQuery(
                null,
                [new FieldValueSortClause('identifier')],
                2,
                1
            ),
            ['bar', 'foobar'],
            12,
        ];
    }

    public function testCreateAttributeDefinition(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $createStruct = $attributeDefinitionService->newAttributeDefinitionCreateStruct('custom');
        $createStruct->setName('eng-US', 'Custom');
        $createStruct->setDescription('eng-US', 'Custom description');
        $createStruct->setName('pol-PL', 'Niestandardowa');
        $createStruct->setDescription('pol-PL', 'Niestandardowy opis');
        $createStruct->setGroup($this->getAttributeGroup());
        $createStruct->setType(new AttributeType('integer'));
        $createStruct->setPosition(4);
        $createStruct->setOptions(['min' => 1, 'max' => 10]);

        $attributeDefinition = $attributeDefinitionService->createAttributeDefinition($createStruct);

        self::assertEquals('custom', $attributeDefinition->getIdentifier());
        self::assertEquals('Custom', $attributeDefinition->getName());
        self::assertEquals('Custom description', $attributeDefinition->getDescription());
        self::assertEquals('dimensions', $attributeDefinition->getGroup()->getIdentifier());
        self::assertEquals('integer', $attributeDefinition->getType()->getIdentifier());
        self::assertEquals(4, $attributeDefinition->getPosition());
        self::assertEquals(['min' => 1, 'max' => 10], $attributeDefinition->getOptions()->all());
    }

    /**
     * @dataProvider dataProviderForTestCreateAttributeDefinitionValidatesCreateStruct
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testCreateAttributeDefinitionValidatesCreateStruct(
        AttributeDefinitionCreateStruct $createStruct,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $attributeDefinitionService->createAttributeDefinition($createStruct);
    }

    /**
     * @return iterable<array{AttributeDefinitionCreateStruct,class-string<\Throwable>,string}>
     */
    public function dataProviderForTestCreateAttributeDefinitionValidatesCreateStruct(): iterable
    {
        yield 'empty identifier' => [
            $this->createCreateStruct(),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'empty name' => [
            $this->createCreateStruct('lorem', 'eng-GB', ''),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'eng-GB' => '',\n)' is incorrect value",
        ];

        yield 'invalid language' => [
            $this->createCreateStruct('lorem', 'non-existing-language'),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'non-existing-language' => 'attribute_A',\n)' is incorrect value",
        ];

        yield 'invalid description language' => [
            $this->createCreateStruct('lorem', 'eng-GB', 'attribute_A', 'non-existing-language'),
            InvalidArgumentException::class,
            "Argument 'descriptions' is invalid: 'array (\n  'non-existing-language' => 'Description of attribute A',\n)' is incorrect value",
        ];

        yield 'invalid attribute group' => [
            $this->createCreateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getInvalidAttributeGroup()
            ),
            InvalidArgumentException::class,
            sprintf("Argument 'group' is invalid: value must be of type '%s'", AttributeGroup::class),
        ];

        yield 'negative position' => [
            $this->createCreateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                -2
            ),
            InvalidArgumentException::class,
            "Argument 'position' is invalid",
        ];

        yield 'too long identifier' => [
            $this->createCreateStruct(
                str_repeat('X', 65),
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                10
            ),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'too long name' => [
            $this->createCreateStruct(
                'lorem',
                'eng-GB',
                str_repeat('X', 191),
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                10
            ),
            InvalidArgumentException::class,
            "Argument 'names' is invalid",
        ];

        yield 'too long description' => [
            $this->createCreateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                str_repeat('X', 10001),
                $this->getAttributeGroup(),
                10
            ),
            InvalidArgumentException::class,
            "Argument 'descriptions' is invalid",
        ];

        $exceptionMessage = <<<END
        Argument 'options' is invalid: 'array (
          'min' => 10,
          'max' => 1,
        )' is incorrect value
        END;

        yield 'invalid options' => [
            $this->createCreateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                0,
                [
                    'min' => 10,
                    'max' => 1,
                ]
            ),
            InvalidArgumentException::class,
            $exceptionMessage,
        ];
    }

    /**
     * @param array<string,mixed> $options
     */
    private function createCreateStruct(
        string $identifier = '',
        string $languageCode = 'eng-GB',
        string $name = 'attribute_A',
        string $descriptionLanguageCode = 'eng-GB',
        string $description = 'Description of attribute A',
        AttributeGroupInterface $attributeGroup = null,
        int $position = 0,
        array $options = []
    ): AttributeDefinitionCreateStruct {
        $attributeGroup ??= $this->getAttributeGroup();

        $createStruct = new AttributeDefinitionCreateStruct($identifier);
        $createStruct->setName($languageCode, $name);
        $createStruct->setDescription($descriptionLanguageCode, $description);
        $createStruct->setGroup($attributeGroup);
        $createStruct->setType(new AttributeType('integer'));
        $createStruct->setPosition($position);
        $createStruct->setOptions($options);

        return $createStruct;
    }

    public function testCreateAttributeDefinitionWithDuplicatedIdentifier(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $createStruct = new AttributeDefinitionCreateStruct('foo');
        $createStruct->setGroup($this->getAttributeGroup());
        $createStruct->setType(new AttributeType('integer'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument 'createStruct' is invalid: An Attribute Definition with the provided identifier already exists");

        $attributeDefinitionService->createAttributeDefinition($createStruct);
    }

    public function testDeleteAttributeDefinition(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();
        $attributeDefinitionService->deleteAttributeDefinition(
            $attributeDefinitionService->getAttributeDefinition('empty')
        );

        try {
            $attributeDefinitionService->getAttributeDefinition('empty');
            $isDeleted = false;
        } catch (NotFoundException $e) {
            $isDeleted = true;
        }

        self::assertTrue($isDeleted);
    }

    public function testNewAttributeDefinitionCreateStruct(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();
        $attributeDefinitionCreateStruct = $attributeDefinitionService->newAttributeDefinitionCreateStruct('foo');

        self::assertEquals('foo', $attributeDefinitionCreateStruct->getIdentifier());
    }

    public function testUpdateAttributeDefinition(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition('foo');

        $updateStruct = $this->getUpdateStruct($attributeDefinition);

        $attributeDefinition = $attributeDefinitionService->updateAttributeDefinition($attributeDefinition, $updateStruct);

        self::assertEquals('updated', $attributeDefinition->getIdentifier());
        self::assertEquals('Updated', $attributeDefinition->getName());
        self::assertEquals('Updated description', $attributeDefinition->getDescription());
        self::assertEquals('fabric', $attributeDefinition->getGroup()->getIdentifier());
        self::assertEquals(1, $attributeDefinition->getPosition());
        self::assertEquals(['min' => -10, 'max' => 0], $attributeDefinition->getOptions()->all());
    }

    /**
     * @dataProvider dataProviderForTestUpdateAttributeDefinitionValidatesUpdateStruct
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testUpdateAttributeDefinitionValidatesUpdateStruct(
        AttributeDefinitionUpdateStruct $updateStruct,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $attributeDefinitionService->updateAttributeDefinition(
            $attributeDefinitionService->getAttributeDefinition('foo'),
            $updateStruct
        );
    }

    /**
     * @return iterable<array{AttributeDefinitionUpdateStruct,class-string<\Throwable>,string}>
     */
    public function dataProviderForTestUpdateAttributeDefinitionValidatesUpdateStruct(): iterable
    {
        yield 'empty identifier' => [
            $this->createUpdateStruct(),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'empty name' => [
            $this->createUpdateStruct('lorem', 'eng-GB', ''),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'eng-GB' => '',\n)' is incorrect value",
        ];

        yield 'invalid language' => [
            $this->createUpdateStruct('lorem', 'non-existing-language'),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'non-existing-language' => 'attribute_A',\n)' is incorrect value",
        ];

        yield 'invalid description language' => [
            $this->createUpdateStruct('lorem', 'eng-GB', 'attribute_A', 'non-existing-language'),
            InvalidArgumentException::class,
            "Argument 'descriptions' is invalid: 'array (\n  'non-existing-language' => 'Description of attribute A',\n)' is incorrect value",
        ];

        yield 'invalid attribute group' => [
            $this->createUpdateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getInvalidAttributeGroup()
            ),
            InvalidArgumentException::class,
            sprintf("Argument 'group' is invalid: value must be of type '%s'", AttributeGroup::class),
        ];

        yield 'negative position' => [
            $this->createUpdateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                -2
            ),
            InvalidArgumentException::class,
            "Argument 'position' is invalid",
        ];

        yield 'too long identifier' => [
            $this->createUpdateStruct(
                str_repeat('X', 65),
                'eng-GB',
                'attribute_A',
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                10
            ),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'too long name' => [
            $this->createUpdateStruct(
                'lorem',
                'eng-GB',
                str_repeat('X', 191),
                'eng-GB',
                'Description of attribute A',
                $this->getAttributeGroup(),
                10
            ),
            InvalidArgumentException::class,
            "Argument 'names' is invalid",
        ];

        yield 'too long description' => [
            $this->createUpdateStruct(
                'lorem',
                'eng-GB',
                'attribute_A',
                'eng-GB',
                str_repeat('X', 10001),
                $this->getAttributeGroup(),
                10
            ),
            InvalidArgumentException::class,
            "Argument 'descriptions' is invalid",
        ];
    }

    private function createUpdateStruct(
        string $identifier = '',
        string $languageCode = 'eng-GB',
        string $name = 'attribute_A',
        string $descriptionLanguageCode = 'eng-GB',
        string $description = 'Description of attribute A',
        AttributeGroupInterface $attributeGroup = null,
        int $position = 0
    ): AttributeDefinitionUpdateStruct {
        $attributeGroup ??= $this->getAttributeGroup();

        $updateStruct = new AttributeDefinitionUpdateStruct();
        $updateStruct->setIdentifier($identifier);
        $updateStruct->setName($languageCode, $name);
        $updateStruct->setDescription($descriptionLanguageCode, $description);
        $updateStruct->setGroup($attributeGroup);
        $updateStruct->setPosition($position);

        return $updateStruct;
    }

    public function testUpdateAttributeDefinitionWithDuplicatedIdentifier(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition('foo');

        $updateStruct = $attributeDefinitionService->newAttributeDefinitionUpdateStruct($attributeDefinition);
        $updateStruct->setIdentifier('bar');
        $updateStruct->setGroup($this->getAttributeGroup());
        $updateStruct->setPosition(self::ATTRIBUTE_DEFINITION_POSITION);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Argument 'updateStruct' is invalid: An Attribute Definition with the provided identifier already exists"
        );

        $attributeDefinitionService->updateAttributeDefinition($attributeDefinition, $updateStruct);
    }

    public function testDeleteAttributeDefinitionTranslation(): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();
        $languageService = self::getLanguageService();
        $translationLanguage = $languageService->loadLanguage('fre-FR');
        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition('foo');

        $updateStruct = $attributeDefinitionService->newAttributeDefinitionUpdateStruct($attributeDefinition);
        $updateStruct->setIdentifier('foo');
        $updateStruct->setGroup($this->getAttributeGroup());
        $updateStruct->setNames([
            $translationLanguage->languageCode => self::ATTRIBUTE_DEFINITION_TRANSLATED_NAME,
        ]);
        $updateStruct->setDescriptions([
            $translationLanguage->languageCode => self::ATTRIBUTE_DEFINITION_TRANSLATED_NAME,
        ]);
        $updateStruct->setPosition(self::ATTRIBUTE_DEFINITION_POSITION);

        $attributeDefinitionService->updateAttributeDefinition($attributeDefinition, $updateStruct);
        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition('foo', [$translationLanguage->languageCode]);

        self::assertSame(self::ATTRIBUTE_DEFINITION_TRANSLATED_NAME, $attributeDefinition->getName());
        $attributeDefinitionService->deleteAttributeDefinitionTranslation($attributeDefinition, $translationLanguage->languageCode);
        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition('foo', [$translationLanguage->languageCode]);

        self::assertSame('', $attributeDefinition->getName());
    }

    public function testIsAttributeDefinitionUsed(): void
    {
        $productService = self::getLocalProductService();

        $productCreateStruct = $productService->newProductCreateStruct(
            self::getProductTypeService()->getProductType('dress'),
            'eng-GB'
        );

        $productCreateStruct->setCode('code');
        $productCreateStruct->setField('name', 'foo');
        $productCreateStruct->setAttributes([
            'foo' => 10,
            'baz' => 10,
        ]);

        $product = $productService->createProduct($productCreateStruct);

        self::assertAttributeUsage('foo', true);
        self::assertAttributeUsage('bar', true);
        self::assertAttributeUsage('baz', true);
        self::assertAttributeUsage('foobar', false);
        self::assertAttributeUsage('foobaz', false);
        self::assertAttributeUsage('empty', false);

        $productService->deleteProduct($product);
    }

    private static function assertAttributeUsage(string $identifier, bool $isUsed): void
    {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $attributeDefinition = $attributeDefinitionService->getAttributeDefinition($identifier);
        self::assertSame($isUsed, $attributeDefinitionService->isAttributeDefinitionUsed($attributeDefinition));
    }

    private function getInvalidAttributeGroup(): AttributeGroupInterface
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
     * @param string[] $expectedIdentifiers
     */
    private function assertAttributeDefinitionList(
        AttributeDefinitionListInterface $attributeDefinitionList,
        array $expectedIdentifiers,
        int $expectedTotalCount
    ): void {
        $actualIdentifiers = [];
        foreach ($attributeDefinitionList as $attributeDefinition) {
            $actualIdentifiers[] = $attributeDefinition->getIdentifier();
        }

        self::assertEqualsCanonicalizing($expectedIdentifiers, $actualIdentifiers);
        self::assertEquals($expectedTotalCount, $attributeDefinitionList->getTotalCount());
    }
}
