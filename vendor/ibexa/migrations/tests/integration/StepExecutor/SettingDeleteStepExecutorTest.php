<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Migration\StepExecutor\SettingDeleteStepExecutor;
use Ibexa\Migration\ValueObject\Step\SettingDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Migration\StepExecutor\AbstractInitializedStepExecutorTest;

final class SettingDeleteStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private SettingService $settingService;

    private SettingDeleteStepExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingService = self::getServiceByClassName(SettingService::class);
        $this->executor = new SettingDeleteStepExecutor($this->settingService);
        self::configureExecutor($this->executor);
    }

    public function testCanHandle(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new SettingDeleteStep('foo_group', 'foo_identifier');
        self::assertTrue($this->executor->canHandle($step));
    }

    public function testHandling(): void
    {
        try {
            $this->settingService->loadSetting('foo_group', 'foo_identifier');
            self::fail(sprintf(
                'Setting "%s" in group "%s" should not be accessible at this point',
                'foo_identifier',
                'foo_group',
            ));
        } catch (NotFoundException $e) {
            // ignore
        }

        $struct = $this->settingService->newSettingCreateStruct();
        $struct->setIdentifier('foo_identifier');
        $struct->setGroup('foo_group');
        $struct->setValue('foo_value');
        $this->settingService->createSetting($struct);

        $step = new SettingDeleteStep('foo_group', 'foo_identifier');
        $this->executor->handle($step);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('foo');
        $this->settingService->loadSetting('foo_group', 'foo_identifier');
    }
}
