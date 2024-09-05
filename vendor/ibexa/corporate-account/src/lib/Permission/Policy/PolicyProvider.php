<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Permission\Policy;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class PolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            'company_application' => [
                'workflow' => ['ApplicationState'],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.company_application', 'forms'))->setDesc('Company Application'),
            (new Message('role.policy.company_application.all_functions', 'forms'))->setDesc('Company Application / All functions'),
            (new Message('role.policy.company_application.workflow', 'forms'))->setDesc('Company Application / Workflow'),
        ];
    }
}
