<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Workflow\Security\Limitation\IgnoreVersionLockLimitation;
use Ibexa\Workflow\Value\WorkflowMetadata;

class EditableWorkflowsListFilter implements ListFilterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $workflowMetadataList): array
    {
        return array_filter($workflowMetadataList, function (WorkflowMetadata $workflowMetadata): bool {
            return $this->permissionResolver->canUser(
                'content',
                'edit',
                $workflowMetadata->versionInfo,
                [new IgnoreVersionLockLimitation()]
            );
        });
    }
}

class_alias(EditableWorkflowsListFilter::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ListFilter\EditableWorkflowsListFilter');
