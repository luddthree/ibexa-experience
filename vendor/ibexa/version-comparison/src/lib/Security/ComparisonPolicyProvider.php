<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class ComparisonPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'comparison' => [
                'view' => [],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.comparison', 'forms'))->setDesc('Comparison'),
            (new Message('role.policy.comparison.all_functions', 'forms'))->setDesc('Comparison / All functions'),
            (new Message('role.policy.comparison.view', 'forms'))->setDesc('Comparison / View'),
        ];
    }
}

class_alias(ComparisonPolicyProvider::class, 'EzSystems\EzPlatformVersionComparison\Security\ComparisonPolicyProvider');
