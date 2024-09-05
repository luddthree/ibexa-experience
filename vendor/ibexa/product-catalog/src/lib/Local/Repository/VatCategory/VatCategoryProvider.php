<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class VatCategoryProvider implements EventSubscriberInterface, VatCategoryProviderInterface
{
    private VatCategoryPoolFactoryInterface $factory;

    private ?VatCategoryPoolInterface $pool = null;

    public function __construct(VatCategoryPoolFactoryInterface $categoryPoolFactory)
    {
        $this->factory = $categoryPoolFactory;
    }

    public function getVatCategory(string $region, string $identifier): VatCategoryInterface
    {
        return $this->getPool()->getVatCategory($region, $identifier);
    }

    public function getVatCategories(string $region): array
    {
        return $this->getPool()->getVatCategories($region);
    }

    private function getPool(): VatCategoryPoolInterface
    {
        if ($this->pool === null) {
            $this->pool = $this->factory->createPool();
        }

        return $this->pool;
    }

    public function onScopeChange(ScopeChangeEvent $event): void
    {
        $this->pool = null;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::CONFIG_SCOPE_CHANGE => 'onScopeChange',
        ];
    }
}
