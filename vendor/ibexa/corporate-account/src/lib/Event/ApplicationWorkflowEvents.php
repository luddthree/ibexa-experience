<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event;

final class ApplicationWorkflowEvents
{
    public const APPLICATION_WORKFLOW = 'corporate_account.application.workflow';

    public static function getStateEvent(string $state): string
    {
        return self::APPLICATION_WORKFLOW . '.' . $state;
    }
}
