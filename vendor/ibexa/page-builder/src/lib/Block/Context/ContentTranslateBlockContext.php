<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Block\Context;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;

/**
 * Provides content for block execution in specific Location.
 *
 * @internal
 */
class ContentTranslateBlockContext implements BlockContextInterface
{
    public const INTENT = 'translate';
    public const INTENT_WITHOUT_BASE_LANGUAGE = 'translate_without_base_language';

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var string */
    private $languageCode;

    /** @var string|null */
    private $baseLanguageCode;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private $page;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $languageCode
     * @param string|null $baseLanguageCode
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function __construct(
        Location $location,
        Content $content,
        string $languageCode,
        ?string $baseLanguageCode,
        Page $page
    ) {
        $this->location = $location;
        $this->content = $content;
        $this->languageCode = $languageCode;
        $this->baseLanguageCode = $baseLanguageCode;
        $this->page = $page;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getLocation(): Location
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
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return string|null
     */
    public function getBaseLanguageCode(): ?string
    {
        return $this->baseLanguageCode;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }
}

class_alias(ContentTranslateBlockContext::class, 'EzSystems\EzPlatformPageBuilder\Block\Context\ContentTranslateBlockContext');
