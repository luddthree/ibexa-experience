<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Segmentation\Loader\UserSegmentsForcedLoadStrategy;
use Ibexa\Segmentation\Loader\UserSegmentsLoadStrategyInterface;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Ibexa\Segmentation\Service\SegmentationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
final class SegmentPreviewSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Segmentation\Service\SegmentationService */
    private $segmentationService;

    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $handler;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Segmentation\Loader\UserSegmentsForcedLoadStrategy */
    private $userSegmentsLoadStrategy;

    /** @var \Ibexa\Segmentation\Loader\UserSegmentsLoadStrategyInterface|null */
    private $defaultUserSegmentsLoadStrategy;

    public function __construct(
        SegmentationService $segmentationService,
        HandlerInterface $handler,
        PermissionResolver $permissionResolver,
        UserSegmentsForcedLoadStrategy $userSegmentsLoadStrategy,
        ?UserSegmentsLoadStrategyInterface $defaultUserSegmentsLoadStrategy = null
    ) {
        $this->segmentationService = $segmentationService;
        $this->handler = $handler;
        $this->permissionResolver = $permissionResolver;
        $this->userSegmentsLoadStrategy = $userSegmentsLoadStrategy;
        $this->defaultUserSegmentsLoadStrategy = $defaultUserSegmentsLoadStrategy;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onController', 255],
            KernelEvents::FINISH_REQUEST => ['onFinishRequest', 255],
        ];
    }

    public function onController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->isPageBuilderPreview($request, $event->getRequestType())) {
            return;
        }

        /** @var array{pagePreview: array{segmentId?: int}} $previewParameters */
        $previewParameters = $request->request->get('parameters', []);

        if (!isset($previewParameters['pagePreview']['segmentId'])) {
            return;
        }

        $userId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $previewSegmentId = (int) $previewParameters['pagePreview']['segmentId'];

        // configure strategy to return selected segment
        $previewSegment = $this->handler->loadSegmentById($previewSegmentId);
        $this->userSegmentsLoadStrategy->setSegments($userId, [$previewSegment]);

        // store default strategy for further rollback
        $this->defaultUserSegmentsLoadStrategy = $this->segmentationService->getUserSegmentsLoadStrategy();

        // reconfigure SegmentationService to use new strategy
        $this->segmentationService->setUserSegmentsLoadStrategy($this->userSegmentsLoadStrategy);
    }

    public function onFinishRequest(FinishRequestEvent $event): void
    {
        $request = $event->getRequest();

        if (null === $this->defaultUserSegmentsLoadStrategy) {
            return;
        }

        if (!$this->isPageBuilderPreview($request, $event->getRequestType())) {
            return;
        }

        // rollback old stategy
        $this->segmentationService->setUserSegmentsLoadStrategy($this->defaultUserSegmentsLoadStrategy);

        // cleanup
        $this->defaultUserSegmentsLoadStrategy = null;
    }

    private function isPageBuilderPreview(Request $request, int $requestType): bool
    {
        $routeName = $request->attributes->get('_route');

        return $requestType === HttpKernelInterface::MASTER_REQUEST
            && $routeName === 'ibexa.page_builder.block.siteaccess_preview';
    }
}

class_alias(SegmentPreviewSubscriber::class, 'Ibexa\Platform\Segmentation\Event\Subscriber\SegmentPreviewSubscriber');
