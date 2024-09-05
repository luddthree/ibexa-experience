<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Builder;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;

class CreatorsMapBuilder
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata[] $workflowMetadataList
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User[]
     */
    public function buildCreatorsMapForWorkflowMetadataList(array $workflowMetadataList): array
    {
        $creatorsMap = [];
        foreach ($workflowMetadataList as $workflowMetadata) {
            try {
                $creator = $this->userService->loadUser($workflowMetadata->versionInfo->creatorId);
            } catch (NotFoundException $e) {
                continue;
            }

            $creatorsMap[$creator->getUserId()] = $creator;
        }

        return $creatorsMap;
    }
}

class_alias(CreatorsMapBuilder::class, 'EzSystems\EzPlatformWorkflow\Builder\CreatorsMapBuilder');
