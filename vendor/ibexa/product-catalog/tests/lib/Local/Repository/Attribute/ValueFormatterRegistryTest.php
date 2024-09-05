<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterRegistry;
use PHPUnit\Framework\TestCase;

final class ValueFormatterRegistryTest extends TestCase
{
    public function testHasFormatter(): void
    {
        $registry = new ValueFormatterRegistry([
            'existing' => $this->createMock(ValueFormatterInterface::class),
        ]);

        self::assertTrue($registry->hasFormatter('existing'));
        self::assertFalse($registry->hasFormatter('non-existing'));
    }

    public function testGetFormatter(): void
    {
        $expectedFormatter = $this->createMock(ValueFormatterInterface::class);

        $registry = new ValueFormatterRegistry([
            'example' => $expectedFormatter,
        ]);

        self::assertSame($expectedFormatter, $registry->getFormatter('example'));
    }

    public function testGetFormatterThrowsInvalidArgumentException(): void
    {
        $message = "Argument 'type' is invalid: Could not find Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface for 'non-existing' attribute type";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $registry = new ValueFormatterRegistry([/* Empty registry */]);
        $registry->getFormatter('non-existing');
    }
}
