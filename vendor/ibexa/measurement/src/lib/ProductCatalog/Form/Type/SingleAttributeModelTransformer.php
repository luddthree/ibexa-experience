<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Type;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface,
 *     array{
 *         measurementType: string,
 *         measurementUnit: string,
 *         value: float|null,
 *     },
 * >
 */
final class SingleAttributeModelTransformer implements DataTransformerInterface
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

        if (!$value instanceof SimpleValueInterface) {
            throw new TransformationFailedException(sprintf(
                'Received %s instead of %s',
                get_debug_type($value),
                SimpleValueInterface::class,
            ));
        }

        return [
            'measurementType' => $value->getMeasurement()->getName(),
            'measurementUnit' => $value->getUnit()->getIdentifier(),
            'value' => $value->getValue(),
        ];
    }

    public function reverseTransform($value): ?SimpleValueInterface
    {
        if ($value === null) {
            return null;
        }

        $type = $value['measurementType'];
        $unit = $value['measurementUnit'];
        $measurementValue = $value['value'];

        if ($measurementValue === null) {
            return null;
        }

        return $this->measurementService->buildSimpleValue($type, $measurementValue, $unit);
    }
}
