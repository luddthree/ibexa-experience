<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Permission;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class PolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public const MODULE_ACTIVITY_LOG = 'activity_log';

    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            self::MODULE_ACTIVITY_LOG => [
                'read' => [
                    ActivityLogOwnerLimitation::IDENTIFIER,
                ],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.activity_log', 'forms'))->setDesc('Activity Log'),
            (new Message('role.policy.activity_log.all_functions', 'forms'))->setDesc('Activity Log / All Functions'),
            (new Message('role.policy.activity_log.read', 'forms'))->setDesc('Activity Log / Read'),
            (new Message('activity_log.limitation.activity_log.limitation.self', 'ibexa_activity_log'))->setDesc('Only own logs'),
        ];
    }
}
