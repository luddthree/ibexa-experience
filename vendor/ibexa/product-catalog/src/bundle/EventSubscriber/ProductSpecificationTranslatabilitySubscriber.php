<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\ContentType\AddFieldDefinitionEvent;
use Ibexa\Contracts\Core\Repository\Events\ContentType\BeforeCreateContentTypeEvent;
use Ibexa\Contracts\Core\Repository\Events\ContentType\BeforeUpdateContentTypeDraftEvent;
use Ibexa\ProductCatalog\Exception\BadStateException;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductSpecificationTranslatabilitySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCreateContentTypeEvent::class => 'onBeforeCreateContentType',
            BeforeUpdateContentTypeDraftEvent::class => 'onBeforeUpdateContentTypeDraft',
            AddFieldDefinitionEvent::class => 'onAddProductSpecificationFieldDefinition',
        ];
    }

    /**
     * @throws \Ibexa\ProductCatalog\Exception\BadStateException
     */
    public function onBeforeCreateContentType(BeforeCreateContentTypeEvent $event): void
    {
        foreach ($event->getContentTypeCreateStruct()->fieldDefinitions as $fieldDefinition) {
            if (
                $fieldDefinition->fieldTypeIdentifier === Type::FIELD_TYPE_IDENTIFIER
                && $fieldDefinition->isTranslatable === true
            ) {
                throw $this->getBadStateException();
            }
        }
    }

    /**
     * @throws \Ibexa\ProductCatalog\Exception\BadStateException
     */
    public function onBeforeUpdateContentTypeDraft(BeforeUpdateContentTypeDraftEvent $event): void
    {
        foreach ($event->getContentTypeDraft()->fieldDefinitions as $fieldDefinition) {
            if (
                $fieldDefinition->fieldTypeIdentifier === Type::FIELD_TYPE_IDENTIFIER
                && $fieldDefinition->isTranslatable === true
            ) {
                throw $this->getBadStateException();
            }
        }
    }

    public function onAddProductSpecificationFieldDefinition(AddFieldDefinitionEvent $event): void
    {
        $fieldDefinition = $event->getFieldDefinitionCreateStruct();
        if ($fieldDefinition->fieldTypeIdentifier !== Type::FIELD_TYPE_IDENTIFIER) {
            return;
        }

        if ($fieldDefinition->isTranslatable === true) {
            throw $this->getBadStateException();
        }
    }

    private function getBadStateException(): BadStateException
    {
        return new BadStateException(
            '$fieldDefinition',
            'Product Specification FieldType cannot be translatable'
        );
    }
}
