<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\UI;

use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;

final class DashboardBanner
{
    public const USER_PREFERENCE_IDENTIFIER = 'hide_dashboard_banner';

    private UserPreferenceService $userPreferenceService;

    public function __construct(
        UserPreferenceService $userPreferenceService
    ) {
        $this->userPreferenceService = $userPreferenceService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function hideDashboardBanner(): void
    {
        $userPreferenceSetStruct = new UserPreferenceSetStruct();
        $userPreferenceSetStruct->name = self::USER_PREFERENCE_IDENTIFIER;
        $userPreferenceSetStruct->value = 'true';
        $this->userPreferenceService->setUserPreference([$userPreferenceSetStruct]);
    }
}
