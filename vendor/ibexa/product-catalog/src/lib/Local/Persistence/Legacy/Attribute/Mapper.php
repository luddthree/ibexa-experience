<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AbstractDoctrineDatabase;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Gateway\StorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use LogicException;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\GatewayInterface
 */
final class Mapper
{
    private ContainerInterface $converters;

    public function __construct(ContainerInterface $converters)
    {
        $this->converters = $converters;
    }

    /**
     * @phpstan-param array<int, Data> $rows
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed>[]
     */
    public function extractFromRows(array $rows): array
    {
        $values = [];
        foreach ($rows as $row) {
            $values[] = $this->extractFromRow($row);
        }

        return $values;
    }

    /**
     * @phpstan-param Data $row
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed>
     */
    public function extractFromRow(array $row): Attribute
    {
        $type = $row['discriminator'];

        if (!$this->converters->has($type)) {
            throw new LogicException(sprintf(
                'Unable to find "%s" for type "%s". Check that corresponding service is tagged with %s tag',
                StorageConverterInterface::class,
                $type,
                'ibexa.product_catalog.attribute.storage_converter',
            ));
        }

        $converter = $this->converters->get($type);

        if (!$converter instanceof StorageConverterInterface) {
            throw new LogicException(sprintf(
                'Expected storage converter to be an instance of %s, received %s.',
                StorageConverterInterface::class,
                get_class($converter),
            ));
        }

        $subtypeRow = $this->extractSubtypeData($row, $type);

        $value = $converter->fromPersistence($subtypeRow);

        return new Attribute(
            $row[StorageSchema::COLUMN_ID],
            $row[StorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID],
            $row[StorageSchema::COLUMN_DISCRIMINATOR],
            $value
        );
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private function extractSubtypeData(array $row, string $discriminator): array
    {
        $prefix = $discriminator . AbstractDoctrineDatabase::DISCRIMINATOR_SEPARATOR;
        $pos = strlen($prefix);
        $data = [];
        foreach ($row as $key => $value) {
            if (substr($key, 0, $pos) !== $prefix) {
                continue;
            }

            $column = substr($key, $pos);
            $data[$column] = $value;
        }

        return $data;
    }
}
