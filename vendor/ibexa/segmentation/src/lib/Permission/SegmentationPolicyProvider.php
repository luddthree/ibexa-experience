<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Permission;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class SegmentationPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            'segment' => [
                'read' => ['SegmentGroup'],
                'create' => ['SegmentGroup'],
                'update' => ['SegmentGroup'],
                'remove' => ['SegmentGroup'],
                'assign_to_user' => ['SegmentGroup'],
                'view_user_segment_list' => [],
            ],
            'segment_group' => [
                'read' => [],
                'create' => [],
                'update' => [],
                'remove' => [],
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.segment', 'forms'))->setDesc('Segment'),
            (new Message('role.policy.segment.all_functions', 'forms'))->setDesc('Segment / All functions'),
            (new Message('role.policy.segment.read', 'forms'))->setDesc('Segment / Read'),
            (new Message('role.policy.segment.create', 'forms'))->setDesc('Segment / Create'),
            (new Message('role.policy.segment.update', 'forms'))->setDesc('Segment / Update'),
            (new Message('role.policy.segment.remove', 'forms'))->setDesc('Segment / Remove'),
            (new Message('role.policy.segment.assign_to_user', 'forms'))->setDesc('Segment / Assign to user'),
            (new Message('role.policy.segment.view_user_segment_list', 'forms'))->setDesc('Segment / View user segment list'),
            (new Message('role.policy.segment_group', 'forms'))->setDesc('Segment Group'),
            (new Message('role.policy.segment_group.all_functions', 'forms'))->setDesc('Segment Group / All functions'),
            (new Message('role.policy.segment_group.read', 'forms'))->setDesc('Segment Group / Read'),
            (new Message('role.policy.segment_group.create', 'forms'))->setDesc('Segment Group / Create'),
            (new Message('role.policy.segment_group.update', 'forms'))->setDesc('Segment Group / Update'),
            (new Message('role.policy.segment_group.remove', 'forms'))->setDesc('Segment Group / Remove'),
        ];
    }
}

class_alias(SegmentationPolicyProvider::class, 'Ibexa\Platform\Segmentation\Permission\SegmentationPolicyProvider');
