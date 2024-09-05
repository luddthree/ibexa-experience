<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\HandlerInterface as AttributeHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\HandlerInterface as ProductHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\AttributeIndexDataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use ReflectionClass;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\AttributeIndexDataProvider
 */
final class AttributeIndexDataProviderTest extends TestCase
{
    private const ATTRIBUTE_TYPE = '__attribute_type__';

    private AttributeIndexDataProvider $attributeIndexDataProvider;

    /** @var \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed> */
    private Attribute $attribute;

    /** @var \Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private AttributeDefinitionHandlerInterface $attributeDefinitionHandler;

    private TestLogger $logger;

    protected function setUp(): void
    {
        $productHandler = $this->createMock(ProductHandlerInterface::class);
        $attributeHandler = $this->createMock(AttributeHandlerInterface::class);
        $this->attributeDefinitionHandler = $this->createMock(AttributeDefinitionHandlerInterface::class);

        $productHandler
            ->method('findByCode')
            ->willReturn(new Product(['id' => 42]));

        $this->attribute = new Attribute(1, 66, 'integer', null);
        $attributeHandler
            ->method('findByProduct')
            ->willReturn([$this->attribute]);

        $attributeDefinition = new AttributeDefinition();
        $attributeDefinition->type = self::ATTRIBUTE_TYPE;
        $attributeDefinition->identifier = 'foo_attribute_definition_identifier';
        $this->attributeDefinitionHandler
            ->method('load')
            ->with(66)
            ->willReturn($attributeDefinition);

        $this->logger = new TestLogger();
        $this->attributeIndexDataProvider = new AttributeIndexDataProvider(
            $productHandler,
            $attributeHandler,
            $this->attributeDefinitionHandler,
            [],
            $this->logger,
        );
    }

    public function testReturnsSearchFieldsFromSpecificProvider(): void
    {
        $spiContent = $this->createMockSpiContent();
        $this->setAttributeValue(true);

        $attributeIndexDataProvider = $this->createMock(IndexDataProviderInterface::class);
        $this->attributeIndexDataProvider->addIndexDataProvider(
            self::ATTRIBUTE_TYPE,
            $attributeIndexDataProvider,
        );

        $searchField1 = $this->createMock(Field::class);
        $searchField2 = $this->createMock(Field::class);
        $attributeIndexDataProvider
            ->method('getFieldsForAttribute')
            ->willReturn([
                $searchField1,
                $searchField2,
            ]);

        $searchFields = $this->attributeIndexDataProvider->getSearchData($spiContent);

        self::assertCount(2, $searchFields);
        self::assertSame($searchField1, $searchFields[0]);
        self::assertSame($searchField2, $searchFields[1]);
    }

    public function testThrowsIfAttributeDoesNotHaveAnIndexProvider(): void
    {
        $this->setAttributeValue(true);
        $spiContent = $this->createMockSpiContent();

        self::assertTrue($this->attributeIndexDataProvider->isSupported($spiContent));

        $message = sprintf(
            'Unable to find index data provider for attribute "foo_attribute_definition_identifier" of type '
            . '"%s". Ensure that service tagged with ibexa.product_catalog.attribute.index_data_provider exists for it.',
            self::ATTRIBUTE_TYPE,
        );
        self::assertFalse(
            $this->logger->hasInfoThatContains($message),
            sprintf('"%s" info message should not be present in the logger.', $message),
        );
        $this->attributeIndexDataProvider->getSearchData($spiContent);
        self::assertTrue(
            $this->logger->hasInfoThatContains($message),
            sprintf('"%s" info message should be present in the logger.', $message),
        );
    }

    /**
     * @param mixed $value
     */
    private function setAttributeValue($value): void
    {
        $reflClass = new ReflectionClass($this->attribute);
        $reflProperty = $reflClass->getProperty('value');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->attribute, $value);
    }

    private function createMockSpiContent(): Content
    {
        $spiContent = new Content();
        $spiContent->fields = [
            new Content\Field([
                'type' => 'ibexa_product_specification',
                'value' => new FieldValue([
                    'externalData' => [
                        'code' => 'foo',
                    ],
                ]),
            ]),
        ];

        return $spiContent;
    }
}
