<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\SetContentStateEvent;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Event\PersonalizationEvent;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ObjectStateEventSubscriber implements EventSubscriberInterface
{
    private CoreContentService $coreContentService;

    private ContentServiceInterface $contentService;

    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    private SettingServiceInterface $settingService;

    public function __construct(
        CoreContentService $coreContentService,
        ContentServiceInterface $contentService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        SettingServiceInterface $settingService
    ) {
        $this->coreContentService = $coreContentService;
        $this->contentService = $contentService;
        $this->includedItemTypeResolver = $includedItemTypeResolver;
        $this->settingService = $settingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SetContentStateEvent::class => ['onSetContentState', PersonalizationEvent::DEFAULT_PRIORITY],
        ];
    }

    public function onSetContentState(SetContentStateEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $content = $this->coreContentService->loadContentByContentInfo($event->getContentInfo());
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->updateContent($content);
    }
}

class_alias(ObjectStateEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\ObjectStateEventSubscriber');
