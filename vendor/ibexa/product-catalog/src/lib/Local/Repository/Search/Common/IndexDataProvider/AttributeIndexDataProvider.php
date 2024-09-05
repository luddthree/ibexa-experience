<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\HandlerInterface as AttributeHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\HandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @internal
 */
final class AttributeIndexDataProvider extends AbstractFieldTypeIndexDataProvider implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private HandlerInterface $productHandler;

    private AttributeHandlerInterface $attributeHandler;

    private AttributeDefinitionHandlerInterface $attributeDefinitionHandler;

    /**
     * @phpstan-var array<
     *     string,
     *     \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface<mixed>,
     * >
     *
     * @var \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface[]
     */
    private array $indexDataProviders = [];

    /**
     * @phpstan-param iterable<
     *     string,
     *     \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface<mixed>,
     * > $indexDataProviders
     */
    public function __construct(
        HandlerInterface $productHandler,
        AttributeHandlerInterface $attributeHandler,
        AttributeDefinitionHandlerInterface $attributeDefinitionHandler,
        iterable $indexDataProviders,
        LoggerInterface $logger = null
    ) {
        $this->productHandler = $productHandler;
        $this->attributeHandler = $attributeHandler;
        $this->attributeDefinitionHandler = $attributeDefinitionHandler;
        foreach ($indexDataProviders as $type => $provider) {
            $this->addIndexDataProvider($type, $provider);
        }
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface<mixed> $attributeIndexDataProvider
     */
    public function addIndexDataProvider(
        string $type,
        IndexDataProviderInterface $attributeIndexDataProvider
    ): void {
        $this->indexDataProviders[$type] = $attributeIndexDataProvider;
    }

    protected function getFieldTypeIdentifier(): string
    {
        return Type::FIELD_TYPE_IDENTIFIER;
    }

    protected function doGetSearchData(SPIContent $content, Field $field): array
    {
        $fields = [];
        $productCode = $field->value->externalData['code'];
        $product = $this->productHandler->findByCode($productCode);
        $attributes = $this->attributeHandler->findByProduct($product->id);

        foreach ($attributes as $attribute) {
            $definition = $this->attributeDefinitionHandler->load($attribute->getAttributeDefinitionId());
            $attributeType = $definition->type;
            if (!isset($this->indexDataProviders[$attributeType])) {
                $this->logger->info(sprintf(
                    'Unable to find index data provider for attribute "%s" of type "%s". Ensure that service tagged with %s exists for it.',
                    $definition->identifier,
                    $attributeType,
                    'ibexa.product_catalog.attribute.index_data_provider',
                ));

                continue;
            }

            $indexDataProvider = $this->indexDataProviders[$attributeType];
            $searchFields = $indexDataProvider->getFieldsForAttribute($definition, $attribute);
            foreach ($searchFields as $searchField) {
                $fields[] = $searchField;
            }
        }

        return $fields;
    }
}
