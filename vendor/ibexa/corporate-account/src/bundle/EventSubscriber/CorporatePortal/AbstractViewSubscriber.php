<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\Specification\IsCorporate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractViewSubscriber implements EventSubscriberInterface
{
    private SiteAccessServiceInterface $siteAccessService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->configResolver = $configResolver;
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
        $pagelayout = $this->configResolver->getParameter('page_layout');

        if ($this->supports($view) && $this->isCorporatePortalSiteAccess()) {
            $this->configureView($view);

            $view->addParameters(['page_layout' => $pagelayout]);
        }
    }

    /**
     * Returns true if given $view is supported by subscriber.
     */
    abstract protected function supports(View $view): bool;

    abstract protected function configureView(View $view): void;

    private function isCorporatePortalSiteAccess(): bool
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();
        if ($currentSiteAccess === null) {
            return false;
        }

        return (new IsCorporate($this->siteAccessService))->isSatisfiedBy($currentSiteAccess);
    }
}
