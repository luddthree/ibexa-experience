<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Type;

/**
 * @extends \Ibexa\Contracts\Core\FieldType\GatewayBasedStorage<
 *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\StorageGateway
 * >
 */
final class Storage extends GatewayBasedStorage
{
    /** @var \Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\StorageGateway */
    protected $gateway;

    /**
     * @param array<mixed> $context
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
    {
        $contentId = $versionInfo->contentInfo->id;
        $fieldId = $field->id;
        $versionNo = $field->versionNo;
        assert($versionNo !== null);

        $data = $field->value->externalData;
        if (empty($data) || empty($data[Type::FIELD_ID_KEY])) {
            if ($this->gateway->exists($fieldId, $versionNo)) {
                $this->gateway->delete($fieldId, $versionNo);
            }

            return true;
        }

        if ($this->gateway->exists($fieldId, $versionNo)) {
            $this->gateway->update($fieldId, $versionNo, (int) $data[Type::FIELD_ID_KEY]);
        } else {
            $this->gateway->insert($fieldId, $versionNo, $contentId, (int) $data[Type::FIELD_ID_KEY]);
        }

        return true;
    }

    /**
     * @param array<mixed> $context
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        assert($field->versionNo !== null);
        $field->value->externalData = $this->gateway->findByFieldIdAndVersionNo(
            $field->id,
            $field->versionNo,
        );
    }

    /**
     * @param array<string|int> $fieldIds
     * @param array<mixed> $context
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context): ?bool
    {
        foreach ($fieldIds as $fieldId) {
            $this->gateway->delete(
                (int) $fieldId,
                $versionInfo->versionNo
            );
        }

        return true;
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    /**
     * @param array<mixed> $context
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context): array
    {
        return [];
    }
}
