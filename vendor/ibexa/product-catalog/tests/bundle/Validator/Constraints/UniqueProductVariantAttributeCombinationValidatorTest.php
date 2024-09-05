<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use ArrayIterator;
use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductVariantData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductVariantAttributeCombination;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductVariantAttributeCombinationValidator;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class UniqueProductVariantAttributeCombinationValidatorTest extends ConstraintValidatorTestCase
{
    private const PRODUCT_CODE_1 = '<PRODUCT_CODE_1>';
    private const PRODUCT_CODE_2 = '<PRODUCT_CODE_2>';
    private const PRODUCT_CODE_3 = '<PRODUCT_CODE_3>';

    /** @var \Ibexa\Contracts\ProductCatalog\ProductServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ProductServiceInterface $productService;

    /** @var array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> */
    private array $existingProductVariants = [];

    protected function setUp(): void
    {
        $this->productService = $this->createMock(ProductServiceInterface::class);
        $list = $this->createMock(ProductVariantListInterface::class);
        $list
            ->method('getIterator')
            ->willReturnCallback(fn (): ArrayIterator => new ArrayIterator($this->existingProductVariants));

        $this->productService
            ->method('findProductVariants')
            ->willReturn($list);

        parent::setUp();
        $this->constraint = new UniqueProductVariantAttributeCombination();
        $this->context->setConstraint($this->constraint);
    }

    /**
     * @dataProvider provideForTestValidate
     *
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $productVariants
     */
    public function testValidate(
        array $productVariants,
        AbstractProductVariantData $data
    ): void {
        $this->existingProductVariants = $productVariants;
        $this->validator->validate($data, $this->constraint);

        $this->assertNoViolation();
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface>,
     *         \Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductVariantData,
     *     }
     * >
     */
    public function provideForTestValidate(): iterable
    {
        yield 'Empty' => [
            [],
            $this->createDataMock([], self::PRODUCT_CODE_1),
        ];

        yield 'Same product is allowed to retain same attributes' => [
            [
                $this->createProductVariant(self::PRODUCT_CODE_1, [
                    'ad.foo' => 'av.foo',
                ]),
            ],
            $this->createDataMock(
                [
                    'ad.foo' => $this->createAttributeMock('ad.foo', 'av.foo'),
                ],
                self::PRODUCT_CODE_1,
            ),
        ];

        yield 'Multiple products + more attributes in the new product' => [
            [
                $this->createProductVariant(self::PRODUCT_CODE_1, [
                    'ad.bar' => 'av.bar',
                ]),
                $this->createProductVariant(self::PRODUCT_CODE_2, [
                    'ad.foo' => 'av.foo',
                ]),
            ],
            $this->createDataMock(
                [
                    'ad.foo' => $this->createAttributeMock('ad.foo', 'av.foo'),
                    'ad.bar' => $this->createAttributeMock('ad.bar', 'av.bar'),
                ],
                self::PRODUCT_CODE_3,
            ),
        ];
    }

    /**
     * @dataProvider provideForTestInvalid
     *
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $productVariants
     */
    public function testInvalid(
        array $productVariants,
        AbstractProductVariantData $data,
        string $expectedProductCode
    ): void {
        $this->existingProductVariants = $productVariants;

        $this->validator->validate($data, $this->constraint);

        $this
            ->buildViolation('ibexa.product.variant.attributes.unique')
            ->setParameter('%product_variant_code%', $expectedProductCode)
            ->atPath('property.path.attributes')
            ->assertRaised();
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface>,
     *         \Ibexa\Bundle\ProductCatalog\Form\Data\AbstractProductVariantData,
     *         string,
     *     }
     * >
     */
    public function provideForTestInvalid(): iterable
    {
        yield 'Single attribute' => [
            [
                $this->createProductVariant(self::PRODUCT_CODE_1, [
                    'ad.foo' => 'av.foo',
                ]),
            ],
            $this->createDataMock(
                [
                    'ad.foo' => $this->createAttributeMock('ad.foo', 'av.foo'),
                ],
                self::PRODUCT_CODE_2,
            ),
            self::PRODUCT_CODE_1,
        ];

        yield 'Multiple attributes, same attributes' => [
            [
                $this->createProductVariant(self::PRODUCT_CODE_1, [
                    'ad.foo' => 'av.foo',
                    'ad.bar' => 'av.bar',
                ]),
            ],
            $this->createDataMock(
                [
                    'ad.foo' => $this->createAttributeMock('ad.foo', 'av.foo'),
                    'ad.bar' => $this->createAttributeMock('ad.bar', 'av.bar'),
                ],
                self::PRODUCT_CODE_2,
            ),
            self::PRODUCT_CODE_1,
        ];

        yield 'Multiple products + new product has the same attributes as existing' => [
            [
                $this->createProductVariant(self::PRODUCT_CODE_1, [
                    'ad.foo' => 'av.bar',
                ]),
                $this->createProductVariant(self::PRODUCT_CODE_2, [
                    'ad.foo' => 'av.foo',
                ]),
            ],
            $this->createDataMock(
                [
                    'ad.foo' => $this->createAttributeMock('ad.foo', 'av.foo'),
                ],
                self::PRODUCT_CODE_3,
            ),
            self::PRODUCT_CODE_2,
        ];

        yield 'Products with complex attributes + new product has the same attributes as existing' => [
            [
                $this->createProductVariant(self::PRODUCT_CODE_1, [
                    'ad.foo' => (object)['foo' => 0],
                ]),
                $this->createProductVariant(self::PRODUCT_CODE_2, [
                    'ad.foo' => (object)['foo' => 1],
                ]),
            ],
            $this->createDataMock(
                [
                    'ad.foo' => $this->createAttributeMock('ad.foo', (object)['foo' => 1]),
                ],
                self::PRODUCT_CODE_3,
            ),
            self::PRODUCT_CODE_2,
        ];
    }

    protected function createValidator(): UniqueProductVariantAttributeCombinationValidator
    {
        return new UniqueProductVariantAttributeCombinationValidator(
            $this->productService,
        );
    }

    /**
     * @param mixed $value
     */
    private function createAttributeMock(string $identifier, $value): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute
            ->method('getIdentifier')
            ->willReturn($identifier);

        $attribute
            ->method('getValue')
            ->willReturn($value);

        return $attribute;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createProductVariant(string $code, array $attributes): ProductVariantInterface
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $productVariant
            ->method('getCode')
            ->willReturn($code);

        $attributes = array_map(function (string $identifier, $value): AttributeInterface {
            return $this->createAttributeMock($identifier, $value);
        }, array_keys($attributes), array_values($attributes));

        $productVariant
            ->method('getAttributes')
            ->willReturn($attributes);

        return $productVariant;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createDataMock(array $attributes, string $originalProductCode): AbstractProductVariantData
    {
        $value = $this->createMock(AbstractProductVariantData::class);
        $value
            ->method('getAttributes')
            ->willReturn($attributes);

        $value
            ->method('getOriginalCode')
            ->willReturn($originalProductCode);

        return $value;
    }
}
