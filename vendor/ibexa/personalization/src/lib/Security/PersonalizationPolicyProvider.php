<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class PersonalizationPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public const PERSONALIZATION_MODULE = 'personalization';
    public const PERSONALIZATION_VIEW_FUNCTION = 'view';
    public const PERSONALIZATION_EDIT_FUNCTION = 'edit';

    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            self::PERSONALIZATION_MODULE => [
                self::PERSONALIZATION_VIEW_FUNCTION => ['PersonalizationAccess'],
                self::PERSONALIZATION_EDIT_FUNCTION => ['PersonalizationAccess'],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.personalization', 'forms'))->setDesc('Personalization'),
            (new Message('role.policy.personalization.all_functions', 'forms'))->setDesc('Personalization / All functions'),
            (new Message('role.policy.personalization.view', 'forms'))->setDesc('Personalization / View'),
            (new Message('role.policy.personalization.edit', 'forms'))->setDesc('Personalization / Edit'),
        ];
    }
}

class_alias(PersonalizationPolicyProvider::class, 'Ibexa\Platform\Personalization\Security\PersonalizationPolicyProvider');
