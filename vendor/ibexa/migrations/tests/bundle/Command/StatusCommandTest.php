<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command;

/**
 * @covers \Ibexa\Bundle\Migration\Command\StatusCommand
 */
final class StatusCommandTest extends AbstractCommandTest
{
    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:status';
    }

    public function testBaseExecution(): void
    {
        $input = [];
        $this->commandTester->execute($input);
        self::assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecutionWithFiles(): void
    {
        self::getFilesystem()->write('migrations/foo.yaml', '');
        self::getFilesystem()->write('migrations/bar.yaml', '');

        $input = [];
        $this->commandTester->execute($input);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('foo.yaml', $output);
        self::assertStringContainsString('bar.yaml', $output);
        self::assertSame(0, $this->commandTester->getStatusCode());
    }
}

class_alias(StatusCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\StatusCommandTest');
