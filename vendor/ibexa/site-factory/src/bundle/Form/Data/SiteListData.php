<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class SiteListData extends ValueObject
{
    /** @var string|null */
    private $searchQuery;

    /** @var int */
    private $page = 1;

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(?string $searchQuery): void
    {
        $this->searchQuery = $searchQuery;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }
}

class_alias(SiteListData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SiteListData');
