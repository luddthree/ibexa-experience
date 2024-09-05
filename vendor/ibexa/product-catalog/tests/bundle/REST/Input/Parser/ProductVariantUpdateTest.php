<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductVariantUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantUpdateStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductVariantUpdateTest extends TestCase
{
    private ProductVariantUpdate $parser;

    protected function setUp(): void
    {
        $this->parser = new ProductVariantUpdate();
    }

    public function testValidInput(): void
    {
        $productVariantUpdateStruct = new ProductVariantUpdateStruct(
            [
                'test' => 'bar',
                'foo' => 'baz',
            ],
            'test_code'
        );

        $parserInput = [
            'code' => 'test_code',
            'attributes' => [
                'test' => 'bar',
                'foo' => 'baz',
            ],
        ];

        self::assertEquals(
            $productVariantUpdateStruct,
            $this->parser->parse(
                $parserInput,
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
