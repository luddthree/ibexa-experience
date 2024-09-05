<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\ProductUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductUpdateStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Rest\Server\Input\Parser\VersionUpdate;
use PHPUnit\Framework\TestCase;

final class ProductUpdateTest extends TestCase
{
    private ProductUpdate $parser;

    protected function setUp(): void
    {
        $versionUpdateParser = $this->createMock(VersionUpdate::class);
        $versionUpdateParser->method('parse')->willReturn(new ContentUpdateStruct());

        $this->parser = new ProductUpdate($versionUpdateParser);
    }

    public function testValidInput(): void
    {
        $productUpdateStruct = new ProductUpdateStruct(
            new ContentUpdateStruct(),
            'test_code',
            [
                'identifier' => 'bar',
                'is_required' => true,
                'is_discriminator' => false,
            ]
        );

        $parserInput = [
            'ContentUpdate' => [
            ],
            'code' => 'test_code',
            'attributes' => [
                'identifier' => 'bar',
                'is_required' => true,
                'is_discriminator' => false,
            ],
        ];

        self::assertEquals(
            $productUpdateStruct,
            $this->parser->parse(
                $parserInput,
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
