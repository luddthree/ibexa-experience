<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\HttpCache\Handler\TagHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TargetingBlockRenderSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    /** @var \Ibexa\HttpCache\Handler\TagHandler */
    private $tagHandler;

    public function __construct(
        PermissionResolver $permissionResolver,
        UserService $userService,
        LocationService $locationService,
        ContentService $contentService,
        SegmentationServiceInterface $segmentationService,
        TagHandler $tagHandler
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->segmentationService = $segmentationService;
        $this->tagHandler = $tagHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('targeting') => 'onBlockPreRender',
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $contentMap = $this->getContentMap($blockValue);

        $user = $this->getCurrentUser();
        $segments = $this->segmentationService->loadSegmentsAssignedToUser($user);

        $segmentIds = array_column($segments, 'id');

        $locationId = null;
        foreach ($contentMap as $segmentData) {
            if (in_array($segmentData['segmentId'], $segmentIds, true)) {
                $locationId = $segmentData['locationId'];
                break;
            }
        }

        if (null === $locationId) {
            $defaultContent = $this->getDefaultContent($blockValue);
            $locationId = $defaultContent->mainLocationId;
        }

        $location = $this->locationService->loadLocation($locationId);
        $parameters = ['location' => $location];

        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $renderRequest */
        $renderRequest = $event->getRenderRequest();
        $renderRequest->setParameters(array_merge($renderRequest->getParameters(), $parameters));

        foreach ($segmentIds as $segmentId) {
            $this->tagHandler->addTags(['sg' . $segmentId]);
        }

        $this->tagHandler->addLocationTags([$locationId]);

        // @todo This is hack for problem with tags invalidation when segment is assigned/unassigned to user.
        $this->tagHandler->addTags(['tb']);
    }

    private function getDefaultContent(BlockValue $blockValue): ContentInfo
    {
        $defaultContentAttribute = $blockValue->getAttribute('default_content_id');
        $defaultContentId = (int)$defaultContentAttribute->getValue();

        return $this->contentService->loadContentInfo($defaultContentId);
    }

    private function getContentMap(BlockValue $blockValue): array
    {
        $contentMapAttribute = $blockValue->getAttribute('content_map');

        return json_decode($contentMapAttribute->getValue(), true);
    }

    private function getCurrentUser(): User
    {
        $userId = $this->permissionResolver->getCurrentUserReference()->getUserId();

        return $this->userService->loadUser($userId);
    }
}

class_alias(TargetingBlockRenderSubscriber::class, 'Ibexa\Platform\Segmentation\Event\Subscriber\TargetingBlockRenderSubscriber');
