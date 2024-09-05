<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Permission\Limitation;

use Ibexa\Contracts\Core\Persistence\User\Policy;
use Ibexa\Contracts\CorporateAccount\Values\Limitation\ApplicationStateLimitation as ApplicationStateLimitationValue;
use Ibexa\Core\Persistence\Legacy\User\Role\LimitationHandler;

final class ApplicationStateLimitationHandler extends LimitationHandler
{
    public function toLegacy(Policy $policy): void
    {
        if ($policy->limitations === '*' || !isset($policy->limitations[ApplicationStateLimitationValue::IDENTIFIER])) {
            return;
        }

        $limitations = [];
        foreach ($policy->limitations[ApplicationStateLimitationValue::IDENTIFIER] as $target => $states) {
            foreach ($states as $state) {
                $limitations[] = "{$target}:{$state}";
            }
        }

        $policy->limitations[ApplicationStateLimitationValue::IDENTIFIER] = $limitations;
    }

    public function toSPI(Policy $policy): void
    {
        if ($policy->limitations === '*' || empty($policy->limitations)) {
            return;
        }

        if (!isset($policy->limitations[ApplicationStateLimitationValue::IDENTIFIER])) {
            return;
        }

        $limitations = [];
        foreach ($policy->limitations[ApplicationStateLimitationValue::IDENTIFIER] as $limitation) {
            [$target, $state] = explode(':', $limitation, 2);
            $limitations[$target][] = $state;
        }

        foreach (ApplicationStateLimitation::LIMITATION_VALUES_ARRAYS as $array) {
            $policy->limitations[ApplicationStateLimitationValue::IDENTIFIER][$array] ??= [];
        }

        $policy->limitations[ApplicationStateLimitationValue::IDENTIFIER] = $limitations;
    }
}
