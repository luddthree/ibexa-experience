<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Scheduler\Controller;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntryList;
use Ibexa\Core\MVC\Symfony\Routing\ChainRouter;
use Ibexa\Rest\Server\Values;
use Ibexa\Scheduler\REST\Server\RestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * DateBasedPublisher Controller class.
 */
class DateBasedPublisherController extends RestController
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    protected $datebasedpublisherService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var string */
    protected $defaultSiteAccess;

    public function __construct(
        DateBasedPublishServiceInterface $datebasedpublisherService,
        ContentService $contentService,
        ChainRouter $router,
        $defaultSiteAccess
    ) {
        $this->datebasedpublisherService = $datebasedpublisherService;
        $this->contentService = $contentService;
        $this->router = $router;
        $this->defaultSiteAccess = $defaultSiteAccess;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getScheduledVersionAction($contentId, $versionNumber, Request $request)
    {
        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNumber);

        return $this->datebasedpublisherService->getScheduledPublish($versionInfo->id);
    }

    public function getScheduledContentVersionsAction($contentId, $page, $limit, Request $request)
    {
        $scheduledEntries = $this->datebasedpublisherService->getVersionsEntriesForContent(
            $contentId,
            (int)$page,
            (int)$limit
        );
        $total = $this->datebasedpublisherService->countVersionsEntriesForContent($contentId);

        return new ScheduledEntryList([
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'scheduledEntries' => $scheduledEntries,
        ]);
    }

    public function getScheduledVersionsAction($page, $limit, Request $request)
    {
        $scheduledEntries = $this->datebasedpublisherService->getScheduledVersions($page, $limit);
        $total = $this->datebasedpublisherService->countScheduledEntries();

        return new ScheduledEntryList([
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'scheduledEntries' => $scheduledEntries,
        ]);
    }

    public function getUserScheduledVersionsAction($page, $limit, Request $request)
    {
        $scheduledEntries = $this->datebasedpublisherService->getUserScheduledVersions($page, $limit);
        $total = $this->datebasedpublisherService->countUserScheduledVersions();

        return new ScheduledEntryList([
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'scheduledEntries' => $scheduledEntries,
        ]);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function rescheduleVersionAction($contentId, $versionNumber, $publicationTimestamp, Request $request)
    {
        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNumber);
        $publicationDate = \DateTime::createFromFormat('U', $publicationTimestamp);

        $scheduledEntry = $this->datebasedpublisherService->getScheduledPublish($versionInfo->id);

        $this->datebasedpublisherService->updateScheduledPublish($scheduledEntry, $publicationDate);

        return new Values\NoContent();
    }

    public function scheduleVersionAction($contentId, $versionNumber, $publicationTimestamp, Request $request)
    {
        $siteAccessName = $request->get('siteAccessName', $this->defaultSiteAccess);

        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNumber);
        $publicationDate = \DateTime::createFromFormat('U', $publicationTimestamp);

        $this->datebasedpublisherService->schedulePublish($versionInfo, $publicationDate);

        return new Values\NoContent();
    }

    public function unscheduleVersionAction($contentId, $versionNumber, Request $request)
    {
        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNumber);

        $this->datebasedpublisherService->unschedulePublish($versionInfo->id);

        return new Values\NoContent();
    }
}

class_alias(DateBasedPublisherController::class, 'EzSystems\DateBasedPublisherBundle\Controller\DateBasedPublisherController');
