<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Exception\Persistence;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Exception\Persistence\InvalidColumnException
 */
final class InvalidColumnExceptionTest extends TestCase
{
    public function testUnauthorizedException(): void
    {
        $exception = new InvalidColumnException(
            'table_name',
            ['missing_column'],
            ['available_column']
        );
        self::assertEquals(
            '"missing_column" do not exist in table_name. Available columns are: "available_column"',
            $exception->getMessage()
        );
    }
}
