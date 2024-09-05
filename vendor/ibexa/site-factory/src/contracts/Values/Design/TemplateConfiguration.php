<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Design;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class TemplateConfiguration extends ValueObject
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $siteAccessGroup;

    /** @var string */
    private $name;

    /** @var string */
    private $thumbnail;

    /** @var string */
    private $design;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[] */
    public $userGroupSkeletonLocations;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $siteSkeletonLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $parentLocation;

    private function __construct(
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

    public static function fromTemplate(
        string $identifier,
        string $design,
        array $template,
        array $userGroupSkeletonLocations,
        ?Location $siteSkeleton = null,
        ?Location $parentLocation = null
    ): TemplateConfiguration {
        return new self(
            $identifier,
            $template['siteaccess_group'],
            $template['name'],
            $template['thumbnail'],
            $design,
            $userGroupSkeletonLocations,
            $siteSkeleton,
            $parentLocation
        );
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSiteAccessGroup(): string
    {
        return $this->siteAccessGroup;
    }

    public function getName(): string
    {
        return $this->name;
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

class_alias(TemplateConfiguration::class, 'EzSystems\EzPlatformSiteFactory\Values\Design\TemplateConfiguration');
