<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractContextProviderTest extends TestCase
{
    public function testAccept(): void
    {
        $contextProvider = $this->getContextProvider();

        self::assertTrue($contextProvider->accept($this->getPolicy()));
    }

    public function testAcceptWithUnsupportedContextProvider(): void
    {
        $contextProvider = $this->getContextProvider();

        self::assertFalse($contextProvider->accept($this->getUnsupportedPolicy()));
    }

    public function testGetPermissionContext(): void
    {
        $contextProvider = $this->getContextProvider();

        self::assertEquals(
            $this->getExpectedContext(),
            $contextProvider->getPermissionContext($this->getPolicy())
        );
    }

    abstract protected function getContextProvider(): ContextProviderInterface;

    abstract protected function getPolicy(): PolicyInterface;

    abstract protected function getUnsupportedPolicy(): PolicyInterface;

    abstract protected function getExpectedContext(): ContextInterface;
}
