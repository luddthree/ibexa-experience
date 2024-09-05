<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Exception;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Exception\UnauthorizedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Exception\UnauthorizedException
 */
final class UnauthorizedExceptionTest extends TestCase
{
    public function testUnauthorizedException(): void
    {
        $exception = new UnauthorizedException($this->getPolicy());
        self::assertEquals(
            "The User does not have the 'function' 'module' permission",
            $exception->getMessage()
        );
    }

    private function getPolicy(): PolicyInterface
    {
        return new class() implements PolicyInterface {
            public function getModule(): string
            {
                return 'module';
            }

            public function getFunction(): string
            {
                return 'function';
            }

            public function getObject(): ?object
            {
                return null;
            }

            public function getTargets(): array
            {
                return [];
            }
        };
    }
}
