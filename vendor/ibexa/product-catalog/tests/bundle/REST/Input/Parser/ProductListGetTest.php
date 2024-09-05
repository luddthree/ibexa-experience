<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductListGet;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductQueryStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductListGetTest extends TestCase
{
    private ProductListGet $parser;

    protected function setUp(): void
    {
        $this->parser = new ProductListGet();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new ProductQueryStruct(0, 50, ['eng-GB', 'ger-DE']),
            $this->parser->parse(
                [
                    'offset' => 0,
                    'limit' => 50,
                    'languages' => ['eng-GB', 'ger-DE'],
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new ProductQueryStruct(0, 50),
            $this->parser->parse(
                [
                    'offset' => 0,
                    'limit' => 50,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
