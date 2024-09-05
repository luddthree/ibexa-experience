<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;

class RequestBlockConfiguration
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private $page;

    /** @var string */
    private $blockId;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null */
    private $language;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param string $blockId
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     */
    public function __construct(
        Location $location = null,
        VersionInfo $versionInfo = null,
        ContentInfo $contentInfo = null,
        Page $page = null,
        string $blockId = null,
        Language $language = null
    ) {
        $this->location = $location;
        $this->versionInfo = $versionInfo;
        $this->page = $page;
        $this->blockId = $blockId;
        $this->language = $language;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo|null
     */
    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     */
    public function setVersionInfo(VersionInfo $versionInfo)
    {
        $this->versionInfo = $versionInfo;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     */
    public function setContentInfo(ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page|null
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return string|null
     */
    public function getBlockId(): ?string
    {
        return $this->blockId;
    }

    /**
     * @param string $blockId
     */
    public function setBlockId(string $blockId)
    {
        $this->blockId = $blockId;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Language
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $language
     */
    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }
}

class_alias(RequestBlockConfiguration::class, 'EzSystems\EzPlatformPageBuilder\Data\RequestBlockConfiguration');
