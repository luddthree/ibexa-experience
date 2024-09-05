<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command;

use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends IbexaKernelTestCase
{
    /** @var \Symfony\Component\Console\Command\Command */
    protected $command;

    /** @var \Symfony\Component\Console\Tester\CommandTester */
    protected $commandTester;

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $this->command = $application->find(static::getCommandName());
        $this->commandTester = new CommandTester($this->command);
    }

    abstract protected static function getCommandName(): string;
}

class_alias(AbstractCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\AbstractCommandTest');
