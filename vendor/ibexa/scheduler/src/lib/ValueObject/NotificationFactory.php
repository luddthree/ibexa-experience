<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\ValueObject;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Routing\ChainRouter;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\Scheduler\ValueObject\ScheduledEntry as SPIScheduledEntry;

class NotificationFactory
{
    public const TYPE_PUBLISHED = 'Published';
    public const TYPE_HIDDEN = 'Hidden';
    public const TYPE_UNSCHEDULED = 'Unscheduled';

    /** @var \Ibexa\Core\MVC\Symfony\Routing\ChainRouter */
    protected $router;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    protected $locationService;

    /** @var \Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator */
    protected $urlAliasGenerator;

    public function __construct(
        ChainRouter $router,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        UrlAliasGenerator $urlAliasGenerator
    ) {
        $this->router = $router;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->urlAliasGenerator = $urlAliasGenerator;
    }

    public function getNotificationCreateStruct(ValueObject $valueObject, $type): CreateStruct
    {
        if ($valueObject instanceof ScheduledEntry) {
            return $this->scheduledEntry($valueObject, $type);
        }

        throw new NotFoundException('Notification', \get_class($valueObject));
    }

    public function getNotificationCreateStructBySPIEntry(
        SPIScheduledEntry $spiScheduledEntry,
        string $type,
        bool $isAvailable = true,
        string $message = ''
    ): CreateStruct {
        $contentInfo = $this->contentService->loadContentInfo($spiScheduledEntry->contentId);

        $createStruct = new CreateStruct();
        $createStruct->ownerId = $spiScheduledEntry->userId;
        $createStruct->type = 'DateBasedPublisher:' . $type;
        $createStruct->data = [
            'message' => $message,
            'contentName' => $contentInfo->name,
            'contentId' => $contentInfo->id,
            'versionNumber' => $spiScheduledEntry->versionInfo->versionNo ?? null,
            'isAvailable' => $isAvailable,
            'link' => false,
        ];

        return $createStruct;
    }

    protected function scheduledEntry(ScheduledEntry $scheduledEntry, string $type): CreateStruct
    {
        $contentInfo = $this->contentService->loadContentInfo($scheduledEntry->content->id);

        $createStruct = new CreateStruct();
        $createStruct->ownerId = $scheduledEntry->user->id;
        $createStruct->type = 'DateBasedPublisher:' . $type;
        $createStruct->data = [
            'message' => '',
            'contentName' => $contentInfo->name,
            'contentId' => $contentInfo->id,
            'versionNumber' => $scheduledEntry->versionInfo->versionNo ?? null,
            'isAvailable' => true,
            'link' => false,
        ];

        return $createStruct;
    }
}

class_alias(NotificationFactory::class, 'EzSystems\DateBasedPublisher\SPI\ValueObject\NotificationFactory');
