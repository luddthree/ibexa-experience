<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\AttributeGroupTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class AttributeGroupTransformerTest extends TestCase
{
    private const EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER = 'dimensions';

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupTransformer $transformer;

    protected function setUp(): void
    {
        $this->attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $this->transformer = new AttributeGroupTransformer($this->attributeGroupService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?AttributeGroupInterface $value, ?string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $attributeGroup->method('getIdentifier')->willReturn(self::EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER);

        yield 'null' => [null, null];
        yield 'Attribute Group' => [$attributeGroup, self::EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . AttributeGroupInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with(self::EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER)
            ->willReturn($attributeGroup);

        self::assertEquals(
            $attributeGroup,
            $this->transformer->reverseTransform(self::EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER)
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

        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with(self::EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_ATTRIBUTE_GROUP_IDENTIFIER);
    }

    /**
     * @return iterable<string,mixed>
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
