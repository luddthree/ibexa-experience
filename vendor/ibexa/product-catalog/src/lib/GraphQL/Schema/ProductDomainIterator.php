<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema;

use Generator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\ProductTypeListAdapter;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Iterator;
use Ibexa\GraphQL\Schema\Domain\NameValidator;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;

final class ProductDomainIterator implements Iterator
{
    public const SKIPPED_FIELD_DEFINITIONS = ['ibexa_product_specification'];

    private ConfigProviderInterface $configProvider;

    private ProductTypeServiceInterface $productTypeService;

    private NameValidator $nameValidator;

    public function __construct(
        ConfigProviderInterface $configProvider,
        ProductTypeServiceInterface $productTypeService,
        NameValidator $nameValidator
    ) {
        $this->configProvider = $configProvider;
        $this->productTypeService = $productTypeService;
        $this->nameValidator = $nameValidator;
    }

    public function init(Builder $schema): void
    {
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function iterate(): Generator
    {
        if ($this->configProvider->getEngineAlias() === null) {
            return;
        }

        $productTypes = new BatchIterator(new ProductTypeListAdapter($this->productTypeService));

        foreach ($productTypes as $productType) {
            $productTypeIdentifier = $productType->getIdentifier();

            if (!$this->nameValidator->isValidName($productTypeIdentifier)) {
                $this->nameValidator->generateInvalidNameWarning('Product Type', $productTypeIdentifier);

                continue;
            }

            yield ['ProductType' => $productType];

            foreach ($productType->getAttributesDefinitions() as $attributesDefinition) {
                yield [
                    'ProductType' => $productType,
                    'Attribute' => $attributesDefinition->getAttributeDefinition(),
                ];
            }

            foreach ($productType->getContentType()->getFieldDefinitions() as $fieldDefinition) {
                if (in_array($fieldDefinition->fieldTypeIdentifier, self::SKIPPED_FIELD_DEFINITIONS, true)) {
                    continue;
                }

                yield [
                    'ProductType' => $productType,
                    'FieldDefinition' => $fieldDefinition,
                ];
            }
        }
    }
}
