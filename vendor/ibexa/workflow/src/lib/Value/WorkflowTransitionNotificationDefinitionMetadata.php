<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

class WorkflowTransitionNotificationDefinitionMetadata
{
    /** @var array */
    private $userGroups;

    /** @var array */
    private $users;

    /**
     * @param array $userGroups
     * @param array $users
     */
    public function __construct(array $userGroups = [], array $users = [])
    {
        $this->userGroups = $userGroups;
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @param array $userGroups
     */
    public function setUserGroups(array $userGroups): void
    {
        $this->userGroups = $userGroups;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array $users
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }
}

class_alias(WorkflowTransitionNotificationDefinitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowTransitionNotificationDefinitionMetadata');
