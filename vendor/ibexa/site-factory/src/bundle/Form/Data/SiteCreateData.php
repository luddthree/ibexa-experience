<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

final class SiteCreateData extends SiteData
{
    /** @var bool */
    private $copySiteSkeleton;

    /** @var int|null */
    private $parentLocationId;

    public function __construct(
        ?array $publicAccesses = null,
        bool $copySiteSkeleton = true,
        ?int $parentLocationId = null
    ) {
        parent::__construct($publicAccesses);

        $this->copySiteSkeleton = $copySiteSkeleton;
        $this->parentLocationId = $parentLocationId;
    }

    public function getCopySiteSkeleton(): bool
    {
        return $this->copySiteSkeleton;
    }

    public function setCopySiteSkeleton(bool $copySiteSkeleton): void
    {
        $this->copySiteSkeleton = $copySiteSkeleton;
    }

    public function getParentLocationId(): ?int
    {
        return $this->parentLocationId;
    }

    public function setParentLocationId(?int $parentLocationId): void
    {
        $this->parentLocationId = $parentLocationId;
    }
}

class_alias(SiteCreateData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SiteCreateData');
