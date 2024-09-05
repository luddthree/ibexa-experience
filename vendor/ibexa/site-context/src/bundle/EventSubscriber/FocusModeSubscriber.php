<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\EventSubscriber;

use Ibexa\Contracts\AdminUi\Event\FocusModeChangedEvent;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FocusModeSubscriber implements EventSubscriberInterface
{
    private SiteContextServiceInterface $siteContextService;

    public function __construct(SiteContextServiceInterface $siteContextService)
    {
        $this->siteContextService = $siteContextService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FocusModeChangedEvent::class => 'onFocusModeChanged',
        ];
    }

    public function onFocusModeChanged(FocusModeChangedEvent $event): void
    {
        if ($event->isEnabled()) {
            $this->siteContextService->setFullscreenMode(true);
        }
    }
}
