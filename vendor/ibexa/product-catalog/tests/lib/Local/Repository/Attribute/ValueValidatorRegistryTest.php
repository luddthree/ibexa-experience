<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace lib\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistry;
use PHPUnit\Framework\TestCase;

final class ValueValidatorRegistryTest extends TestCase
{
    public function testHasValidator(): void
    {
        $registry = new ValueValidatorRegistry([
            'existing' => $this->createMock(ValueValidatorInterface::class),
        ]);

        self::assertTrue($registry->hasValidator('existing'));
        self::assertFalse($registry->hasValidator('non-existing'));
    }

    public function testGetValidator(): void
    {
        $expectedValidator = $this->createMock(ValueValidatorInterface::class);

        $registry = new ValueValidatorRegistry([
            'example' => $expectedValidator,
        ]);

        self::assertSame($expectedValidator, $registry->getValidator('example'));
    }

    public function testGetValidatorThrowsInvalidArgumentException(): void
    {
        $message = "Argument 'type' is invalid: Could not find Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface for 'non-existing' attribute type";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $registry = new ValueValidatorRegistry([/* Empty registry */]);
        $registry->getValidator('non-existing');
    }
}
