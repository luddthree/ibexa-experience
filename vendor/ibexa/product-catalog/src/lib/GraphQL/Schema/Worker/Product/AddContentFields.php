<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\Product;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class AddContentFields extends Base implements Worker
{
    private FieldDefinitionMapper $fieldDefinitionMapper;

    public function __construct(FieldDefinitionMapper $fieldDefinitionMapper)
    {
        $this->fieldDefinitionMapper = $fieldDefinitionMapper;
    }

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $typeIdentifier = $this->getTypeContentFields($args);
        $fieldDefinition = $this->getDefinition(
            $args[self::FIELD_DEFINITION_IDENTIFIER]
        );

        $schema->addFieldToType(
            $typeIdentifier,
            new Builder\Input\Field(
                $this->getFieldName($args),
                $fieldDefinition['type'],
                $fieldDefinition
            )
        );

        if ($args[self::FIELD_DEFINITION_IDENTIFIER]->isTranslatable) {
            $schema->addArgToField(
                $typeIdentifier,
                $this->getFieldName($args),
                new Builder\Input\Arg('language', 'RepositoryLanguage')
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
            ) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface &&
            $args[self::FIELD_DEFINITION_IDENTIFIER] instanceof FieldDefinition &&
            $schema->hasType($this->getTypeContentFields($args));
    }

    /**
     * @phpstan-return array{
     *    type: ?string,
     *    resolve: ?string,
     *    argsBuilder?: ?string
     * }
     */
    private function getDefinition(FieldDefinition $fieldDefinition): array
    {
        $resolver = $this->fieldDefinitionMapper->mapToFieldValueResolver($fieldDefinition);

        $definition = [
            'type' => $this->fieldDefinitionMapper->mapToFieldValueType($fieldDefinition),
            'resolve' => $this->mapToProductFieldValueResolver($resolver),
        ];

        if (($argsBuilder = $this->fieldDefinitionMapper->mapToFieldValueArgsBuilder($fieldDefinition)) !== null) {
            $definition['argsBuilder'] = $argsBuilder;
        }

        return $definition;
    }

    private function mapToProductFieldValueResolver(?string $resolver): string
    {
        if (empty($resolver)) {
            return '';
        }

        return str_replace('ItemFieldValue', 'ContentFieldByProduct', $resolver);
    }

    /**
     * @param array<mixed> $args
     */
    private function getTypeContentFields(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductContentFields(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }

    /**
     * @param array<mixed> $args
     */
    protected function getFieldName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getFieldDefinition($args[self::FIELD_DEFINITION_IDENTIFIER]);
    }
}
