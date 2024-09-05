<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\Core\Helper\ContentPreviewHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ActivePreviewInEditorialModeRequestSubscriber implements EventSubscriberInterface
{
    private ContentPreviewHelper $contentPreviewHelper;

    public function __construct(ContentPreviewHelper $contentPreviewHelper)
    {
        $this->contentPreviewHelper = $contentPreviewHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $isEditorialMode = $event->getRequest()->get('editorial_mode_bearer', false);

        if ($isEditorialMode) {
            $this->contentPreviewHelper->setPreviewActive(true);
        }
    }
}
