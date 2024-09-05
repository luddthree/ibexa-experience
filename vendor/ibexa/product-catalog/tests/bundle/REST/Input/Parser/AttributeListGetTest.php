<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AttributeListGet;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeQueryStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class AttributeListGetTest extends TestCase
{
    private AttributeListGet $parser;

    protected function setUp(): void
    {
        $this->parser = new AttributeListGet();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new AttributeQueryStruct('group_prefix', 'prefix', 0, 25),
            $this->parser->parse(
                [
                    'group_name_prefix' => 'group_prefix',
                    'name_prefix' => 'prefix',
                    'offset' => 0,
                    'limit' => 25,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new AttributeQueryStruct(null, null, 4, 200),
            $this->parser->parse(
                [
                    'offset' => 4,
                    'limit' => 200,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
