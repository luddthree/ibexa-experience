<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Context;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

class ContentCreateContext implements ContextInterface
{
    public const INTENT = 'create';

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType */
    private $contentType;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $parentLocation;

    /** @var string */
    private $languageCode;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $parentLocation
     * @param string $languageCode
     */
    public function __construct(
        ContentType $contentType,
        Location $parentLocation,
        string $languageCode
    ) {
        $this->contentType = $contentType;
        $this->parentLocation = $parentLocation;
        $this->languageCode = $languageCode;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     */
    public function setContentType(ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getParentLocation(): Location
    {
        return $this->parentLocation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $parentLocation
     */
    public function setParentLocation(Location $parentLocation): void
    {
        $this->parentLocation = $parentLocation;
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
}

class_alias(ContentCreateContext::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Context\ContentCreateContext');
