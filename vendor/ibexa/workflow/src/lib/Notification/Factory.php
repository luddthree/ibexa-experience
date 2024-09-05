<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Notification;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Workflow\Value\VersionLock;

final class Factory
{
    public const ASK_UNLOCK_TYPE = 'AskUnlock';

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    public function getAskUnlockNotificationCreateStruct(VersionLock $versionLock): CreateStruct
    {
        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $content = $this->contentService->loadContent($versionLock->contentId, null, $versionLock->version);

        $createStruct = new CreateStruct();
        $createStruct->ownerId = $versionLock->userId;
        $createStruct->type = 'Workflow:' . self::ASK_UNLOCK_TYPE;
        $createStruct->data = [
            'contentName' => $content->getName(),
            'contentId' => $content->id,
            'versionNumber' => $versionLock->version,
            'sender' => $this->userService->loadUser($currentUserId)->getName(),
            'senderId' => $currentUserId,
        ];

        return $createStruct;
    }
}
