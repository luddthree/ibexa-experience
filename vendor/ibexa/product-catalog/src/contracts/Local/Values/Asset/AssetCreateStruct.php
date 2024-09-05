<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Asset;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class AssetCreateStruct extends ValueObject
{
    private ?string $uri;

    /** @var array<string, mixed> */
    private array $tags;

    /**
     * @param array<string, mixed> $tags
     */
    public function __construct(?string $uri = null, array $tags = [])
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
     * @return array<string, mixed>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array<string, mixed> $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
