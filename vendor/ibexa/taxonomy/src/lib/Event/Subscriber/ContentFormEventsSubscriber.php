<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Taxonomy\Extractor\TaxonomyEntryExtractorInterface;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentFormEventsSubscriber implements EventSubscriberInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyEntryExtractorInterface $taxonomyEntryExtractor;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyEntryExtractorInterface $taxonomyEntryExtractor,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyEntryExtractor = $taxonomyEntryExtractor;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentFormEvents::CONTENT_CANCEL => ['onContentFormCancel', -10],
        ];
    }

    public function onContentFormCancel(FormActionEvent $event): void
    {
        $data = $event->getData();
        if (!$data instanceof ContentUpdateData || !$event->hasPayload('content')) {
            return;
        }

        if (null !== $event->getOption('referrerLocation')) {
            return;
        }

        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($data->contentDraft->getContentType())) {
            return;
        }

        $contentInfo = $this->taxonomyEntryExtractor->extractEntryParentFromContentUpdateData($data);
        if (null === $contentInfo) {
            return;
        }

        $url = $this->urlGenerator->generate(
            'ibexa.content.view',
            [
                'contentId' => $contentInfo->id,
                'locationId' => $contentInfo->mainLocationId,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $event->setResponse(new RedirectResponse($url));
    }
}
