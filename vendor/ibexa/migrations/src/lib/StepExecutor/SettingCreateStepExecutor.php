<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\ValueObject\Step\SettingCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class SettingCreateStepExecutor extends AbstractStepExecutor implements StepExecutorInterface
{
    private SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SettingCreateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\SettingCreateStep $step
     */
    protected function doHandle(StepInterface $step): Setting
    {
        $createStruct = $this->settingService->newSettingCreateStruct();
        $createStruct->setGroup($step->group);
        $createStruct->setIdentifier($step->identifier);
        $createStruct->setValue($step->value);

        return $this->settingService->createSetting($createStruct);
    }
}
