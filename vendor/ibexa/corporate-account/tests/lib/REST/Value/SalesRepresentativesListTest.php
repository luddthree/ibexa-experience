<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Value;

use Ibexa\Contracts\CorporateAccount\Values\SalesRepresentativesList as APISalesRepresentativesList;
use Ibexa\CorporateAccount\REST\Value\SalesRepresentativesList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\CorporateAccount\REST\Value\SalesRepresentativesList
 */
final class SalesRepresentativesListTest extends TestCase
{
    public function testGetSalesRepresentativesList(): void
    {
        $apiSalesRepresentativesList = new APISalesRepresentativesList();
        $restSalesRepresentativesList = new SalesRepresentativesList($apiSalesRepresentativesList);

        self::assertSame($apiSalesRepresentativesList, $restSalesRepresentativesList->getSalesRepresentativesList());
    }
}
