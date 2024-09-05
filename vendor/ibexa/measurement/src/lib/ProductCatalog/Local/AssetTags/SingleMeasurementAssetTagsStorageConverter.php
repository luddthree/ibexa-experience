<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Local\AssetTags;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface;

/**
 * @internal
 *
 * @implements \Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface<
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface|null,
 *     string,
 * >
 *
 * @phpstan-type TStorageConverter \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<
 *     array{
 *         unit_id: int|null,
 *         value: float|null,
 *         base_unit_id: int|null,
 *         base_value: float|null,
 *     },
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface,
 * >
 */
final class SingleMeasurementAssetTagsStorageConverter implements AssetTagsStorageConverterInterface
{
    /** @var TStorageConverter */
    private StorageConverterInterface $converter;

    private MeasurementServiceInterface $measurementService;

    /**
     * @param TStorageConverter $converter
     */
    public function __construct(
        StorageConverterInterface $converter,
        MeasurementServiceInterface $measurementService
    ) {
        $this->converter = $converter;
        $this->measurementService = $measurementService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function convertToStorage($tag)
    {
        if ($tag === null) {
            return '';
        }

        // this corresponds to the string tag format coming e.g. from UDW via Ibexa\Measurement\Value\SimpleValue::__toString()
        if (is_string($tag)) {
            [$typeName, $value, $unitName] = explode(' ', $tag);

            $tag = $this->measurementService->buildSimpleValue(
                $typeName,
                (float)$value,
                $unitName
            );
        }

        return json_encode($this->converter->toPersistence($tag)) ?: '';
    }

    public function convertFromStorage($tag)
    {
        /** @var array{
         *    unit_id: int|null,
         *    value: float|null,
         *    base_unit_id: int|null,
         *    base_value: float|null,
         * } $decoded */
        $decoded = json_decode($tag, true);

        return $this->converter->fromPersistence($decoded ?? []);
    }

    /**
     * @param string|\Ibexa\Contracts\Measurement\Value\ValueInterface $value
     */
    public function supportsToStorage(string $attributeTypeIdentifier, $value): bool
    {
        return $attributeTypeIdentifier === 'measurement_single';
    }

    /**
     * @param string $value
     */
    public function supportsFromStorage(string $attributeTypeIdentifier, $value): bool
    {
        return $attributeTypeIdentifier === 'measurement_single';
    }
}
