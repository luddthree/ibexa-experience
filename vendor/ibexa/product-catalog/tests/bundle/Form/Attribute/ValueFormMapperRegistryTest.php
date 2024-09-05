<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\ValueFormMapperRegistry;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface;
use PHPUnit\Framework\TestCase;

final class ValueFormMapperRegistryTest extends TestCase
{
    public function testHasMapper(): void
    {
        $registry = new ValueFormMapperRegistry([
            'existing' => $this->createMock(ValueFormMapperInterface::class),
        ]);

        self::assertTrue($registry->hasMapper('existing'));
        self::assertFalse($registry->hasMapper('non-existing'));
    }

    public function testGetMapper(): void
    {
        $expectedMapper = $this->createMock(ValueFormMapperInterface::class);

        $registry = new ValueFormMapperRegistry([
            'example' => $expectedMapper,
        ]);

        self::assertSame($expectedMapper, $registry->getMapper('example'));
    }

    public function testGetMapperThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Could not find Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface for 'non-existing' attribute type");

        $registry = new ValueFormMapperRegistry([/** Empty registry */]);
        $registry->getMapper('non-existing');
    }
}
