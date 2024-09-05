<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Repository;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Scheduler\Repository\DateBasedEntriesListInterface;

abstract class AbstractDateBasedService implements DateBasedEntriesListInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    protected $contentService;

    public function __construct(
        ContentService $contentService,
        PermissionResolver $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
    }

    abstract protected function getActionType(): string;
}

class_alias(AbstractDateBasedService::class, 'EzSystems\DateBasedPublisher\Core\Repository\AbstractDateBasedService');
