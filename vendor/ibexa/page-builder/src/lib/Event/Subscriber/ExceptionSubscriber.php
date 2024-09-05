<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\Core\Helper\ContentPreviewHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Kernel Exception Event Subscriber which restores proper SA context when needed.
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Ibexa\Core\Helper\ContentPreviewHelper
     */
    private $previewHelper;

    /**
     * @param \Ibexa\Core\Helper\ContentPreviewHelper $previewHelper
     */
    public function __construct(ContentPreviewHelper $previewHelper)
    {
        $this->previewHelper = $previewHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 10],
            ],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();

        $originalSiteAccess = $request->get('originalSiteAccess');

        if (empty($originalSiteAccess)) {
            return;
        }

        // restore original config scope (SiteAccess) to handle Exception
        $this->previewHelper->changeConfigScope($originalSiteAccess);
    }
}

class_alias(ExceptionSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\ExceptionSubscriber');
