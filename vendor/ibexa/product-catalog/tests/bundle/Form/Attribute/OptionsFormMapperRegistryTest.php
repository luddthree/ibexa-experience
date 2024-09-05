<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\OptionsFormMapperRegistry;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface;
use PHPUnit\Framework\TestCase;

final class OptionsFormMapperRegistryTest extends TestCase
{
    public function testHasMapper(): void
    {
        $registry = new OptionsFormMapperRegistry([
            'existing' => $this->createMock(OptionsFormMapperInterface::class),
        ]);

        self::assertTrue($registry->hasMapper('existing'));
        self::assertFalse($registry->hasMapper('non-existing'));
    }

    public function testGetMapper(): void
    {
        $expectedMapper = $this->createMock(OptionsFormMapperInterface::class);

        $registry = new OptionsFormMapperRegistry([
            'example' => $expectedMapper,
        ]);

        self::assertSame($expectedMapper, $registry->getMapper('example'));
    }

    public function testGetMapperThrowsInvalidArgumentException(): void
    {
        $message = "Argument '\$type' is invalid: could not find "
            . "Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface for 'non-existing'. Valid values are: ";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $registry = new OptionsFormMapperRegistry([/* Empty registry */]);
        $registry->getMapper('non-existing');
    }
}
