<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\VariantFormMapperRegistry;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface;
use PHPUnit\Framework\TestCase;

final class FilterFormMapperRegistryTest extends TestCase
{
    public function testHasMapper(): void
    {
        $registry = new VariantFormMapperRegistry([
            'existing' => $this->createMock(VariantFormMapperInterface::class),
        ]);

        self::assertTrue($registry->hasMapper('existing'));
        self::assertFalse($registry->hasMapper('non-existing'));
    }

    public function testGetMapper(): void
    {
        $expectedMapper = $this->createMock(VariantFormMapperInterface::class);

        $registry = new VariantFormMapperRegistry([
            'example' => $expectedMapper,
        ]);

        self::assertSame($expectedMapper, $registry->getMapper('example'));
    }

    public function testGetMapperThrowsInvalidArgumentException(): void
    {
        $message = "Argument 'type' is invalid: Could not find "
            . "Ibexa\Bundle\ProductCatalog\Form\Attribute\VariantFormMapperRegistryInterface "
            . "for 'non-existing' attribute type";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $registry = new VariantFormMapperRegistry([/* Empty registry */]);
        $registry->getMapper('non-existing');
    }
}
