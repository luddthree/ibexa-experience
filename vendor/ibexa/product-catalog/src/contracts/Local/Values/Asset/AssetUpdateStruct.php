<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Asset;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class AssetUpdateStruct extends ValueObject
{
    private ?string $uri;

    /** @var string[]|null */
    private ?array $tags;

    /**
     * @param string[]|null $tags
     */
    public function __construct(?string $uri = null, ?array $tags = null)
    {
        parent::__construct();

        $this->uri = $uri;
        $this->tags = $tags;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): void
    {
        $this->uri = $uri;
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
