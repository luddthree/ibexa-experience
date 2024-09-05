<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Permissions\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class FieldGroupLimitationPolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'content' => [
                'create' => [
                    'FieldGroup',
                ],
                'edit' => [
                    'FieldGroup',
                ],
            ],
        ]);
    }
}

class_alias(FieldGroupLimitationPolicyProvider::class, 'Ibexa\Platform\Permissions\Security\FieldGroupLimitationPolicyProvider');
