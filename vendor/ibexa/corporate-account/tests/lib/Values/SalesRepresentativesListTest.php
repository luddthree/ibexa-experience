<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Values;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Values\SalesRepresentativesList;
use PHPUnit\Framework\TestCase;

final class SalesRepresentativesListTest extends TestCase
{
    public function testGetIterator(): void
    {
        $userList = [
            $this->createMock(User::class),
            $this->createMock(User::class),
        ];
        $salesRepresentativesList = new SalesRepresentativesList($userList);
        self::assertCount(count($userList), $salesRepresentativesList);

        $idx = -1;
        foreach ($salesRepresentativesList as $idx => $user) {
            self::assertSame($userList[$idx], $user);
        }
        // check if iteration over the list happened
        self::assertSame(1, $idx);
    }

    public function testAppend(): void
    {
        $salesRepresentativesList = new SalesRepresentativesList();
        self::assertCount(0, $salesRepresentativesList);

        $userMock = $this->createMock(User::class);
        $salesRepresentativesList->append($userMock);

        self::assertSame([$userMock], iterator_to_array($salesRepresentativesList));
    }
}
