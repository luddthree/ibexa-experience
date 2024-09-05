<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

use Ibexa\Contracts\SiteFactory\Values\Design\Template;

abstract class SiteData
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template|null */
    private $design;

    /** @var int|null */
    private $treeRootLocationId;

    /** @var string|null */
    private $siteName;

    /** @var \Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessData[] */
    private $publicAccesses = [];

    public function __construct(array $publicAccesses = null)
    {
        $this->publicAccesses = $publicAccesses ?? [new PublicAccessData()];
    }

    /**
     * @return \Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessData[]
     */
    public function getPublicAccesses(): array
    {
        return $this->publicAccesses;
    }

    public function addPublicAccess(PublicAccessData $publicAccessData): void
    {
        $this->publicAccesses[] = $publicAccessData;
    }

    public function removePublicAccess(PublicAccessData $publicAccessData): void
    {
        foreach ($this->publicAccesses as $key => $publicAccess) {
            if ($publicAccess == $publicAccessData) {
                unset($this->publicAccesses[$key]);
            }
        }
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(?string $siteName): void
    {
        $this->siteName = $siteName;
    }

    public function getDesign(): ?Template
    {
        return $this->design;
    }

    public function setDesign(?Template $design): void
    {
        $this->design = $design;
    }

    public function getTreeRootLocationId(): ?int
    {
        return $this->treeRootLocationId;
    }

    public function setTreeRootLocationId(?int $treeRootLocationId): void
    {
        $this->treeRootLocationId = $treeRootLocationId;
    }
}

class_alias(SiteData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SiteData');
