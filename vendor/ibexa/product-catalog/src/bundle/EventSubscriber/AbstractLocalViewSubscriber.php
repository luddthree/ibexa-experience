<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractLocalViewSubscriber implements EventSubscriberInterface
{
    private SiteAccessServiceInterface $siteAccessService;

    private ConfigProviderInterface $configProvider;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->configProvider = $configProvider;
    }

    /**
     * @return array<string,string>
     */
    final public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    final public function onPreContentView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();

        if ($this->supports($view) && $this->isAdminSiteAccess() && $this->isLocal()) {
            $this->configureView($view);
        }
    }

    /**
     * Returns true if given $view is supported by subscriber.
     */
    abstract protected function supports(View $view): bool;

    abstract protected function configureView(View $view): void;

    /**
     * Returns true if current product catalog implementation is editable.
     */
    protected function isLocal(): bool
    {
        return $this->configProvider->getEngineType() === 'local';
    }

    /**
     * Returns true if current siteaccess is administrative.
     */
    private function isAdminSiteAccess(): bool
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();

        if ($currentSiteAccess === null) {
            return false;
        }

        // Workaround: for some reason value returned from SiteAccessServiceInterface::getCurrent() doesn't
        // contain groups
        $currentSiteAccess = $this->siteAccessService->get($currentSiteAccess->name);
        foreach ($currentSiteAccess->groups as $group) {
            if ($group->getName() === IbexaAdminUiBundle::ADMIN_GROUP_NAME) {
                return true;
            }
        }

        return false;
    }
}
