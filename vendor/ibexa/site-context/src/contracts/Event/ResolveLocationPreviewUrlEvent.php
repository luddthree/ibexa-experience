<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteContext\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Contracts\EventDispatcher\Event;

final class ResolveLocationPreviewUrlEvent extends Event
{
    private Location $location;

    /** @var array<string, mixed> */
    private array $context;

    private ?string $previewUrl = null;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(Location $location, array $context = [])
    {
        $this->location = $location;
        $this->context = $context;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    public function setPreviewUrl(?string $previewUrl): void
    {
        $this->previewUrl = $previewUrl;
    }
}
