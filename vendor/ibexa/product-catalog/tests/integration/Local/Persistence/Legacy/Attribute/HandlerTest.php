<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\Attribute;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Handler;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Handler
 */
final class HandlerTest extends IbexaKernelTestCase
{
    // Match tests/integration/_fixtures/attribute_definitions_data.yaml
    private const ATTRIBUTE_DEFINITION_ID_FOO_BOOLEAN = 8;
    private const ATTRIBUTE_DEFINITION_ID_FOO_INTEGER = 9;
    private const ATTRIBUTE_DEFINITION_ID_FOO_FLOAT = 10;
    private const ATTRIBUTE_DEFINITION_ID_FOO_SELECTION = 11;

    private Handler $handler;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->handler = self::getServiceByClassName(Handler::class);
    }

    public function testOperations(): void
    {
        $struct = new AttributeCreateStruct(1, self::ATTRIBUTE_DEFINITION_ID_FOO_BOOLEAN, true);
        $id = $this->handler->create($struct);

        self::assertTrue($this->handler->exists($id));
        $spiAttribute = $this->handler->find($id);
        self::assertTrue($spiAttribute->getValue());

        $struct = new AttributeCreateStruct(1, self::ATTRIBUTE_DEFINITION_ID_FOO_INTEGER, 42);
        $id = $this->handler->create($struct);

        self::assertTrue($this->handler->exists($id));
        $spiAttribute = $this->handler->find($id);
        self::assertSame(42, $spiAttribute->getValue());

        $struct = new AttributeCreateStruct(1, self::ATTRIBUTE_DEFINITION_ID_FOO_FLOAT, 42.96);
        $id = $this->handler->create($struct);

        self::assertTrue($this->handler->exists($id));
        $spiAttribute = $this->handler->find($id);
        self::assertSame(42.96, $spiAttribute->getValue());

        $struct = new AttributeCreateStruct(1, self::ATTRIBUTE_DEFINITION_ID_FOO_SELECTION, 'option');
        $id = $this->handler->create($struct);

        self::assertTrue($this->handler->exists($id));
        $spiAttribute = $this->handler->find($id);
        self::assertSame('option', $spiAttribute->getValue());
    }
}
