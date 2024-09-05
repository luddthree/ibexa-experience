<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductTypeListGet;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeQueryStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductTypeListGetTest extends TestCase
{
    private ProductTypeListGet $parser;

    protected function setUp(): void
    {
        $this->parser = new ProductTypeListGet();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new ProductTypeQueryStruct('test', 0, 25),
            $this->parser->parse(
                [
                    'name_prefix' => 'test',
                    'offset' => 0,
                    'limit' => 25,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new ProductTypeQueryStruct(null, 0, 10),
            $this->parser->parse(
                [
                    'limit' => 10,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
