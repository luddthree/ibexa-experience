<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[] $publicAccesses
 * @property-read string $siteName
 * @property-read bool $copySiteSkeleton
 * @property-read \DateTimeInterface $siteCreated
 * @property-read int|null $siteSkeletonLocationId
 * @property-read int $parentLocationId
 */
final class SiteCreateStruct extends ValueObject
{
    /** @var string */
    protected $siteName;

    /** @var bool */
    protected $copySiteSkeleton;

    /** @var \DateTimeInterface */
    protected $siteCreated;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess */
    protected $publicAccesses;

    /** @var int|null */
    protected $siteSkeletonLocationId;

    /** @var int */
    protected $parentLocationId;

    /** @var int[] */
    protected $userGroupSkeletonIds;

    public function __construct(
        string $siteName,
        bool $copySiteSkeleton,
        array $publicAccesses,
        int $parentLocationId,
        array $userGroupSkeletonIds,
        ?DateTimeInterface $siteCreated = null,
        ?int $siteSkeletonLocationId = null
    ) {
        parent::__construct();

        $this->siteName = $siteName;
        $this->copySiteSkeleton = $copySiteSkeleton;
        $this->publicAccesses = $publicAccesses;
        $this->siteCreated = $siteCreated ?? (new DateTimeImmutable())->setTimestamp(time());
        $this->siteSkeletonLocationId = $siteSkeletonLocationId;
        $this->parentLocationId = $parentLocationId;
        $this->userGroupSkeletonIds = $userGroupSkeletonIds;
    }
}

class_alias(SiteCreateStruct::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\SiteCreateStruct');
