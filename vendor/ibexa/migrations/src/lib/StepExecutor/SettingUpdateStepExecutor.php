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
use Ibexa\Migration\ValueObject\Step\SettingUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class SettingUpdateStepExecutor extends AbstractStepExecutor implements StepExecutorInterface
{
    private SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\SettingUpdateStep $step
     */
    public function doHandle(StepInterface $step): Setting
    {
        $setting = $this->settingService->loadSetting($step->group, $step->identifier);

        $updateStruct = $this->settingService->newSettingUpdateStruct();
        $updateStruct->setValue($step->value);

        return $this->settingService->updateSetting($setting, $updateStruct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SettingUpdateStep;
    }
}
