<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber\FieldType\ProductSpecification;

use Ibexa\Contracts\AdminUi\Event\FieldDefinitionMappingEvent;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Region;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DefaultVatRateIdentifiersEventSubscriber implements EventSubscriberInterface
{
    private const PRODUCT_SPECIFICATION_IDENTIFIER = 'product_specification';

    private VatServiceInterface $vatService;

    public function __construct(VatServiceInterface $vatService)
    {
        $this->vatService = $vatService;
    }

    public static function getSubscribedEvents(): array
    {
        return [FieldDefinitionMappingEvent::NAME => ['mapToDefaultConfigIdentifiers', 40]];
    }

    public function mapToDefaultConfigIdentifiers(FieldDefinitionMappingEvent $event): void
    {
        if ($event->getFieldDefinition()->identifier !== self::PRODUCT_SPECIFICATION_IDENTIFIER) {
            return;
        }

        $fieldDefinitionData = $event->getFieldDefinitionData();
        if (!$fieldDefinitionData->contentTypeData->isNew()) {
            return;
        }

        $regions = $fieldDefinitionData->fieldSettings['regions'];

        foreach ($regions as $region) {
            $regionIdentifier = $region['region_identifier'];
            $regionInstance = new Region($regionIdentifier);

            $vatCategories = $this->vatService->getVatCategories($regionInstance)->getVatCategories();
            if (empty($vatCategories)) {
                continue;
            }

            $regions[$regionIdentifier]['vat_category_identifier'] = $vatCategories[0]->getIdentifier();
        }

        $fieldDefinitionData->fieldSettings['regions'] = $regions;
        $event->setFieldDefinitionData($fieldDefinitionData);
    }
}
