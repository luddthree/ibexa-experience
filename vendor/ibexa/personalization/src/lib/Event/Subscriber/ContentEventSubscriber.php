<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CopyContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\HideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\RevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentMetadataEvent;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Query\QueryFactoryInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Event\PersonalizationEvent;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentEventSubscriber extends AbstractRepositoryEventSubscriber implements EventSubscriberInterface
{
    private PermissionResolver $permissionResolver;

    private SettingServiceInterface $settingService;

    public function __construct(
        CoreContentService $coreContentService,
        ContentServiceInterface $contentService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        LocationService $locationService,
        Repository $repository,
        PermissionResolver $permissionResolver,
        SettingServiceInterface $settingService,
        QueryFactoryInterface $queryFactory,
        SearchService $searchService
    ) {
        parent::__construct(
            $coreContentService,
            $contentService,
            $includedItemTypeResolver,
            $locationService,
            $repository,
            $queryFactory,
            $searchService
        );

        $this->permissionResolver = $permissionResolver;
        $this->settingService = $settingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeDeleteContentEvent::class => ['onBeforeDeleteContent', PersonalizationEvent::DEFAULT_PRIORITY],
            BeforeDeleteTranslationEvent::class => ['onBeforeDeleteTranslation', PersonalizationEvent::DEFAULT_PRIORITY],
            HideContentEvent::class => ['onHideContent', PersonalizationEvent::DEFAULT_PRIORITY],
            RevealContentEvent::class => ['onRevealContent', PersonalizationEvent::DEFAULT_PRIORITY],
            UpdateContentMetadataEvent::class => ['onUpdateContentMetadata', PersonalizationEvent::DEFAULT_PRIORITY],
            CopyContentEvent::class => ['onCopyContent', PersonalizationEvent::DEFAULT_PRIORITY],
            PublishVersionEvent::class => ['onPublishVersion', PersonalizationEvent::DEFAULT_PRIORITY],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onBeforeDeleteContent(BeforeDeleteContentEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $contentInfo = $event->getContentInfo();
        if (!$this->canDelete($contentInfo)) {
            throw new UnauthorizedException('content', 'remove', ['contentId' => $contentInfo->getId()]);
        }

        $this->deleteContentWithChildrenFromAllLocations($contentInfo);

        $relations = $this->getContentRelations($contentInfo);
        if (empty($relations)) {
            return;
        }

        $this->contentService->updateContentItems($relations);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onBeforeDeleteTranslation(BeforeDeleteTranslationEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $contentInfo = $event->getContentInfo();
        $languageCodes = [$event->getLanguageCode()];

        if (!$this->canDelete($contentInfo, $languageCodes)) {
            throw new UnauthorizedException('content', 'remove', ['contentId' => $contentInfo->getId()]);
        }

        $content = $this->coreContentService->loadContentByContentInfo($event->getContentInfo());
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->deleteContent($content, $languageCodes);
    }

    public function onHideContent(HideContentEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->deleteContentWithChildrenFromAllLocations($event->getContentInfo());

        $relations = $this->getContentRelations($event->getContentInfo());
        if (empty($relations)) {
            return;
        }

        $this->contentService->updateContentItems($relations);
    }

    public function onRevealContent(RevealContentEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $this->updateContentWithChildrenFromAllLocationsAndRelations(
            $event->getContentInfo()
        );
    }

    public function onUpdateContentMetadata(UpdateContentMetadataEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $content = $event->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->updateContent($event->getContent());
    }

    public function onCopyContent(CopyContentEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $content = $event->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->updateContent($content);
    }

    public function onPublishVersion(PublishVersionEvent $event): void
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return;
        }

        $content = $event->getContent();
        if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
            return;
        }

        $this->contentService->updateContent($content, $event->getTranslations());
    }

    /**
     * @param array<string>|null $languageCodes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function canDelete(
        ContentInfo $contentInfo,
        ?array $languageCodes = null
    ): bool {
        if (empty($languageCodes)) {
            $versionInfo = $this->coreContentService->loadVersionInfo($contentInfo);
            $languageCodes = $this->getTranslationsToDelete($versionInfo->getLanguages());
        }

        $target = (new Target\Version())->deleteTranslations($languageCodes);

        return $this->permissionResolver->canUser('content', 'remove', $contentInfo, [$target]);
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language> $languages
     *
     * @return array<string>
     */
    private function getTranslationsToDelete(iterable $languages): array
    {
        $translations = [];
        foreach ($languages as $language) {
            $translations[] = $language->getLanguageCode();
        }

        return $translations;
    }
}

class_alias(ContentEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\ContentEventSubscriber');
