<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\AttributeGroupService
 *
 * @group attribute-group-service
 */
final class AttributeGroupServiceTest extends BaseAttributeGroupServiceTest
{
    private const ATTRIBUTE_GROUP_IDENTIFIER = 'dimensions';
    private const EMPTY_ATTRIBUTE_GROUP_IDENTIFIER = 'empty';
    private const TOTAL_ATTRIBUTE_GROUP_COUNT = 7;

    public function testGetAttributeGroup(): void
    {
        $attributeGroup = self::getAttributeGroupService()->getAttributeGroup(self::ATTRIBUTE_GROUP_IDENTIFIER);

        self::assertEquals(self::ATTRIBUTE_GROUP_IDENTIFIER, $attributeGroup->getIdentifier());
        self::assertEquals('Dimensions', $attributeGroup->getName());
        self::assertEquals(0, $attributeGroup->getPosition());
    }

    public function testGetAttributeGroupNonExistingGroup(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup' with identifier 'non-existing'");

        self::getAttributeGroupService()->getAttributeGroup('non-existing');
    }

    /**
     * @dataProvider dataProviderForTestGetAttributeGroups
     *
     * @param string[] $expectedIdentifiers
     */
    public function testFindAttributeGroups(
        ?AttributeGroupQuery $query,
        array $expectedIdentifiers,
        int $expectedTotalCount
    ): void {
        $attributeGroupList = self::getAttributeGroupService()->findAttributeGroups($query);

        $actualIdentifiers = [];
        foreach ($attributeGroupList as $attributeGroup) {
            $actualIdentifiers[] = $attributeGroup->getIdentifier();
        }

        self::assertEqualsCanonicalizing($expectedIdentifiers, $actualIdentifiers);
        self::assertEquals($expectedTotalCount, $attributeGroupList->getTotalCount());
    }

    /**
     * @return iterable<array{AttributeGroupQuery,string[],int}>
     */
    public function dataProviderForTestGetAttributeGroups(): iterable
    {
        yield 'empty' => [
            new AttributeGroupQuery(),
            [self::ATTRIBUTE_GROUP_IDENTIFIER, 'dress_size', 'fabric', 'foo_group', 'fastener', 'empty', 'shoes_size'],
            self::TOTAL_ATTRIBUTE_GROUP_COUNT,
        ];

        yield 'filter by prefix' => [
            new AttributeGroupQuery('Siz'),
            ['dress_size', 'shoes_size'],
            2,
        ];

        yield 'filter by prefix (case insensitivity)' => [
            new AttributeGroupQuery('siz'),
            ['dress_size', 'shoes_size'],
            2,
        ];

        yield 'pagination' => [
            new AttributeGroupQuery(null, 1, 2),
            ['dress_size', 'fabric'],
            self::TOTAL_ATTRIBUTE_GROUP_COUNT,
        ];
    }

    public function testCreateAttributeGroup(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();

        $createStruct = $attributeGroupService->newAttributeGroupCreateStruct('custom');
        $createStruct->setNames([
            'eng-US' => 'Custom',
            'pol-PL' => 'Niestandardowa',
        ]);
        $createStruct->setPosition(42);

        $attributeGroup = $attributeGroupService->createAttributeGroup($createStruct);

        self::assertEquals('custom', $attributeGroup->getIdentifier());
        self::assertEquals('Custom', $attributeGroup->getName());
        self::assertEquals(42, $attributeGroup->getPosition());
    }

    /**
     * @dataProvider dataProviderForTestCreateAttributeGroupValidatesCreateStruct
     *
     *  @param class-string<\Throwable> $exceptionClass
     */
    public function testCreateAttributeGroupValidatesCreateStruct(
        AttributeGroupCreateStruct $createStruct,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroupService->createAttributeGroup($createStruct);
    }

