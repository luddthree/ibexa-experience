<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Region;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RegionProvider implements EventSubscriberInterface, RegionProviderInterface
{
    private RegionPoolFactoryInterface $factory;

    private ?RegionPoolInterface $pool = null;

    public function __construct(RegionPoolFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getRegion(string $identifier): RegionInterface
    {
        return $this->getPool()->getRegion($identifier);
    }

    /**
     * @return iterable<array-key,\Ibexa\Contracts\ProductCatalog\Values\RegionInterface>
     */
    public function getRegions(): iterable
    {
        return $this->getPool()->getRegions();
    }

    public function onScopeChange(ScopeChangeEvent $event): void
    {
        $this->pool = null;
    }

    private function getPool(): RegionPoolInterface
    {
        if ($this->pool === null) {
            $this->pool = $this->factory->createPool();
        }

        return $this->pool;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::CONFIG_SCOPE_CHANGE => 'onScopeChange',
        ];
    }
}
