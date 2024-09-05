<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Generator;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value as ProductSpecificationValue;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface as AttributeGroupHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition as SPIAttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup as SPIAttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductAvailabilityDelegate;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductPriceDelegate;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductVariantsDelegate;
use Ibexa\ProductCatalog\Local\Repository\Values\Attribute;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionAssignment;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant;
use Ibexa\ProductCatalog\Local\Repository\Variant\NameGeneratorInterface;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryProviderInterface;

final class DomainMapper
{
    private LanguageHandlerInterface $languageHandler;

    private LanguageResolver $languageResolver;

    private AttributeTypeServiceInterface $attributeTypeRegistry;

    private AttributeGroupHandlerInterface $attributeGroupHandler;

    private AttributeDefinitionHandlerInterface $attributeDefinitionHandler;

    private ProductAvailabilityDelegate $productAvailabilityDelegate;

    private ProductPriceDelegate $productPriceDelegate;

    private VatCategoryProviderInterface $vatCategoryProvider;

    private ProductVariantsDelegate $productVariantsDelegate;

    private NameGeneratorInterface $nameGenerator;

    public function __construct(
        LanguageHandlerInterface $languageHandler,
        LanguageResolver $languageResolver,
        AttributeTypeServiceInterface $attributeTypeRegistry,
        AttributeGroupHandlerInterface $attributeGroupHandler,
        AttributeDefinitionHandlerInterface $attributeDefinitionHandler,
        ProductAvailabilityDelegate $productAvailabilityDelegate,
        ProductPriceDelegate $productPriceDelegate,
        ProductVariantsDelegate $productVariantsDelegate,
        VatCategoryProviderInterface $vatCategoryProvider,
        NameGeneratorInterface $nameGenerator
    ) {
        $this->languageHandler = $languageHandler;
        $this->languageResolver = $languageResolver;
        $this->attributeTypeRegistry = $attributeTypeRegistry;
        $this->attributeGroupHandler = $attributeGroupHandler;
        $this->attributeDefinitionHandler = $attributeDefinitionHandler;
        $this->productAvailabilityDelegate = $productAvailabilityDelegate;
        $this->productPriceDelegate = $productPriceDelegate;
        $this->productVariantsDelegate = $productVariantsDelegate;
        $this->vatCategoryProvider = $vatCategoryProvider;
        $this->nameGenerator = $nameGenerator;
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed>[] $spiAttributes
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\Attribute[]
     */
    private function createAttributeListFromSpiValues(array $spiAttributes): iterable
    {
        if (empty($spiAttributes)) {
            return [];
        }

        $prioritizedLanguages = $this->getPrioritizedLanguages();

        $attributes = [];
        foreach ($spiAttributes as $spiAttribute) {
            $definition = $this->createAttributeDefinition(
                $this->attributeDefinitionHandler->load($spiAttribute->getAttributeDefinitionId()),
                $prioritizedLanguages
            );

            $attributes[] = new Attribute($definition, $spiAttribute->getValue());
        }

        $sorted = $this->sortAttributesList($attributes);

        foreach ($sorted as $attribute) {
            yield $attribute->getIdentifier() => $attribute;
        }
    }

    /**
     * @param array<int,mixed> $values
     *
     * @return \Generator<string, \Ibexa\ProductCatalog\Local\Repository\Values\Attribute>
     */
    private function createAttributeListFromRawValues(array $values): Generator
    {
        $prioritizedLanguages = $this->getPrioritizedLanguages();

        $attributes = [];
        foreach ($values as $attributeDefinitionId => $value) {
            $definition = $this->createAttributeDefinition(
                $this->attributeDefinitionHandler->load($attributeDefinitionId),
                $prioritizedLanguages
            );

            $attributes[] = new Attribute($definition, $value);
        }

        $sorted = $this->sortAttributesList($attributes);

        foreach ($sorted as $attribute) {
            yield $attribute->getIdentifier() => $attribute;
        }
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\Attribute[] $attributes
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\Attribute[]
     */
    private function sortAttributesList(array $attributes): array
    {
        usort(
            $attributes,
            static fn (Attribute $a, Attribute $b): int => $a->getAttributeDefinition()->getPosition() <=> $b->getAttributeDefinition()->getPosition()
        );

        return $attributes;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[]|null $prioritizedLanguages
     */
    public function createAttributeDefinition(
        SPIAttributeDefinition $spiAttributeDefinition,
        ?iterable $prioritizedLanguages = null
    ): AttributeDefinition {
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $name = $description = null;
        $names = $descriptions = [];
        foreach ($prioritizedLanguages as $prioritizedLanguage) {
            $languageId = $prioritizedLanguage->id;

            if (!$spiAttributeDefinition->hasName($languageId)) {
                continue;
            }

            $names[$prioritizedLanguage->languageCode] = $spiAttributeDefinition->getName($languageId);
            if ($spiAttributeDefinition->hasDescription($languageId)) {
                $descriptions[$prioritizedLanguage->languageCode] = $spiAttributeDefinition->getDescription($languageId);
            } else {
                $descriptions[$prioritizedLanguage->languageCode] = null;
            }

            if ($name !== null) {
                continue;
            }

            $name = $spiAttributeDefinition->getName($languageId);
            if ($spiAttributeDefinition->hasDescription($languageId)) {
                $description = $spiAttributeDefinition->getDescription($languageId);
            }
        }

        $languageIds = array_keys($spiAttributeDefinition->getNames());
        $languages = [];
        foreach ($this->languageHandler->loadList($languageIds) as $language) {
            $languages[] = $language->languageCode;
        }

        return new AttributeDefinition(
            $spiAttributeDefinition->id,
            $spiAttributeDefinition->identifier,
            $this->attributeTypeRegistry->getAttributeType($spiAttributeDefinition->type),
            $this->createAttributeGroup(
                $this->attributeGroupHandler->load($spiAttributeDefinition->attributeGroupId),
                $prioritizedLanguages
            ),
            $name ?? '',
            $spiAttributeDefinition->position,
            $languages,
            $description,
            $names,
            $descriptions,
            $spiAttributeDefinition->options
        );
    }

    /**
     * @phpstan-param array{
     *   attributeDefinition: string,
     *   required: boolean,
     *   discriminator: boolean
     * } $assignmentData
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[] $prioritizedLanguages
     */
    public function createAttributeDefinitionAssignment(
        array $assignmentData,
        ?iterable $prioritizedLanguages = null
    ): AttributeDefinitionAssignment {
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        return new AttributeDefinitionAssignment(
            $this->createAttributeDefinition(
                $this->attributeDefinitionHandler->loadByIdentifier($assignmentData['attributeDefinition']),
                $prioritizedLanguages
            ),
            $assignmentData['required'],
            $assignmentData['discriminator']
        );
    }

    /**
     * @phpstan-param iterable<string,array<string,array{
     *   attributeDefinition: string,
     *   required: boolean,
     *   discriminator: boolean
     * }>> $attributesDefinitions
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[] $prioritizedLanguages
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionAssignment[]
     */
    public function createAttributeDefinitionAssignmentList(
        iterable $attributesDefinitions,
        ?iterable $prioritizedLanguages = null
    ): iterable {
        $attributeDefinitionAssignmentList = [];
        foreach ($attributesDefinitions as $assignmentGroup) {
            foreach ($assignmentGroup as $assignmentData) {
                $attributeDefinitionAssignmentList[] = $this->createAttributeDefinitionAssignment(
                    $assignmentData,
                    $prioritizedLanguages
                );
            }
        }

        return $attributeDefinitionAssignmentList;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[]|null $prioritizedLanguages
     */
    public function createAttributeGroup(
        SPIAttributeGroup $spiAttributeGroup,
        ?iterable $prioritizedLanguages = null
    ): AttributeGroup {
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $name = null;
        foreach ($prioritizedLanguages as $prioritizedLanguage) {
            if (isset($spiAttributeGroup->names[$prioritizedLanguage->id])) {
                $name = $spiAttributeGroup->names[$prioritizedLanguage->id];
                break;
            }
        }

        $languageIds = array_keys($spiAttributeGroup->names);
        $languages = [];
        $names = [];
        foreach ($this->languageHandler->loadList($languageIds) as $language) {
            $languages[] = $language->languageCode;
            $names[$language->languageCode] = $spiAttributeGroup->names[$language->id];
        }

        return new AttributeGroup(
            $spiAttributeGroup->id,
            $spiAttributeGroup->identifier,
            $name ?? '',
            $spiAttributeGroup->position,
            $languages,
            $names,
        );
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup[] $spiAttributeGroups
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[]|null $prioritizedLanguages
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup[]
     */
    public function createAttributeGroupList(
        iterable $spiAttributeGroups,
        ?iterable $prioritizedLanguages = null
    ): array {
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $items = [];
        foreach ($spiAttributeGroups as $spiAttributeGroup) {
            $items[] = $this->createAttributeGroup($spiAttributeGroup, $prioritizedLanguages);
        }

        return $items;
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed>[] $attributes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProduct(
        ProductTypeInterface $productType,
        Content $content,
        ?string $code = null,
        ?array $attributes = null
    ): Product {
        $code ??= $this->getProductSpecification($content)->getCode();

        if ($attributes === null) {
            $attributes = $this->createAttributeListFromRawValues(
                $this->getProductSpecification($content)->getAttributes()
            );
        } else {
            $attributes = $this->createAttributeListFromSpiValues($attributes);
        }

        $product = new Product($productType, $content, $code, $attributes);
        $product->setProductAvailabilityDelegate($this->productAvailabilityDelegate);
        $product->setProductPriceDelegate($this->productPriceDelegate);
        $product->setProductVariantsDelegate($this->productVariantsDelegate);

        return $product;
    }

    public function createVariant(ProductInterface $product, AbstractProduct $variant): ProductVariant
    {
        $attributes = $this->createAttributeListFromSpiValues($variant->attributes);

        $variant = new ProductVariant($product, $variant->code, $attributes);
        $variant->setProductAvailabilityDelegate($this->productAvailabilityDelegate);
        $variant->setProductPriceDelegate($this->productPriceDelegate);
        $variant->setNameGenerator($this->nameGenerator);

        return $variant;
    }

    /**
     * @param iterable<\Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct> $variants
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant[]
     */
    public function createVariantList(ProductInterface $product, iterable $variants): array
    {
        $items = [];
        foreach ($variants as $variant) {
            $items[] = $this->createVariant($product, $variant);
        }

        return $items;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>|null $prioritizedLanguages
     */
    public function createProductType(
        ContentType $contentType,
        ?iterable $prioritizedLanguages = null
    ): ProductType {
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $specificationFieldDefinition = $contentType->getFieldDefinitionsOfType(Type::FIELD_TYPE_IDENTIFIER)->first();

        return new ProductType(
            $contentType,
            $this->createAttributeDefinitionAssignmentList(
                $specificationFieldDefinition->fieldSettings['attributes_definitions'] ?? [],
                $prioritizedLanguages
            ),
            $this->createVatCategoriesMap(
                $specificationFieldDefinition->fieldSettings['regions'] ?? []
            ),
            $specificationFieldDefinition->fieldSettings['is_virtual'] ?? false,
        );
    }

    /**
     * @phpstan-param iterable<string,array{region_identifier: string, vat_category_identifier: string}> $regions
     *
     * @return iterable<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>
     */
    public function createVatCategoriesMap(iterable $regions): iterable
    {
        $map = [];
        foreach ($regions as $config) {
            if (!empty($config['vat_category_identifier'])) {
                $vatCategory = $this->vatCategoryProvider->getVatCategory(
                    $config['region_identifier'],
                    $config['vat_category_identifier']
                );

                $map[$vatCategory->getRegion()] = $vatCategory;
            }
        }

        return $map;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[] $contentTypes
     * @param iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>|null $prioritizedLanguages
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\ProductType[]
     */
    public function createProductTypeList(array $contentTypes, ?iterable $prioritizedLanguages = null): array
    {
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $items = [];
        foreach ($contentTypes as $contentType) {
            $items[] = $this->createProductType($contentType, $prioritizedLanguages);
        }

        return $items;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getProductSpecification(Content $content): ProductSpecificationValue
    {
        foreach ($content->getFields() as $field) {
            if ($field->value instanceof ProductSpecificationValue) {
                return $field->value;
            }
        }

        throw new InvalidArgumentException('$content', 'Missing product specification');
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]|iterable
     */
    private function getPrioritizedLanguages(): iterable
    {
        return $this->languageHandler->loadListByLanguageCodes(
            $this->languageResolver->getPrioritizedLanguages()
        );
    }
}
