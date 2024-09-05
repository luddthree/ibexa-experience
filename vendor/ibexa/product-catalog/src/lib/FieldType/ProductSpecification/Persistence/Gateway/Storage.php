<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\FieldType\StorageGatewayInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\HandlerInterface as AttributeHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;

/**
 * @extends \Ibexa\Contracts\Core\FieldType\GatewayBasedStorage<
 *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGateway
 * >
 */
final class Storage extends GatewayBasedStorage
{
    private AttributeHandlerInterface $attributeHandler;

    private GatewayInterface $productGateway;

    public function __construct(
        StorageGatewayInterface $gateway,
        GatewayInterface $productGateway,
        AttributeHandlerInterface $attributesHandler
    ) {
        parent::__construct($gateway);

        $this->productGateway = $productGateway;
        $this->attributeHandler = $attributesHandler;
    }

    /**
     * @param array<string,mixed> $context
     */
    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context)
    {
        return null;
    }

    /**
     * @param array<mixed> $context
     * @param array<int> $fieldIds
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context): ?bool
    {
        $this->gateway->deleteByFieldIds($fieldIds, $versionInfo->versionNo);

        return true;
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    /**
     * @param array<mixed> $context
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        $data = $this->gateway->findByContentIdAndVersionNo(
            $versionInfo->contentInfo->id,
            $versionInfo->versionNo
        );

        if ($data !== null) {
            $data['attributes'] = [];
            foreach ($this->attributeHandler->findByProduct($data['id']) as $attribute) {
                $attributeDefinitionId = $attribute->getAttributeDefinitionId();
                $data['attributes'][$attributeDefinitionId] = $attribute->getValue();
            }
        }

        $field->value->externalData = $data;
    }

    /**
     * @param array<mixed> $context
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context): array
    {
        return [];
    }

    /**
     * @param array<mixed> $context
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
    {
        $contentId = $versionInfo->contentInfo->id;
        $versionNo = $versionInfo->versionNo;

        $data = $field->value->externalData;
        if (empty($data) || empty($data['code'])) {
            $this->gateway->delete($contentId, $versionNo, $field->id);

            return null;
        }

        if ($this->gateway->exists($contentId, $versionNo)) {
            $this->gateway->update($contentId, $versionNo, $field->id, $data['code']);
            $data = $this->gateway->findByContentIdAndVersionNo($contentId, $versionNo);
            if ($data === null) {
                throw new \LogicException(sprintf(
                    'Product data for content ID %s and version %s not found after executing update operation.',
                    $contentId,
                    $versionNo,
                ));
            }

            $id = $data['id'];
        } else {
            $productId = $this->findOrCreateProduct($data['code']);
            $id = $this->gateway->insert($productId, $contentId, $versionNo, $field->id, $data['code']);
        }

        $this->storeAttributes($id, $data['attributes'] ?? []);

        return null;
    }

    /**
     * @param array<string|int, array<mixed>|scalar|object|null> $attributes
     */
    private function storeAttributes(int $productSpecificationId, array $attributes): void
    {
        foreach ($attributes as $attributeDefinitionId => $value) {
            $spiAttribute = new AttributeCreateStruct($productSpecificationId, $attributeDefinitionId, $value);
            $this->attributeHandler->create($spiAttribute);
        }
    }

    private function findOrCreateProduct(string $code): int
    {
        $product = $this->productGateway->findOneByCode($code);
        if ($product !== null) {
            return $product['id'];
        }

        return $this->productGateway->insert($code);
    }
}
