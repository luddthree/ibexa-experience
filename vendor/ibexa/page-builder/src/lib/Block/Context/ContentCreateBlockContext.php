<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Block\Context;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;

/**
 * Provides content for block execution in specific Location.
 *
 * @internal
 */
class ContentCreateBlockContext implements BlockContextInterface
{
    public const INTENT = 'create';

    /** @var string */
    private $languageCode;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType */
    private $contentType;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $parentLocation;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private $page;

    /**
     * @param string $languageCode
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $parentLocation
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function __construct(
        string $languageCode,
        ContentType $contentType,
        Location $parentLocation,
        Page $page
    ) {
        $this->languageCode = $languageCode;
        $this->contentType = $contentType;
        $this->parentLocation = $parentLocation;
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getParentLocation(): Location
    {
        return $this->parentLocation;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }
}

class_alias(ContentCreateBlockContext::class, 'EzSystems\EzPlatformPageBuilder\Block\Context\ContentCreateBlockContext');
