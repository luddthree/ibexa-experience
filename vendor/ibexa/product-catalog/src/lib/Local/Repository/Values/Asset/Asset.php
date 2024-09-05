<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\Asset;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;

final class Asset implements AssetInterface
{
    private Content $content;

    private string $identifier;

    private string $uri;

    /** @var array<string, mixed> */
    private array $tags;

    /**
     * @param array<string, mixed> $tags
     */
    public function __construct(Content $content, string $identifier, string $uri, array $tags = [])
    {
        $this->content = $content;
        $this->identifier = $identifier;
        $this->uri = $uri;
        $this->tags = $tags;
    }

    public function getName(): string
    {
        return $this->content->getName();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
