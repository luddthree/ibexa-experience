<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Permission;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ActivityLogOwnerLimitation extends Limitation implements TranslationContainerInterface
{
    public const IDENTIFIER = 'activity_log_owner';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(
                'policy.limitation.identifier.activity_log_owner',
                'ibexa_content_forms_policies'
            ))->setDesc('Owner'),
        ];
    }
}
