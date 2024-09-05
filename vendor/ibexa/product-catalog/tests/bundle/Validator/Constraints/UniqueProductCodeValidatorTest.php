<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCodeDataContainerInterface;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductCode;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductCodeValidator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueProductCodeValidatorTest extends TestCase
{
    private const EXAMPLE_PRODUCT_CODE_A = '0001';
    private const EXAMPLE_PRODUCT_CODE_B = '0002';

    /** @var \Ibexa\Contracts\ProductCatalog\ProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductServiceInterface $productService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private UniqueProductCodeValidator $validator;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(ProductServiceInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueProductCodeValidator($this->productService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     */
    public function testSkipValidation(object $value): void
    {
        $this->productService
            ->expects(self::never())
            ->method('getProduct');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueProductCode());
    }

    /**
     * @return iterable<string,array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'unsupported data class' => [
            new stdClass(),
        ];

        yield 'missing code (create)' => [
            $this->createProductCodeDataContainer(null),
        ];

        yield 'missing code (update)' => [
            $this->createProductCodeDataContainer(null, 'example'),
        ];
    }

    public function testValidDataWithNewProductCode(): void
    {
        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE_A)
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = $this->createProductCodeDataContainer(self::EXAMPLE_PRODUCT_CODE_A);

        $this->validator->validate($value, new UniqueProductCode());
    }

    public function testValidDataWithUpdatedProductCode(): void
    {
        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE_A)
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = $this->createProductCodeDataContainer(
            self::EXAMPLE_PRODUCT_CODE_A,
            self::EXAMPLE_PRODUCT_CODE_B
        );

        $this->validator->validate($value, new UniqueProductCode());
    }

    public function testValidDataWithUnchangedProductCode(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE_A);

        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE_A)
            ->willReturn($product);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = $this->createProductCodeDataContainer(
            self::EXAMPLE_PRODUCT_CODE_A,
            self::EXAMPLE_PRODUCT_CODE_A
        );

        $this->validator->validate($value, new UniqueProductCode());
    }

    public function testInvalidDataWithNewProductCode(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE_A);

        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE_A)
            ->willReturn($product);

        $constraint = new UniqueProductCode();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock(self::EXAMPLE_PRODUCT_CODE_A));

        $value = $this->createProductCodeDataContainer(self::EXAMPLE_PRODUCT_CODE_A);

        $this->validator->validate($value, new UniqueProductCode());
    }

    public function testInvalidDataWithUpdatedProductCode(): void
    {
        $originalProduct = $this->createMock(ProductInterface::class);
        $originalProduct->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE_A);

        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE_A)
            ->willReturn($originalProduct);

        $constraint = new UniqueProductCode();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock(self::EXAMPLE_PRODUCT_CODE_A));

        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE_B);

        $value = $this->createProductCodeDataContainer(self::EXAMPLE_PRODUCT_CODE_A);

        $this->validator->validate($value, new UniqueProductCode());
    }

    private function getConstraintViolationBuilderMock(string $code): ConstraintViolationBuilderInterface
    {
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('atPath')
            ->with('code')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->with('%code%', $code)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        return $constraintViolationBuilder;
    }

    private function createProductCodeDataContainer(
        ?string $code,
        ?string $originalCode = null
    ): ProductCodeDataContainerInterface {
        $container = $this->createMock(ProductCodeDataContainerInterface::class);
        $container->method('getCode')->willReturn($code);
        $container->method('getOriginalCode')->willReturn($originalCode);

        return $container;
    }
}
