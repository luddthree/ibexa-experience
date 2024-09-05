<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\ContentType\BeforeRemoveFieldDefinitionEvent;
use Ibexa\Contracts\Core\Repository\Events\ContentType\BeforeUpdateFieldDefinitionEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Throws validation errors when Taxonomy fieldtypes are removed from content type.
 *
 * This prevents system from becoming corrupted due to broken Taxonomy content type.
 *
 * @internal
 */
final class ContentTypeEventsSubscriber implements EventSubscriberInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeRemoveFieldDefinitionEvent::class => 'onRemoveFieldDefinition',
            BeforeUpdateFieldDefinitionEvent::class => 'onUpdateFieldDefinition',
        ];
    }

    public function onRemoveFieldDefinition(BeforeRemoveFieldDefinitionEvent $event): void
    {
        $taxonomy = $this->getTaxonomy($event->getContentTypeDraft());
        if (null === $taxonomy) {
            return;
        }

        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);
        $fieldDefinition = $event->getFieldDefinition();

        if (!in_array($fieldDefinition->identifier, $fieldMappings, true)) {
            return;
        }

        throw new InvalidArgumentException(
            '$fieldDefinition',
            'Cannot remove field definitions used by Taxonomy content type',
        );
    }

    public function onUpdateFieldDefinition(BeforeUpdateFieldDefinitionEvent $event): void
    {
        $taxonomy = $this->getTaxonomy($event->getContentTypeDraft());
        if (null === $taxonomy) {
            return;
        }

        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);
        $fieldDefinition = $event->getFieldDefinition();

        if (!in_array($fieldDefinition->identifier, $fieldMappings, true)) {
            return;
        }

        $fieldDefinitionUpdateStruct = $event->getFieldDefinitionUpdateStruct();

        if (
            null !== $fieldDefinitionUpdateStruct->identifier
            && $fieldDefinitionUpdateStruct->identifier !== $fieldDefinition->identifier
        ) {
            throw new InvalidArgumentException(
                '$fieldDefinition',
                'Cannot change field definition identifier used by Taxonomy content type',
            );
        }

        // additional validation for identifier and parent
        if (
            $fieldDefinition->identifier === $fieldMappings['identifier']
            || $fieldDefinition->identifier === $fieldMappings['parent']
        ) {
            if (
                null !== $fieldDefinitionUpdateStruct->isRequired
                && $fieldDefinition->isRequired !== $fieldDefinitionUpdateStruct->isRequired
            ) {
                throw new InvalidArgumentException(
                    '$fieldDefinition',
                    'Cannot change isRequired flag on field definition used by Taxonomy content type',
                );
            }

            if (
                null !== $fieldDefinitionUpdateStruct->isTranslatable
                && $fieldDefinition->isTranslatable !== $fieldDefinitionUpdateStruct->isTranslatable
            ) {
                throw new InvalidArgumentException(
                    '$fieldDefinition',
                    'Cannot change isTranslatable flag on field definition used by Taxonomy content type',
                );
            }
        }
    }

    private function getTaxonomy(ContentTypeDraft $contentTypeDraft): ?string
    {
        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentTypeDraft)) {
            return null;
        }

        return $this->taxonomyConfiguration->getTaxonomyForContentType($contentTypeDraft);
    }
}
