<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Context;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;

/**
 * Provides content for block execution in specific Location.
 */
class ContentViewBlockContext implements BlockContextInterface
{
    public const INTENT = 'view';

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var int */
    private $versionNo;

    /** @var string */
    private $languageCode;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private $page;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param int $versionNo
     * @param string $languageCode
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function __construct(
        ?Location $location,
        Content $content,
        int $versionNo,
        string $languageCode,
        Page $page
    ) {
        $this->location = $location;
        $this->content = $content;
        $this->versionNo = $versionNo;
        $this->languageCode = $languageCode;
        $this->page = $page;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getVersionNo(): int
    {
        return $this->versionNo;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }
}

class_alias(ContentViewBlockContext::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Context\ContentViewBlockContext');
