<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\AdminUi\Form\Type\Event\ContentCreateContentTypeChoiceLoaderEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentCreateContentTypeChoiceLoaderSubscriber implements EventSubscriberInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(TaxonomyConfiguration $taxonomyConfiguration)
    {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentCreateContentTypeChoiceLoaderEvent::RESOLVE_CONTENT_TYPES => 'removeTaxonomyContentTypes',
        ];
    }

    public function removeTaxonomyContentTypes(ContentCreateContentTypeChoiceLoaderEvent $event): void
    {
        $contentTypeGroups = $event->getContentTypeGroups();

        foreach ($contentTypeGroups as $groupIdentifier => $contentTypes) {
            $contentTypes = array_filter(
                $contentTypes,
                fn (ContentType $contentType): bool => !$this
                    ->taxonomyConfiguration
                    ->isContentTypeAssociatedWithTaxonomy($contentType)
            );

            $event->addContentTypeGroup($groupIdentifier, $contentTypes);
        }
    }
}
