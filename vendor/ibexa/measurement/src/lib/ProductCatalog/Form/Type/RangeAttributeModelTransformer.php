<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Type;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Contracts\Measurement\Value\RangeValueInterface,
 *     array{
 *         measurementType: string,
 *         measurementUnit: string,
 *         measurementRangeMinimumValue: float|null,
 *         measurementRangeMaximumValue: float|null,
 *     },
 * >
 */
final class RangeAttributeModelTransformer implements DataTransformerInterface
{
    private MeasurementServiceInterface $measurementService;

    public function __construct(MeasurementServiceInterface $measurementService)
    {
        $this->measurementService = $measurementService;
    }

    public function transform($value): ?array
    {
        if ($value === null || is_array($value)) {
            return $value;
        }

        if (!$value instanceof RangeValueInterface) {
            throw new TransformationFailedException(sprintf(
                'Received %s instead of %s',
                get_debug_type($value),
                RangeValueInterface::class,
            ));
        }

        return [
            'measurementType' => $value->getMeasurement()->getName(),
            'measurementUnit' => $value->getUnit()->getIdentifier(),
            'measurementRangeMinimumValue' => $value->getMinValue(),
            'measurementRangeMaximumValue' => $value->getMaxValue(),
        ];
    }

    public function reverseTransform($value): ?RangeValueInterface
    {
        if ($value === null) {
            return null;
        }

        $type = $value['measurementType'];
        $unit = $value['measurementUnit'];
        $minValue = $value['measurementRangeMinimumValue'];
        $maxValue = $value['measurementRangeMaximumValue'];

        if ($minValue === null || $maxValue === null) {
            return null;
        }

        return $this->measurementService->buildRangeValue($type, $minValue, $maxValue, $unit);
    }
}
