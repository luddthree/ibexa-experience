<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber\FieldType\ProductSpecification;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AttributeIdentifierConversionEventSubscriber implements EventSubscriberInterface
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                ['convertAttributeIdentifiersToIds', -100],
            ],
        ];
    }

    public function convertAttributeIdentifiersToIds(FieldValueFromHashEvent $event): void
    {
        if ($event->getFieldTypeIdentifier() !== Type::FIELD_TYPE_IDENTIFIER) {
            return;
        }

        $hash = $event->getHash();
        if (!is_array($hash) || empty($hash['attributes'])) {
            return;
        }

        foreach ($hash['attributes'] as $key => $attribute) {
            if (is_int($key)) {
                continue;
            }

            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition($key);
            if (!$attributeDefinition instanceof AttributeDefinition) {
                continue;
            }

            $id = $attributeDefinition->getId();
            unset($hash['attributes'][$key]);
            $hash['attributes'][$id] = $attribute;
        }

        $event->setHash($hash);
    }
}
