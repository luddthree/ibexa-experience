<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Design;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Template extends ValueObject
{
    /** @var string */
    public $siteAccessGroup;

    /** @var string */
    public $name;

    /** @var string */
    public $identifier;

    /** @var string */
    public $thumbnail;

    /** @var string */
    public $design;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[] */
    public $userGroupSkeletonLocations;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    public $siteSkeletonLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    public $parentLocation;

    public function __construct(
        string $identifier,
        string $siteAccessGroup,
        string $name,
        string $thumbnail,
        string $design,
        array $userGroupSkeletonLocations,
        ?Location $siteSkeletonLocation = null,
        ?Location $parentLocation = null
    ) {
        $this->identifier = $identifier;
        $this->siteAccessGroup = $siteAccessGroup;
        $this->name = $name;
        $this->thumbnail = $thumbnail;
        $this->design = $design;
        $this->userGroupSkeletonLocations = $userGroupSkeletonLocations;
        $this->siteSkeletonLocation = $siteSkeletonLocation;
        $this->parentLocation = $parentLocation;
    }

    public static function fromConfiguration(TemplateConfiguration $templateConfiguration): Template
    {
        return new self(
            $templateConfiguration->getIdentifier(),
            $templateConfiguration->getSiteAccessGroup(),
            $templateConfiguration->getName(),
            $templateConfiguration->getThumbnail(),
            $templateConfiguration->getDesign(),
            $templateConfiguration->getUserGroupSkeletonLocations(),
            $templateConfiguration->getSiteSkeletonLocation(),
            $templateConfiguration->getParentLocation()
        );
    }

    public function getSiteAccessGroup(): string
    {
        return $this->siteAccessGroup;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getDesign(): string
    {
        return $this->design;
    }

    public function getSiteSkeletonLocation(): ?Location
    {
        return $this->siteSkeletonLocation;
    }

    public function getParentLocation(): ?Location
    {
        return $this->parentLocation;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function getUserGroupSkeletonLocations(): array
    {
        return $this->userGroupSkeletonLocations;
    }
}

class_alias(Template::class, 'EzSystems\EzPlatformSiteFactory\Values\Design\Template');
