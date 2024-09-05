<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorRegistry;
use PHPUnit\Framework\TestCase;

final class OptionsValidatorRegistryTest extends TestCase
{
    public function testHasValidator(): void
    {
        $registry = new OptionsValidatorRegistry([
            'existing' => $this->createMock(OptionsValidatorInterface::class),
        ]);

        self::assertTrue($registry->hasValidator('existing'));
        self::assertFalse($registry->hasValidator('non-existing'));
    }

    public function testGetValidator(): void
    {
        $expectedValidator = $this->createMock(OptionsValidatorInterface::class);

        $registry = new OptionsValidatorRegistry([
            'example' => $expectedValidator,
        ]);

        self::assertSame($expectedValidator, $registry->getValidator('example'));
    }

    public function testGetValidatorThrowsInvalidArgumentException(): void
    {
        $message = "Argument 'type' is invalid: Could not find Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface for 'non-existing' attribute type";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $registry = new OptionsValidatorRegistry([/* Empty registry */]);
        $registry->getValidator('non-existing');
    }
}
