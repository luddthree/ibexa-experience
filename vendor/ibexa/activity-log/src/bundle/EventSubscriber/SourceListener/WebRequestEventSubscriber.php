<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\SourceListener;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class WebRequestEventSubscriber implements EventSubscriberInterface
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService
    ) {
        $this->activityLogService = $activityLogService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                'onKernelRequest', -10,
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->attributes->getBoolean('is_rest_request')) {
            $context = $this->activityLogService->prepareContext('rest');
        } elseif ($event->getRequest()->attributes->get('_route') === 'overblog_graphql_endpoint') {
            $context = $this->activityLogService->prepareContext('graphql');
        } else {
            $context = $this->activityLogService->prepareContext('web');
        }

        $ip = $event->getRequest()->getClientIp();
        if ($ip !== null) {
            $context->setIp($ip);
        }
    }
}
