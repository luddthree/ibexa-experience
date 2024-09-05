<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Page extends ValueObject
{
    public const STATUS_ONLINE = 1;

    /** @var int */
    protected $status;

    /** @var string */
    protected $name;

    /** @var string[] */
    protected $languages;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    protected $treeRootLocation;

    /**
     * @param string[] $languages
     */
    public function __construct(
        string $name,
        array $languages,
        ?Location $treeRootLocation
    ) {
        $this->name = $name;
        $this->treeRootLocation = $treeRootLocation;
        $this->languages = $languages;
        $this->status = self::STATUS_ONLINE;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * Tree root Location will be empty when user does not have permission to load it.
     */
    public function getTreeRootLocation(): ?Location
    {
        return $this->treeRootLocation;
    }
}

class_alias(Page::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\Page');
