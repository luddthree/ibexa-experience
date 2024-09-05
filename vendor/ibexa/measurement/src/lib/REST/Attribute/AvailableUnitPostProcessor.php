<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\REST\Attribute;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute;
use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;
use Ibexa\Contracts\ProductCatalog\REST\Output\AttributePostProcessorInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class AvailableUnitPostProcessor implements AttributePostProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MeasurementTypesInterface $measurementTypes;

    public function __construct(
        MeasurementTypesInterface $measurementTypes
    ) {
        $this->measurementTypes = $measurementTypes;
    }

    public function supports(Attribute $attribute): bool
    {
        return in_array(
            $attribute->attributeDefinition->getType()->getIdentifier(),
            [
                'measurement_range',
                'measurement_single',
                'measurement',
            ],
            true,
        );
    }

    public function process(Attribute $attribute): array
    {
        $options = $attribute->attributeDefinition->getOptions();
        if (!$options->has('type')) {
            $this->logger->error(
                sprintf(
                    'Missing "type" option in Attribute definition for Measurement type %s.',
                    $attribute->attributeDefinition->getIdentifier(),
                ),
            );

            return [];
        }

        $type = $options->get('type');
        if (!is_string($type)) {
            $this->logger->error(
                sprintf(
                    'Invalid "type" option in Attribute definition for Measurement type %s.'
                        . 'Expected option value to be string, found %s',
                    $attribute->attributeDefinition->getIdentifier(),
                    get_debug_type($type),
                ),
            );

            return [];
        }

        $availableUnits = $this->measurementTypes->getUnitsByType($type);
        $extraOptions = [
            'available_units' => [],
        ];
        foreach ($availableUnits as $label => $value) {
            $extraOptions['available_units'][] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $extraOptions;
    }
}
