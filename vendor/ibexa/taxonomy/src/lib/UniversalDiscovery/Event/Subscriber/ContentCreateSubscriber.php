<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\UniversalDiscovery\Event\Subscriber;

use Ibexa\AdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentCreateSubscriber implements EventSubscriberInterface
{
    private ContentTypeService $contentTypeService;

    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        ContentTypeService $contentTypeService,
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve', -10],
        ];
    }

    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        if ($event->getConfigName() !== 'create') {
            return;
        }

        $config = $event->getConfig();
        $allowedContentTypes = $config['allowed_content_types'];

        $eligibleContentTypes = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroup);
            if ($contentTypes instanceof \Traversable) {
                $contentTypes = iterator_to_array($contentTypes);
            }

            $contentTypes = array_filter(
                $contentTypes,
                fn (ContentType $contentType): bool => !$this
                    ->taxonomyConfiguration
                    ->isContentTypeAssociatedWithTaxonomy($contentType)
            );

            $eligibleContentTypes = array_merge(
                $eligibleContentTypes,
                array_column($contentTypes, 'identifier')
            );
        }

        $config['allowed_content_types'] = $allowedContentTypes !== null
            ? array_intersect($allowedContentTypes, $eligibleContentTypes)
            : $eligibleContentTypes;

        $event->setConfig($config);
    }
}
