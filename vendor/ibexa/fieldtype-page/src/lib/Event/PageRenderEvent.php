<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class PageRenderEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $location;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private $page;

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(
        Content $content,
        ?Location $location,
        Page $page,
        Request $request
    ) {
        $this->content = $content;
        $this->location = $location;
        $this->page = $page;
        $this->request = $request;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     */
    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     */
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}

class_alias(PageRenderEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\PageRenderEvent');
