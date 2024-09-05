<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use Ibexa\Rest\Value as RestValue;

/**
 * @internal
 */
final class Link extends RestValue
{
    private string $rel;

    private string $routeName;

    /**
     * @var array<string, mixed>
     */
    private array $routeParameters;

    private string $mediaType;

    /**
     * @param non-empty-string $rel
     * @param non-empty-string $routeName
     * @param array<string, mixed> $routeParameters
     */
    public function __construct(
        string $rel,
        string $mediaType,
        string $routeName,
        array $routeParameters = []
    ) {
        $this->rel = $rel;
        $this->routeName = $routeName;
        $this->routeParameters = $routeParameters;
        $this->mediaType = $mediaType;
    }

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }
}
