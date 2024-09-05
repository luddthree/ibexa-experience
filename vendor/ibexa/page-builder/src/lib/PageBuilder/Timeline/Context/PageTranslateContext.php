<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Context;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;

class PageTranslateContext extends ContentTranslateContext implements PageContextInterface
{
    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private $page;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $languageCode
     * @param string|null $baseLanguageCode
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function __construct(
        ?Location $location,
        Content $content,
        string $languageCode,
        ?string $baseLanguageCode,
        Page $page
    ) {
        parent::__construct($location, $content, $languageCode, $baseLanguageCode);

        $this->page = $page;
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
}

class_alias(PageTranslateContext::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Context\PageTranslateContext');
