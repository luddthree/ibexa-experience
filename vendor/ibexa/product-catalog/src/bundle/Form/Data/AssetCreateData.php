<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class AssetCreateData
{
    /** @var string[] */
    private array $uris;

    /** @var string[]|null */
    private ?array $tags;

    /**
     * @param string[] $uris
     * @param string[]|null $tags
     */
    public function __construct(array $uris = [], ?array $tags = null)
    {
        $this->uris = $uris;
        $this->tags = $tags;
    }

    /**
     * @return string[]
     */
    public function getUris(): array
    {
        return $this->uris;
    }

    /**
     * @param string[] $uris
     */
    public function setUris(array $uris): void
    {
        $this->uris = $uris;
    }

    /**
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param string[]|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }
}
