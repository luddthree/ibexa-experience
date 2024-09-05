<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CurrencyUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyUpdateStruct;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CurrencyUpdateTest extends TestCase
{
    private CurrencyUpdate $parser;

    protected function setUp(): void
    {
        $this->parser = new CurrencyUpdate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new CurrencyUpdateStruct('USD', 2, true),
            $this->parser->parse(
                [
                    'code' => 'USD',
                    'subunits' => 2,
                    'enabled' => true,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new CurrencyUpdateStruct('EUR', null, null),
            $this->parser->parse(
                [
                    'code' => 'EUR',
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }
}
