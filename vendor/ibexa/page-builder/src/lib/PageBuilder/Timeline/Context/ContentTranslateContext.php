<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Context;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class ContentTranslateContext implements ContextInterface
{
    public const INTENT = 'translate';
    public const INTENT_WITHOUT_BASE_LANGUAGE = 'translate_without_base_language';

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var string */
    private $languageCode;

    /** @var string|null */
    private $baseLanguageCode;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $languageCode
     * @param string|null $baseLanguageCode
     */
    public function __construct(
        ?Location $location,
        Content $content,
        string $languageCode,
        ?string $baseLanguageCode
    ) {
        $this->content = $content;
        $this->languageCode = $languageCode;
        $this->location = $location;
        $this->baseLanguageCode = $baseLanguageCode;
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
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @return string|null
     */
    public function getBaseLanguageCode(): ?string
    {
        return $this->baseLanguageCode;
    }

    /**
     * @param string|null $baseLanguageCode
     */
    public function setBaseLanguageCode(?string $baseLanguageCode): void
    {
        $this->baseLanguageCode = $baseLanguageCode;
    }
}

class_alias(ContentTranslateContext::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Context\ContentTranslateContext');
