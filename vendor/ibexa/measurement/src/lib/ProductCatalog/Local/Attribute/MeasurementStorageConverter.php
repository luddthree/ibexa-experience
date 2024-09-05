<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\SimpleCustom\StorageConverter;
use LogicException;
use Webmozart\Assert\Assert;

/**
 * @implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<
 *     array{'value': mixed|null},
 *     \Ibexa\Contracts\Measurement\Value\ValueInterface,
 * >
 */
final class MeasurementStorageConverter implements StorageConverterInterface
{
    private StorageConverter $storageConverter;

    private MeasurementServiceInterface $measurementService;

    public function __construct(
        StorageConverter $storageConverter,
        MeasurementServiceInterface $measurementService
    ) {
        $this->storageConverter = $storageConverter;
        $this->measurementService = $measurementService;
    }

    public function fromPersistence(array $data)
    {
        $value = $this->storageConverter->fromPersistence($data);

        if (!isset($value['type'], $value['unit'])) {
            return null;
        }

        Assert::string($value['type']);
        Assert::string($value['unit']);

        if (isset($value['value'])) {
            Assert::numeric($value['value']);

            return $this->measurementService->buildSimpleValue(
                $value['type'],
                (float)$value['value'],
                $value['unit'],
            );
        } elseif (isset($value['min_value'], $value['max_value'])) {
            Assert::numeric($value['min_value']);
            Assert::numeric($value['max_value']);

            return $this->measurementService->buildRangeValue(
                $value['type'],
                (float)$value['min_value'],
                (float)$value['max_value'],
                $value['unit'],
            );
        }

        return null;
    }

    public function toPersistence($value): array
    {
        Assert::nullOrIsInstanceOf($value, ValueInterface::class);

        if ($value === null) {
            return $this->storageConverter->toPersistence($value);
        }

        if ($value instanceof RangeValueInterface) {
            $value = [
                'min_value' => $value->getMinValue(),
                'max_value' => $value->getMaxValue(),
                'type' => $value->getMeasurement()->getName(),
                'unit' => $value->getUnit()->getIdentifier(),
            ];
        } elseif ($value instanceof SimpleValueInterface) {
            $value = [
                'min_value' => $value->getValue(),
                'type' => $value->getMeasurement()->getName(),
                'unit' => $value->getUnit()->getIdentifier(),
            ];
        } else {
            throw new LogicException(sprintf(
                'Unable to handle object of class "%s". Expected one of: "%s"',
                get_class($value),
                implode('", "', [
                    RangeValueInterface::class,
                    SimpleValueInterface::class,
                ]),
            ));
        }

        return $this->storageConverter->toPersistence($value);
    }
}
