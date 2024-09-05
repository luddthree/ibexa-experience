<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Tests\Bundle\Migration\FooService;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandServiceCallExecuteTest extends AbstractMigrateCommand
{
    /** @var \Ibexa\Tests\Bundle\Migration\FooService */
    private $fooService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fooService = self::getServiceByClassName(FooService::class, '__foo_service__');
    }

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/service-call.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertSame(0, $this->fooService->calledTimes);
    }

    protected function postCommandAssertions(): void
    {
        self::assertSame(1, $this->fooService->calledTimes);
    }
}

class_alias(MigrateCommandServiceCallExecuteTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandServiceCallExecuteTest');
