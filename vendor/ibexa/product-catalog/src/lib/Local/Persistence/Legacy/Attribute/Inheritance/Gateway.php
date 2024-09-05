<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Inheritance;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageDefinitionInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Integer\StorageSchema;
use LogicException;

/**
 * @template T of array|scalar
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array<mixed>>
 *
 * @implements \Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Inheritance\GatewayInterface<array<mixed>>
 */
final class Gateway extends AbstractDoctrineDatabase implements GatewayInterface
{
    /** @var array<string, \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<array<string, mixed>, mixed>> */
    private array $storageConverters;

    private StorageDefinitionInterface $storageDefinition;

    /** @var string[] */
    private array $discriminators;

    /**
     * @param iterable<string, \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<array<string, mixed>, mixed>> $storageConverters
     * @param string[] $discriminators
     */
    public function __construct(
        Connection $connection,
        DoctrineSchemaMetadataRegistryInterface $registry,
        iterable $storageConverters,
        StorageDefinitionInterface $storageDefinition,
        array $discriminators
    ) {
        parent::__construct($connection, $registry);

        if ($storageConverters instanceof \Traversable) {
            $storageConverters = iterator_to_array($storageConverters);
        }
        $this->storageConverters = $storageConverters;
        $this->storageDefinition = $storageDefinition;
        $this->discriminators = $discriminators;
    }

    protected function getTableName(): string
    {
        return $this->storageDefinition->getTableName();
    }

    public function canHandle(string $discriminator): bool
    {
        return in_array($discriminator, $this->discriminators, true);
    }

    public function create(
        string $discriminator,
        int $id,
        $value
    ): void {
        $subclassMetadata = $this->getMetadata();
        $identifierColumnName = $subclassMetadata->getIdentifierColumn();

        $values = [
            $identifierColumnName => $id,
        ];

        if (!isset($this->storageConverters[$discriminator])) {
            throw new LogicException(sprintf(
                'Unable to persist data. Missing storage converter for type "%s".',
                $discriminator,
            ));
        }

        $storageConverter = $this->storageConverters[$discriminator];

        $values = array_merge(
            $values,
            $storageConverter->toPersistence($value),
        );

        $this->doInsert($values);
    }

    /**
     * @param mixed $value
     */
    public function update(string $discriminator, int $id, $value): void
    {
        if (!isset($this->storageConverters[$discriminator])) {
            throw new LogicException(sprintf(
                'Unable to persist data. Missing storage converter for type "%s".',
                $discriminator,
            ));
        }

        $storageConverter = $this->storageConverters[$discriminator];

        $this->doUpdate(
            [$this->getMetadata()->getIdentifierColumn() => $id],
            $storageConverter->toPersistence($value)
        );
    }

    public function getDiscriminators(): array
    {
        return $this->discriminators;
    }

    protected function buildMetadata(): DoctrineSchemaMetadataInterface
    {
        $identifierColumns = [
            StorageSchema::COLUMN_ID,
        ];
        $columnToTypesMap = array_merge($this->storageDefinition->getColumns(), [
            StorageSchema::COLUMN_ID => Types::INTEGER,
        ]);

        return new DoctrineSchemaMetadata(
            $this->connection,
            null,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns,
        );
    }
}
