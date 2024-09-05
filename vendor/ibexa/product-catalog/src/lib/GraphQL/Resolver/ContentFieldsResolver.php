<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\GraphQL\Value\Field as GraphQLField;
use Ibexa\ProductCatalog\GraphQL\Schema\NameHelper;
use Ibexa\ProductCatalog\GraphQL\Schema\ProductDomainIterator;
use Overblog\GraphQLBundle\Definition\Argument;

/**
 * @internal
 */
final class ContentFieldsResolver
{
    private NameHelper $nameHelper;

    public function __construct(NameHelper $nameHelper)
    {
        $this->nameHelper = $nameHelper;
    }

    public function resolveContentFieldsType(ProductInterface $product): string
    {
        return $this->nameHelper->getProductContentFields(
            $product->getProductType()
        );
    }

    public function resolveContentFieldByProduct(
        ContentAwareProductInterface $product,
        string $fieldDefinitionIdentifier,
        Argument $args = null
    ): ?Field {
        $field = $product
            ->getContent()
            ->getField($fieldDefinitionIdentifier, $args['language'] ?? null);

        return GraphQLField::fromField($field);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field[]
     */
    public function resolveContentFieldsByProduct(ContentAwareProductInterface $product): iterable
    {
        $fields = (array)$product->getContent()->getFields();

        return array_filter(
            $fields,
            static function (Field $field): bool {
                return !in_array(
                    $field->fieldTypeIdentifier,
                    ProductDomainIterator::SKIPPED_FIELD_DEFINITIONS,
                    true
                );
            }
        );
    }
}
