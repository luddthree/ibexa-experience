<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class WorkflowPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            'content' => [
                'edit' => [
                    'WorkflowStage',
                    'VersionLock',
                ],
                'publish' => [
                    'WorkflowStage',
                ],
                'unlock' => [
                    'Class',
                    'Section',
                    'Subtree',
                    'VersionLock',
                    'Language',
                ],
            ],
            'workflow' => [
                'change_stage' => [
                    'WorkflowTransition',
                ],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.workflow', 'forms'))->setDesc('Workflow'),
            (new Message('role.policy.workflow.all_functions', 'forms'))->setDesc('Workflow / All functions'),
            (new Message('role.policy.content.unlock', 'forms'))->setDesc('Content / Unlock'),
            (new Message('role.policy.workflow.change_stage', 'forms'))->setDesc('Workflow / Change Stage'),
        ];
    }
}

class_alias(WorkflowPolicyProvider::class, 'EzSystems\EzPlatformWorkflow\Security\WorkflowPolicyProvider');