    /**
     * @return iterable<array{AttributeGroupCreateStruct,class-string<\Throwable>,string}>
     */
    public function dataProviderForTestCreateAttributeGroupValidatesCreateStruct(): iterable
    {
        yield 'empty identifier' => [
            new AttributeGroupCreateStruct(''),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'too long identifier' => [
            new AttributeGroupCreateStruct(str_repeat('X', 65)),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'empty name' => [
            new AttributeGroupCreateStruct('foo', ['eng-GB' => '']),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'eng-GB' => '',\n)' is incorrect value",
        ];

        yield 'too long name' => [
            new AttributeGroupCreateStruct('foo', ['eng-GB' => str_repeat('X', 191)]),
            InvalidArgumentException::class,
            "Argument 'names' is invalid",
        ];

        yield 'invalid language' => [
            new AttributeGroupCreateStruct('foo', [
                'non-existing' => 'Foo',
            ]),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'non-existing' => 'Foo',\n)' is incorrect value",
        ];

        yield 'negative position' => [
            new AttributeGroupCreateStruct('foo', ['eng-GB' => 'Foo'], -42),
            InvalidArgumentException::class,
            "Argument 'position' is invalid",
        ];
    }

    public function testCreateAttributeGroupWithDuplicatedIdentifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument 'createStruct' is invalid: An Attribute Group with the provided identifier already exists");

        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroupService->createAttributeGroup(new AttributeGroupCreateStruct(self::ATTRIBUTE_GROUP_IDENTIFIER));
    }

    public function testDeleteAttributeGroup(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroupService->deleteAttributeGroup($attributeGroupService->getAttributeGroup(self::EMPTY_ATTRIBUTE_GROUP_IDENTIFIER));

        try {
            $attributeGroupService->getAttributeGroup(self::EMPTY_ATTRIBUTE_GROUP_IDENTIFIER);
            $isDeleted = false;
        } catch (NotFoundException $e) {
            $isDeleted = true;
        }

        self::assertTrue($isDeleted);
    }

    public function testDeleteAttributeGroupWillThrowExceptionWhenInUse(): void
    {
        $productService = self::getLocalProductService();

        $attributeGroupService = self::getLocalAttributeGroupService();
        $product = $this->createProduct();
        $group = $attributeGroupService->getAttributeGroup('dimensions');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument 'group' is invalid: An Attribute Group is in use");

        $attributeGroupService->deleteAttributeGroup($group);

        $productService->deleteProduct($product);
    }

    public function testNewAttributeGroupCreateStruct(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroupCreateStruct = $attributeGroupService->newAttributeGroupCreateStruct('foo');

        self::assertEquals('foo', $attributeGroupCreateStruct->getIdentifier());
    }

    public function testUpdateAttributeGroup(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();

        $attributeGroup = $attributeGroupService->getAttributeGroup(self::ATTRIBUTE_GROUP_IDENTIFIER);
        $updateStruct = $attributeGroupService->newAttributeGroupUpdateStruct($attributeGroup);
        $updateStruct->setIdentifier('updated');
        $updateStruct->setNames([
            'eng-US' => 'Updated',
        ]);
        $updateStruct->setPosition(42);

        $attributeGroup = $attributeGroupService->updateAttributeGroup($attributeGroup, $updateStruct);

        self::assertEquals('updated', $attributeGroup->getIdentifier());
        self::assertEquals('Updated', $attributeGroup->getName());
        self::assertEquals(42, $attributeGroup->getPosition());
    }

    /**
     * @dataProvider dataProviderForTestUpdateAttributeGroupValidatesCreateStruct
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testUpdateAttributeGroupValidatesCreateStruct(
        string $identifier,
        AttributeGroupUpdateStruct $updateStruct,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $attributeGroupService = self::getLocalAttributeGroupService();
        $attributeGroupService->updateAttributeGroup(
            $attributeGroupService->getAttributeGroup($identifier),
            $updateStruct
        );
    }

    /**
     * @return iterable<array{string,AttributeGroupUpdateStruct,class-string<\Throwable>,string}>
     */
    public function dataProviderForTestUpdateAttributeGroupValidatesCreateStruct(): iterable
    {
        yield 'empty identifier' => [
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            new AttributeGroupUpdateStruct(''),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'too long identifier' => [
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            new AttributeGroupUpdateStruct(str_repeat('X', 65)),
            InvalidArgumentException::class,
            "Argument 'identifier' is invalid",
        ];

        yield 'empty name' => [
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            new AttributeGroupUpdateStruct(null, ['eng-GB' => '']),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'eng-GB' => '',\n)' is incorrect value",
        ];

        yield 'too long name' => [
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            new AttributeGroupUpdateStruct(null, ['eng-GB' => str_repeat('X', 191)]),
            InvalidArgumentException::class,
            "Argument 'names' is invalid",
        ];

        yield 'invalid language' => [
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            new AttributeGroupUpdateStruct(null, ['non-existing' => 'Foo']),
            InvalidArgumentException::class,
            "Argument 'names' is invalid: 'array (\n  'non-existing' => 'Foo',\n)' is incorrect value",
        ];

        yield 'negative position' => [
            self::ATTRIBUTE_GROUP_IDENTIFIER,
            new AttributeGroupUpdateStruct('foo', ['eng-GB' => 'Foo'], -42),
            InvalidArgumentException::class,
            "Argument 'position' is invalid",
        ];
    }

    public function testUpdateAttributeGroupWithDuplicatedIdentifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument 'updateStruct' is invalid: An Attribute Group with the provided identifier already exists");

        $attributeGroupService = self::getLocalAttributeGroupService();

        $attributeGroup = $attributeGroupService->getAttributeGroup('dress_size');

        $updateStruct = $attributeGroupService->newAttributeGroupUpdateStruct($attributeGroup);
        $updateStruct->setIdentifier('shoes_size');

        $attributeGroupService->updateAttributeGroup($attributeGroup, $updateStruct);
    }

    public function testDeleteAttributeGroupTranslation(): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();
        $languageService = self::getLanguageService();
        $translationLanguage = $languageService->loadLanguage('fre-FR');
        $attributeGroup = $attributeGroupService->getAttributeGroup(self::ATTRIBUTE_GROUP_IDENTIFIER);

        $updateStruct = $attributeGroupService->newAttributeGroupUpdateStruct($attributeGroup);
        $updateStruct->setIdentifier(self::ATTRIBUTE_GROUP_IDENTIFIER);
        $updateStruct->setNames([
            $translationLanguage->languageCode => 'la traduction',
        ]);
        $updateStruct->setPosition(42);

        $attributeGroupService->updateAttributeGroup($attributeGroup, $updateStruct);
        $attributeGroup = $attributeGroupService->getAttributeGroup(self::ATTRIBUTE_GROUP_IDENTIFIER, [$translationLanguage]);

        self::assertSame('la traduction', $attributeGroup->getName());
        $attributeGroupService->deleteAttributeGroupTranslation($attributeGroup, $translationLanguage->languageCode);
        $attributeGroup = $attributeGroupService->getAttributeGroup(self::ATTRIBUTE_GROUP_IDENTIFIER, [$translationLanguage]);

        self::assertSame('', $attributeGroup->getName());
    }

    public function testIsAttributeGroupUsed(): void
    {
        $productService = self::getLocalProductService();

        $product = $this->createProduct();

        self::assertAttributeGroupUsage('dimensions', true);
        self::assertAttributeGroupUsage('shoes_size', false);

        $productService->deleteProduct($product);
    }

    private static function assertAttributeGroupUsage(string $identifier, bool $isUsed): void
    {
        $attributeGroupService = self::getLocalAttributeGroupService();

        $attributeGroup = $attributeGroupService->getAttributeGroup($identifier);
        self::assertSame(
            $isUsed,
            $attributeGroupService->isAttributeGroupUsed($attributeGroup),
            sprintf(
                'Attribute Group "%s" %s be used.',
                $identifier,
                $isUsed ? 'should' : 'should not',
            ),
        );
    }

    private function createProduct(): ProductInterface
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

        return $productService->createProduct($productCreateStruct);
    }
}
