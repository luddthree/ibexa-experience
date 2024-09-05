<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\Event\PageRenderEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PageService
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    private $eventDispatcher;

    /**
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function dispatchRenderPageEvent(
        Content $content,
        Page $page,
        ?Location $location,
        Request $request
    ): void {
        $event = new PageRenderEvent($content, $location, $page, $request);
        $this->eventDispatcher->dispatch($event, PageEvents::PRE_RENDER);
    }
}

class_alias(PageService::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Service\PageService');
