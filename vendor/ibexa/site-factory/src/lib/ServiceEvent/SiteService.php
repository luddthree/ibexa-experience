<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\ServiceEvent;

use Ibexa\Contracts\SiteFactory\Events\BeforeCreateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\BeforeDeleteSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\BeforeUpdateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\CreateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\DeleteSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\UpdateSiteEvent;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\ServiceDecorator\SiteServiceDecorator;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SiteService extends SiteServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        SiteServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createSite(SiteCreateStruct $createStruct): Site
    {
        $eventData = [
            $createStruct,
        ];

        $beforeEvent = new BeforeCreateSiteEvent(...$eventData);
        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSite();
        }

        $site = $beforeEvent->hasSite()
            ? $beforeEvent->getSite()
            : $this->innerService->createSite($createStruct);

        $this->eventDispatcher->dispatch(
            new CreateSiteEvent($site, ...$eventData)
        );

        return $site;
    }

    public function updateSite(Site $site, SiteUpdateStruct $siteUpdateStruct): Site
    {
        $eventData = [
            $site,
            $siteUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateSiteEvent(...$eventData);
        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedSite();
        }

        $updatedSite = $beforeEvent->hasUpdatedSite()
            ? $beforeEvent->getUpdatedSite()
            : $this->innerService->updateSite($site, $siteUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateSiteEvent($updatedSite, ...$eventData)
        );

        return $site;
    }

    public function deleteSite(Site $site): void
    {
        $eventData = [
            $site,
        ];

        $beforeEvent = new BeforeDeleteSiteEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteSite($site);

        $this->eventDispatcher->dispatch(
            new DeleteSiteEvent(...$eventData)
        );
    }
}

class_alias(SiteService::class, 'EzSystems\EzPlatformSiteFactory\ServiceEvent\SiteService');
