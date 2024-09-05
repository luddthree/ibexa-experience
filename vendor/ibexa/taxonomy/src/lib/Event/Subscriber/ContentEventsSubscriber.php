<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\AdminUi\Event\ContentProxyCreateEvent;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Taxonomy\Exception\TaxonomyEntryInvalidParentException;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class ContentEventsSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyConfiguration $taxonomyConfiguration;

    private LocationService $locationService;

    private ContentService $contentService;

    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyConfiguration $taxonomyConfiguration,
        LocationService $locationService,
        ContentService $contentService,
        ?LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->taxonomyService = $taxonomyService;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DeleteContentEvent::class => 'onContentDelete',
            PublishVersionEvent::class => 'onVersionPublish',
            ContentProxyCreateEvent::class => 'onContentProxyCreate',
        ];
    }

    public function onContentDelete(DeleteContentEvent $event): void
    {
        $content = $event->getContentInfo();
        $contentType = $content->getContentType();

        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return;
        }

        try {
            $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($content->id);
        } catch (TaxonomyEntryNotFoundException $e) {
            $this->logger->error(
                "Cannot find TaxonomyEntry for Content ID: {$content->id}. "
                . 'This may be an orphan Content item left after corresponding TaxonomyEntry was removed.',
                [
                    'exception' => $e,
                ],
            );

            return;
        }

        // This operation will be performed without the limit and may result in a timeout when running in FPM.
        // Repository Event Listeners are running outside the main transaction and
        // breaking operation here would result in creating orphan content items.
        // UI has configurable size of the subtree that can be removed at once to prevent timeouts.

        $childrenEntry = $this->taxonomyService->loadEntryChildren($taxonomyEntry, null);

        foreach ($childrenEntry as $childEntry) {
            $this->contentService->deleteContent($childEntry->content->contentInfo);
        }

        $this->taxonomyService->removeEntry($taxonomyEntry);

        $this->entityManager->flush();
    }

    public function onVersionPublish(PublishVersionEvent $event): void
    {
        $content = $event->getContent();
        $versionInfo = $event->getVersionInfo();

        // we only create entry once when first version is published
        if ($versionInfo->versionNo > 1) {
            return;
        }

        $contentType = $content->getContentType();

        try {
            $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
        } catch (TaxonomyNotFoundException $exception) {
            // this will occur for all content types so no need to spam logs
            return;
        }

        $createStruct = new TaxonomyEntryCreateStruct(
            $this->taxonomyConfiguration->getEntryIdentifierFieldFromContent($content),
            $taxonomy,
            $this->taxonomyConfiguration->getEntryParentFieldFromContent($content),
            $content
        );

        $this->taxonomyService->createEntry($createStruct);
    }

    public function onContentProxyCreate(ContentProxyCreateEvent $event): void
    {
        $options = $event->getOptions();
        if (!$options->has(ContentProxyCreateEvent::OPTION_CONTENT_DRAFT)) {
            return;
        }

        /** @var \Ibexa\Core\Repository\Values\Content\Content $contentDraft */
        $contentDraft = $event->getOptions()->get(ContentProxyCreateEvent::OPTION_CONTENT_DRAFT);
        $contentType = $contentDraft->getContentType();

        try {
            $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
        } catch (TaxonomyNotFoundException $exception) {
            return;
        }

        $taxonomyParentLocation = $this->locationService->loadLocationByRemoteId(
            $this->taxonomyConfiguration->getConfigForTaxonomy(
                $taxonomy,
                TaxonomyConfiguration::CONFIG_PARENT_LOCATION_REMOTE_ID
            )
        );

        if ($taxonomyParentLocation->id !== $event->getParentLocationId()) {
            throw TaxonomyEntryInvalidParentException::createWithTaxonomyName($taxonomy);
        }

        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $fieldIdentifier = $this->taxonomyConfiguration->getFieldMappings($taxonomy)['parent'];
        $contentUpdateStruct->setField($fieldIdentifier, new Value($options->get('parent_entry')));

        $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct, []);
    }
}
