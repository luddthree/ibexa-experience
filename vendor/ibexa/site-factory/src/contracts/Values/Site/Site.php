<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Site extends ValueObject
{
    public const STATUS_OFFLINE = 0;
    public const STATUS_ONLINE = 1;

    /** @var int */
    public $id;

    /** @var int */
    public $status = self::STATUS_OFFLINE;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[] */
    public $publicAccesses;

    /** @var string */
    public $name;

    /** @var \DateTimeInterface */
    public $created;

    /** @var string[] */
    public $languages;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template */
    public $template;

    public function getDesignName(): string
    {
        return $this->getFirstSiteAccess()->getDesign();
    }

    public function getTreeRootLocationId(): int
    {
        return $this->getFirstSiteAccess()->getTreeRootLocationId();
    }

    public function getFirstSiteAccess(): PublicAccess
    {
        if (empty($this->publicAccesses)) {
            new \RuntimeException('Can\'t fetch design. Site Accesses list is empty.');
        }

        return $this->publicAccesses[0];
    }
}

class_alias(Site::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\Site');
