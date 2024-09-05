<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Local\Attribute\Assets;

use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetCreateStruct;
use Ibexa\Tests\Integration\Measurement\ProductCatalog\Local\Attribute\AbstractValueMeasurementTest;

final class SingleMeasurementAssetsTest extends AbstractValueMeasurementTest
{
    public function testCreatingAssetsForSingleMeasurement(): void
    {
        $attributeTypeService = self::getServiceByClassName(AttributeTypeServiceInterface::class);
        $assetService = self::getServiceByClassName(LocalAssetServiceInterface::class);

        $attributeType = $attributeTypeService->getAttributeType('measurement_single');

        $attributeGroup = $this->createAttributeGroup('measurement_single_group');
        $attributeDefinition1 = $this->createAttributeDefinition(
            $attributeType,
            $attributeGroup,
            'foo_measurement_1_single_value_with_assets',
            ['sign' => 'none']
        );
        $productType = $this->createProductType(
            [$attributeDefinition1],
            'pt_with_measurement_single'
        );

        $createStruct = $this->productService->newProductCreateStruct($productType, 'eng-GB');
        $createStruct->setCode('product_with_assets');

        $value = $this->measurementService->buildSimpleValue('length', 0.7, 'centimeter');
        $createStruct->setAttribute('foo_measurement_1_single_value_with_assets', $value);
        $createStruct->setField('name', 'foo');

        $product = $this->productService->createProduct($createStruct);

        $assetService->createAsset(
            $product,
            new AssetCreateStruct(
                'ezcontent://2',
                ['foo_measurement_1_single_value_with_assets' => $value]
            )
        );

        $assetService->createAsset(
            $product,
            new AssetCreateStruct(
                'ezcontent://3',
                ['foo_measurement_1_single_value_with_assets' => 'length 3.000000 centimeter']
            )
        );

        $assets = $assetService->findAssets($product)->toArray();
        $asset1 = $assets[0];
        $asset2 = $assets[1];

        self::assertCount(2, $assets);
        self::assertEquals('ezcontent://2', $asset1->getUri());
        self::assertEquals('ezcontent://3', $asset2->getUri());

        self::assertEquals(
            ['foo_measurement_1_single_value_with_assets' => $value],
            $asset1->getTags()
        );

        $simpleValue = $asset2->getTags()['foo_measurement_1_single_value_with_assets'];
        self::assertInstanceOf(SimpleValueInterface::class, $simpleValue);
        self::assertEquals(3.0, $simpleValue->getValue());
    }
}
