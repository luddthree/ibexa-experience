<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\CodeGenerator\Strategy;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\CodeGenerator\Strategy\IncrementalCodeGenerator;
use PHPUnit\Framework\TestCase;

final class IncrementalCodeGeneratorTest extends TestCase
{
    private IncrementalCodeGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new IncrementalCodeGenerator();
    }

    public function testGenerate(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn('BASE');

        $context = new CodeGeneratorContext(
            $this->createMock(ProductTypeInterface::class),
        );
        $context->setBaseProduct($product);
        $context->setIndex(100);

        self::assertEquals('BASE-100', $this->generator->generateCode($context));
    }

    public function testGenerateWithMissingBaseProduct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$context\' is invalid: missing base product');

        $context = new CodeGeneratorContext(
            $this->createMock(ProductTypeInterface::class)
        );

        $this->generator->generateCode($context);
    }

    public function testGenerateWithMissingIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$context\' is invalid: missing index');

        $context = new CodeGeneratorContext(
            $this->createMock(ProductTypeInterface::class),
            [],
            $this->createMock(ProductInterface::class)
        );

        $this->generator->generateCode($context);
    }
}
