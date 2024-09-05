<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class TaxonomyPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            'taxonomy' => [
                'read' => ['Taxonomy'],
                'manage' => ['Taxonomy'],
                'assign' => [],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.taxonomy', 'forms'))->setDesc('Taxonomy'),
            (new Message('role.policy.taxonomy.all_functions', 'forms'))->setDesc('Taxonomy / All functions'),
            (new Message('role.policy.taxonomy.read', 'forms'))->setDesc('Taxonomy / Read'),
            (new Message('role.policy.taxonomy.manage', 'forms'))->setDesc('Taxonomy / Manage'),
            (new Message('role.policy.taxonomy.assign', 'forms'))->setDesc('Taxonomy / Assign'),
        ];
    }
}
