<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\MatrixFieldType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

abstract class AbstractMatrixFieldDefinitionSchemaWorker extends Base implements Worker
{
    private string $matrixFieldTypeIdentifier;

    public function __construct(string $matrixFieldTypeIdentifier)
    {
        $this->matrixFieldTypeIdentifier = $matrixFieldTypeIdentifier;
    }

    abstract public function getInputTypeName(): string;

    /**
     * @return-phpstan callable(
     *     \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType,
     *     string $fieldDefinitionIdentifier
     * ): string;
     */
    abstract protected function getTypeNameClosure(): callable;

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $typeName = $this->getTypeName($args);
        $schema->addType(
            new Builder\Input\Type($typeName, $this->getInputTypeName())
        );

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        $fieldDefinition = $args[self::FIELD_DEFINITION_IDENTIFIER];
        foreach ($fieldDefinition->getFieldSettings()['columns'] as $column) {
            $schema->addFieldToType(
                $typeName,
                new Builder\Input\Field(
                    $column['identifier'],
                    'String',
                    ['description' => $column['name']]
                )
            );
        }
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER],
                $args[self::FIELD_DEFINITION_IDENTIFIER]
            )
            && $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface
            && $args[self::FIELD_DEFINITION_IDENTIFIER] instanceof FieldDefinition
            && $args[self::FIELD_DEFINITION_IDENTIFIER]->fieldTypeIdentifier === $this->matrixFieldTypeIdentifier
            && !$schema->hasType($this->getTypeName($args));
    }

    /**
     * @param array<mixed> $args
     */
    private function getTypeName(array $args): string
    {
        return $this->getTypeNameClosure()(
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]->getContentType(),
            $args[self::FIELD_DEFINITION_IDENTIFIER]
        );
    }
}
