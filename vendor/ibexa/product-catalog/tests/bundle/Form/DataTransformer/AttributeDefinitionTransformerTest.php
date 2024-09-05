<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\AttributeDefinitionTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\AttributeDefinitionTransformer
 */
final class AttributeDefinitionTransformerTest extends TestCase
{
    private const EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeDefinitionTransformer $transformer;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $this->transformer = new AttributeDefinitionTransformer($this->attributeDefinitionService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?AttributeDefinitionInterface $value, ?string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string, array{?\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface, ?string}>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $attributeDefinition->method('getIdentifier')->willReturn(self::EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER);

        yield 'null' => [null, null];
        yield 'Attribute Definition' => [$attributeDefinition, self::EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . AttributeDefinitionInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with(self::EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER)
            ->willReturn($attributeDefinition);

        self::assertEquals(
            $attributeDefinition,
            $this->transformer->reverseTransform(self::EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER)
        );
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformWithInvalidInputDataProvider
     *
     * @param mixed $value
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->transformer->reverseTransform($value);
    }

    /**
     * @return iterable<string,array{mixed}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInputDataProvider(): iterable
    {
        yield 'integer' => [123456];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleProductLoadFailure
     */
    public function testReverseTransformHandleProductLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with(self::EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_ATTRIBUTE_DEFINITION_IDENTIFIER);
    }

    /**
     * @return iterable<string, array{\Exception}>
     */
    public function dataProviderForTestReverseTransformHandleProductLoadFailure(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];

        yield UnauthorizedException::class => [
            $this->createMock(UnauthorizedException::class),
        ];
    }
}
