<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteContext\PreviewUrlResolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteContext\Event\ResolveLocationPreviewUrlEvent;
use Ibexa\Contracts\SiteContext\Exception\UnresolvedPreviewUrlException;
use Ibexa\Contracts\SiteContext\PreviewUrlResolver\LocationPreviewUrlResolverInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class LocationPreviewUrlResolver implements LocationPreviewUrlResolverInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function resolveUrl(Location $location, array $context = []): string
    {
        $event = $this->eventDispatcher->dispatch(new ResolveLocationPreviewUrlEvent($location, $context));
        if ($event->getPreviewUrl() === null) {
            throw new UnresolvedPreviewUrlException(
                sprintf('Preview URL for location "%d" could not be resolved.', $location->id)
            );
        }

        return $event->getPreviewUrl();
    }
}
