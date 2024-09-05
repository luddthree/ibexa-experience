<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class SiteFactoryPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            'site' => [
                'view' => null,
                'create' => null,
                'edit' => null,
                'delete' => null,
                'change_status' => null,
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.site', 'forms'))->setDesc('Site'),
            (new Message('role.policy.site.all_functions', 'forms'))->setDesc('Site / All functions'),
            (new Message('role.policy.site.view', 'forms'))->setDesc('Site / View'),
            (new Message('role.policy.site.create', 'forms'))->setDesc('Site / Create'),
            (new Message('role.policy.site.edit', 'forms'))->setDesc('Site / Edit'),
            (new Message('role.policy.site.delete', 'forms'))->setDesc('Site / Delete'),
            (new Message('role.policy.site.change_status', 'forms'))->setDesc('Site / Change status'),
        ];
    }
}

class_alias(SiteFactoryPolicyProvider::class, 'EzSystems\EzPlatformSiteFactory\Security\SiteFactoryPolicyProvider');
