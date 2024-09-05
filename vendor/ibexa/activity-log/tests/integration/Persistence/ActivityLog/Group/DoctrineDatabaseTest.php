<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\Persistence\ActivityLog\Group;

use Doctrine\Common\Collections\Expr\Comparison;
use Ibexa\ActivityLog\Persistence\ActivityLog\Group\GatewayInterface;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ActivityLog\Persistence\ActivityLog\Group\DoctrineDatabase
 */
final class DoctrineDatabaseTest extends IbexaKernelTestCase
{
    private GatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = $this->getIbexaTestCore()->getServiceByClassName(GatewayInterface::class);
    }

    public function testDeleting(): void
    {
        $comparison = new Comparison('logged_at', '<', date('Y-m-d H:i:s', strtotime('+1 hour')));

        $count = $this->gateway->countBy([$comparison]);
        self::assertSame(5, $count);

        $this->gateway->deleteBy([$comparison]);

        $count = $this->gateway->countBy([$comparison]);
        self::assertSame(0, $count);
    }
}
