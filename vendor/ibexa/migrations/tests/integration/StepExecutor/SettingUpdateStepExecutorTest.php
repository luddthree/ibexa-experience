<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Migration\StepExecutor\SettingUpdateStepExecutor;
use Ibexa\Migration\ValueObject\Step\SettingUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Migration\StepExecutor\AbstractInitializedStepExecutorTest;

final class SettingUpdateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private SettingService $settingService;

    private SettingUpdateStepExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingService = self::getServiceByClassName(SettingService::class);
        $this->executor = new SettingUpdateStepExecutor($this->settingService);
        self::configureExecutor($this->executor);
    }

    public function testCanHandle(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new SettingUpdateStep('foo_group', 'foo_identifier', []);
        self::assertTrue($this->executor->canHandle($step));
    }

    /**
     * @dataProvider provideForHandling
     *
     * @param mixed $value
     */
    public function testHandling(SettingUpdateStep $step, $value): void
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

        $this->executor->handle($step);

        $setting = $this->settingService->loadSetting('foo_group', 'foo_identifier');
        self::assertSame('foo_identifier', $setting->identifier);
        self::assertSame('foo_group', $setting->group);
        self::assertEquals($value, $setting->value);
    }

    /**
     * @return iterable<string, array{
     *     \Ibexa\Migration\ValueObject\Step\SettingUpdateStep,
     *     mixed,
     * }>
     */
    public function provideForHandling(): iterable
    {
        $value = [];
        yield 'empty array value' => [
            new SettingUpdateStep('foo_group', 'foo_identifier', $value),
            $value,
        ];

        $value = 'foo';
        yield '"foo" value' => [
            new SettingUpdateStep('foo_group', 'foo_identifier', $value),
            $value,
        ];

        $value = [
            'foo_key' => 'foo_value',
            'bar_key' => 'bar_value',
        ];
        yield 'array value' => [
            new SettingUpdateStep('foo_group', 'foo_identifier', $value),
            $value,
        ];

        $value = null;
        yield 'null value' => [
            new SettingUpdateStep('foo_group', 'foo_identifier', $value),
            $value,
        ];
    }
}
