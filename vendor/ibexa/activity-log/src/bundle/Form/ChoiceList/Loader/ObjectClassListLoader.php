<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Form\ChoiceList\Loader;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;

final class ObjectClassListLoader extends AbstractChoiceLoader
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(ActivityLogServiceInterface $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>>
     */
    protected function loadChoices(): iterable
    {
        return $this->activityLogService->getObjectClasses();
    }
}
