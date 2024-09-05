<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductVariantGenerate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantsGenerateStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class ProductVariantsGenerateTest extends TestCase
{
    private ProductVariantGenerate $parser;

    protected function setUp(): void
    {
        $this->parser = new ProductVariantGenerate();
    }

    public function testValidInput(): void
    {
        $productVariantGenerateStruct = new ProductVariantsGenerateStruct(
            [
                'test' => ['bar'],
                'foo' => ['baz'],
            ],
        );

        $parserInput = [
            'attributes' => [
                'test' => ['bar'],
                'foo' => ['baz'],
            ],
        ];

        self::assertEquals(
            $productVariantGenerateStruct,
            $this->parser->parse(
                $parserInput,
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
