<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\CodeGenerator;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface;
use Ibexa\ProductCatalog\Local\Repository\CodeGenerator\CodeGeneratorRegistry;
use PHPUnit\Framework\TestCase;

final class CodeGeneratorRegistryTest extends TestCase
{
    public function testHasCodeGenerator(): void
    {
        $registry = new CodeGeneratorRegistry([
            'existing' => $this->createMock(CodeGeneratorInterface::class),
        ]);

        self::assertTrue($registry->hasCodeGenerator('existing'));
        self::assertFalse($registry->hasCodeGenerator('non-existing'));
    }

    public function testGetCodeGenerator(): void
    {
        $expectedCodeGenerator = $this->createMock(CodeGeneratorInterface::class);

        $registry = new CodeGeneratorRegistry([
            'example' => $expectedCodeGenerator,
        ]);

        self::assertSame($expectedCodeGenerator, $registry->getCodeGenerator('example'));
    }

    public function testGetCodeGeneratorThrowsInvalidArgumentException(): void
    {
        $message = "Argument 'type' is invalid: could not find "
            . "Ibexa\Contracts\ProductCatalog\Local\CodeGenerator\CodeGeneratorInterface for 'non-existing'. Valid values are: ";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $registry = new CodeGeneratorRegistry([/* Empty registry */]);
        $registry->getCodeGenerator('non-existing');
    }
}
