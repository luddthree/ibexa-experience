<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\CodeGenerator\Strategy;

use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorContext;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\CodeGenerator\Strategy\RandomCodeGenerator;
use PHPUnit\Framework\TestCase;

final class RandomCodeGeneratorTest extends TestCase
{
    private RandomCodeGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new RandomCodeGenerator();
    }

    public function testGenerateWithoutBaseProduct(): void
    {
        $context = new CodeGeneratorContext(
            $this->createMock(ProductTypeInterface::class),
        );

        self::assertMatchesRegularExpression(
            '/^[A-Z0-9]{13}$/',
            $this->generator->generateCode($context)
        );
    }

    public function testGenerateWithBaseProduct(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn('BASE');

        $context = new CodeGeneratorContext(
            $this->createMock(ProductTypeInterface::class),
        );
        $context->setBaseProduct($product);

        self::assertMatchesRegularExpression(
            '/^BASE-[A-Z0-9]{13}$/',
            $this->generator->generateCode($context)
        );
    }
}
