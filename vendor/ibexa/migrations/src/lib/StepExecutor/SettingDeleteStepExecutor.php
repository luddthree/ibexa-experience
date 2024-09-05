<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\ValueObject\Step\SettingDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class SettingDeleteStepExecutor extends AbstractStepExecutor implements StepExecutorInterface
{
    private SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\SettingDeleteStep $step
     */
    public function doHandle(StepInterface $step)
    {
        $setting = $this->settingService->loadSetting($step->group, $step->identifier);

        $this->settingService->deleteSetting($setting);

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SettingDeleteStep;
    }
}
