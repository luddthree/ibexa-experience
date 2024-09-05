<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\EventSubscriber\Migrations;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class ProductAttributeConversionSubscriber implements EventSubscriberInterface
{
    private MeasurementServiceInterface $measurementService;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        MeasurementServiceInterface $measurementService,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->measurementService = $measurementService;
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertScalarHashIntoObjects',
                -100,
            ],
        ];
    }

    public function convertScalarHashIntoObjects(FieldValueFromHashEvent $event): void
    {
        $hash = $event->getHash();
        $fieldTypeIdentifier = $event->getFieldTypeIdentifier();

        if ($fieldTypeIdentifier !== 'ibexa_product_specification') {
            return;
        }

        if ($hash === null) {
            return;
        }

        Assert::isArray($hash);

        if (!isset($hash['attributes'])) {
            return;
        }

        Assert::isArray($hash['attributes']);

        foreach ($hash['attributes'] as $attributeDefinitionIdentifier => $attributeData) {
            if ($attributeData instanceof ValueInterface) {
                continue;
            }

            if (!is_array($attributeData)) {
                continue;
            }

            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition(
                (string)$attributeDefinitionIdentifier
            );

            $measurementTypeIdentifier = $attributeDefinition->getType()->getIdentifier();

            switch ($measurementTypeIdentifier) {
                case 'measurement_range':

                    Assert::keyExists($attributeData, 'measurementType');
                    $measurementType = $attributeData['measurementType'];

                    Assert::keyExists($attributeData, 'measurementRangeMinimumValue');
                    $minimumValue = $attributeData['measurementRangeMinimumValue'];
                    Assert::numeric($minimumValue);
                    $minimumValue = (float)$minimumValue;

                    Assert::keyExists($attributeData, 'measurementRangeMaximumValue');
                    $maximumValue = $attributeData['measurementRangeMaximumValue'];
                    Assert::numeric($maximumValue);
                    $maximumValue = (float)$maximumValue;

                    Assert::keyExists($attributeData, 'measurementUnit');
                    $measurementUnit = $attributeData['measurementUnit'];

                    $measurement = $this->measurementService->buildRangeValue(
                        $measurementType,
                        $minimumValue,
                        $maximumValue,
                        $measurementUnit,
                    );

                    $hash['attributes'][$attributeDefinitionIdentifier] = $measurement;

                    break;
                case 'measurement_single':

                    Assert::keyExists($attributeData, 'measurementType');
                    $measurementType = $attributeData['measurementType'];

                    Assert::keyExists($attributeData, 'value');
                    $value = $attributeData['value'];
                    Assert::numeric($value);
                    $value = (float)$value;

                    Assert::keyExists($attributeData, 'measurementUnit');
                    $measurementUnit = $attributeData['measurementUnit'];

                    $measurement = $this->measurementService->buildSimpleValue(
                        $measurementType,
                        $value,
                        $measurementUnit,
                    );

                    $hash['attributes'][$attributeDefinitionIdentifier] = $measurement;

                    break;
            }
        }

        $event->setHash($hash);
    }
}
