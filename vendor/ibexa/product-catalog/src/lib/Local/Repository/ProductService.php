<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId;
use Ibexa\Contracts\ProductCatalog\Events\ProductCodeChangedEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\IsProductBased;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationFieldType;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value as ProductSpecificationValue;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\HandlerInterface as AttributeHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment\HandlerInterface as AssignmentHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\HandlerInterface as ProductHandler;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeUpdateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariant as SPIProductVariant;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantCreateStruct as SPIProductVariantCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantUpdateStruct as SPIProductVariantUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorDispatcher;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductList;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariantList;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ProductService implements LocalProductServiceInterface
{
    private ContentService $contentService;

    private LocationService $locationService;

    private SearchService $searchService;

    private ProxyDomainMapper $proxyDomainMapper;

    private TransactionHandler $transactionHandler;

    private ProductHandler $productHandler;

    private AttributeDefinitionHandler $attributeDefinitionHandler;

    private AssignmentHandler $attributeDefinitionAssignmentHandler;

    private AttributeHandler $attributeHandler;

    private Repository $repository;

    private PermissionResolverInterface $permissionResolver;

    private PermissionCriterionResolver $permissionCriterionResolver;

    private DomainMapper $domainMapper;

    private ConfigProviderInterface $configProvider;

    private ValueValidatorDispatcher $valueValidatorDispatcher;

    private ProductSpecificationLocator $productSpecificationLocator;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        SearchService $searchService,
        ContentService $contentService,
        LocationService $locationService,
        ProxyDomainMapper $proxyDomainMapper,
        DomainMapper $domainMapper,
        TransactionHandler $transactionHandler,
        ProductHandler $productHandler,
        AttributeDefinitionHandler $attributeDefinitionHandler,
        AssignmentHandler $attributeDefinitionAssignmentHandler,
        AttributeHandler $attributeHandler,
        Repository $repository,
        PermissionResolverInterface $permissionResolver,
        PermissionCriterionResolver $permissionCriterionResolver,
        ConfigProviderInterface $configProvider,
        ValueValidatorDispatcher $valueValidatorDispatcher,
        ProductSpecificationLocator $productSpecificationLocator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->proxyDomainMapper = $proxyDomainMapper;
        $this->domainMapper = $domainMapper;
        $this->transactionHandler = $transactionHandler;
        $this->productHandler = $productHandler;
        $this->attributeDefinitionHandler = $attributeDefinitionHandler;
        $this->attributeDefinitionAssignmentHandler = $attributeDefinitionAssignmentHandler;
        $this->attributeHandler = $attributeHandler;
        $this->repository = $repository;
        $this->permissionResolver = $permissionResolver;
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        $this->configProvider = $configProvider;
        $this->valueValidatorDispatcher = $valueValidatorDispatcher;
        $this->productSpecificationLocator = $productSpecificationLocator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getProduct(string $code, ?LanguageSettings $settings = null): ProductInterface
    {
        $product = $this->internalLoadProduct($code, $settings);

        $this->permissionResolver->assertPolicy(new View($product));

        return $product;
    }

    public function getProductFromContent(Content $content): ProductInterface
    {
        $product = $this->domainMapper->createProduct(
            $this->proxyDomainMapper->createProductTypeProxyFromContent($content),
            $content
        );

        $this->permissionResolver->assertPolicy(new View($product));

        return $product;
    }

    public function isProduct(Content $content): bool
    {
        return $content->getContentType()->hasFieldDefinitionOfType(
            ProductSpecificationFieldType::FIELD_TYPE_IDENTIFIER
        );
    }

    public function findProducts(ProductQuery $query, ?LanguageSettings $languageSettings = null): ProductListInterface
    {
        if (!$this->permissionResolver->canUser(new View())) {
            return new ProductList();
        }

        $contentQuery = new Query();
        $contentQuery->limit = $query->getLimit();
        $contentQuery->offset = $query->getOffset();

        if ($query->hasQuery()) {
            $contentQuery->query = new ProductCriterionAdapter($query->getQuery());
        }

        foreach ($query->getSortClauses() as $sortClause) {
            $contentQuery->sortClauses[] = new ProductSortClauseAdapter($sortClause);
        }

        if (empty($contentQuery->sortClauses)) {
            $contentQuery->sortClauses[] = new ContentId();
        }

        $criteria = [
            new IsProductBased(),
        ];

        $productViewCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('product', 'view');
        if (!is_bool($productViewCriterion)) {
            $criteria[] = $productViewCriterion;
        }

        if ($query->hasFilter()) {
            $criteria[] = new ProductCriterionAdapter($query->getFilter());
        }

        $contentQuery->filter = new LogicalAnd($criteria);

        if ($query->getAggregations()) {
            $contentQuery->aggregations = $query->getAggregations();
        }

        $languageFilter = [];
        if ($languageSettings) {
            $languageFilter['languages'] = $languageSettings->getLanguages();
            $languageFilter['useAlwaysAvailable'] = $languageSettings->getUseAlwaysAvailable();
        }

        $products = [];

        $searchResults = $this->searchService->findContent($contentQuery, $languageFilter, false);
        foreach ($searchResults as $result) {
            $products[] = $this->domainMapper->createProduct(
                $this->proxyDomainMapper->createProductTypeProxyFromContent($result->valueObject),
                $result->valueObject
            );
        }

        return new ProductList($products, $searchResults->totalCount, $searchResults->aggregations);
    }

    public function createProduct(ProductCreateStruct $createStruct): ProductInterface
    {
        $this->permissionResolver->assertPolicy(new Create($createStruct));

        $this->assertProductCodeIsValid('$createStruct', $createStruct->getCode());

        $values = $createStruct->getAttributes();
        foreach ($createStruct->getProductType()->getAttributesDefinitions() as $assignment) {
            $identifier = $assignment->getAttributeDefinition()->getIdentifier();

            if ($assignment->isDiscriminator()) {
                if (array_key_exists($identifier, $values)) {
                    throw new InvalidArgumentException('$createStruct', "Attribute '$identifier' is discriminator");
                }

                continue;
            }

            $this->assertValidAttributeValue($assignment, '$createStruct', $values[$identifier] ?? null);
        }

        // TODO: Check for values for unassigned attributes

        $productSpecificationFieldDefinition = $this->productSpecificationLocator->findFieldDefinition(
            $createStruct->getProductType()
        );

        $specification = new ProductSpecificationValue();
        $specification->setCode($createStruct->getCode());

        $assignments = $this->attributeDefinitionAssignmentHandler->getIdentityMap($productSpecificationFieldDefinition->id);
        foreach ($assignments as $id => $identifier) {
            $specification->setAttribute($id, $values[$identifier] ?? null);
        }

        $contentCreateStruct = $createStruct->getContentCreateStruct();
        $contentCreateStruct->setField($productSpecificationFieldDefinition->identifier, $specification);

        $this->repository->beginTransaction();
        try {
            $content = $this->contentService->createContent(
                $contentCreateStruct,
                $this->createProductLocationCreateStruct()
            );

            $publishedContent = $this->contentService->publishVersion($content->getVersionInfo());

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->domainMapper->createProduct(
            $createStruct->getProductType(),
            $publishedContent,
            $createStruct->getCode()
        );
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> $createStructs
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProductVariants(ProductInterface $product, iterable $createStructs): void
    {
        $productType = $product->getProductType();

        foreach ($createStructs as $createStruct) {
            $this->assertProductCodeIsValid('$createStruct', $createStruct->getCode());

            $attributeDefinitionAssignments = $productType->getAttributesDefinitions();
            if (empty($attributeDefinitionAssignments)) {
                throw new InvalidArgumentException(
                    '$attributeDefinitionAssignments',
                    'Product variant attributes cannot be empty.'
                );
            }

            foreach ($attributeDefinitionAssignments as $assignment) {
                if ($assignment->isDiscriminator()) {
                    $value = $createStruct->getAttribute($assignment->getAttributeDefinition()->getIdentifier());
                    $this->assertValidAttributeValue($assignment, $assignment->getAttributeDefinition()->getIdentifier(), $value);
                }
            }
        }

        $specificationFieldDefinition = $this->productSpecificationLocator->findFieldDefinition($productType);
        $assignments = $this->attributeDefinitionAssignmentHandler->getIdentityMap(
            $specificationFieldDefinition->id,
            true
        );

        $baseProductCode = $product->getCode();
        $fieldId = $this->productSpecificationLocator->findField($product)->id;

        $this->repository->beginTransaction();
        try {
            foreach ($createStructs as $createStruct) {
                $spiCreateStruct = new SPIProductVariantCreateStruct(
                    $baseProductCode,
                    $fieldId,
                    $createStruct->getCode()
                );

                $spiVariant = $this->productHandler->createVariant($spiCreateStruct);

                foreach ($assignments as $attributeDefinitionId => $attributeDefinitionIdentifier) {
                    $attributeCreateStruct = new AttributeCreateStruct(
                        $spiVariant->id,
                        $attributeDefinitionId,
                        $createStruct->getAttribute((string)$attributeDefinitionIdentifier)
                    );

                    $this->attributeHandler->create($attributeCreateStruct);
                }
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getProductVariant(string $code, ?LanguageSettings $settings = null): ProductVariantInterface
    {
        $productVariant = $this->internalLoadProduct($code, $settings);

        if (!$productVariant instanceof ProductVariantInterface) {
            throw new InvalidArgumentException('productVariant', 'must be an instance of ' . ProductVariantInterface::class);
        }

        $this->permissionResolver->assertPolicy(new View($productVariant));

        return $productVariant;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function findProductVariants(
        ProductInterface $product,
        ?ProductVariantQuery $query = null
    ): ProductVariantListInterface {
        $query ??= new ProductVariantQuery();

        $specification = $this->productSpecificationLocator->findField($product);

        $totalCount = $this->productHandler->countVariants($specification->id);
        if ($totalCount === 0) {
            return new ProductVariantList();
        }

        $variants = [];
        if ($query->getLimit() > 0) {
            $variants = $this->productHandler->findVariants(
                $specification->id,
                $query->getOffset(),
                $query->getLimit()
            );

            foreach ($variants as $variant) {
                $variant->attributes = $this->attributeHandler->findByProduct($variant->id);
            }
        }

        return new ProductVariantList(
            $this->domainMapper->createVariantList($product, $variants),
            $totalCount
        );
    }

    public function newProductCreateStruct(
        ProductTypeInterface $productType,
        string $mainLanguageCode
    ): ProductCreateStruct {
        if (!$productType instanceof ProductType) {
            throw new InvalidArgumentException(
                '$productType',
                __METHOD__ . ' supports ' . ProductType::class . ' instances only.'
            );
        }

        $contentCreateStruct = $this->contentService->newContentCreateStruct(
            $productType->getContentType(),
            $mainLanguageCode
        );

        return new ProductCreateStruct($productType, $contentCreateStruct);
    }

    public function newProductUpdateStruct(ProductInterface $product): ProductUpdateStruct
    {
        return new ProductUpdateStruct($product, $this->contentService->newContentUpdateStruct());
    }

    public function updateProduct(ProductUpdateStruct $updateStruct): ProductInterface
    {
        $this->permissionResolver->assertPolicy(new Edit($updateStruct));

        $product = $updateStruct->getProduct();
        if (!$product instanceof ContentAwareProductInterface) {
            throw new InvalidArgumentException('product', 'must be an instance of ' . ContentAwareProductInterface::class);
        }

        $content = $product->getContent();

        $productField = $this->productSpecificationLocator->findField($product);
        /** @var \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value $specification */
        $specification = $productField->value;
        if ($updateStruct->getCode() !== null && $updateStruct->getCode() !== $product->getCode()) {
            $this->assertProductCodeIsValid('$updateStruct', $updateStruct->getCode());

            $specification->setCode($updateStruct->getCode());
        }

        foreach ($updateStruct->getAttributes() as $identifier => $value) {
            $attributeIdentifier = (string)$identifier;
            $attributeDefinitionId = $this->attributeDefinitionHandler->loadByIdentifier($attributeIdentifier)->id;
            $assignment = $this->findAttributeDefinitionInProduct($product, $attributeIdentifier);
            $this->assertValidAttributeValue($assignment, '$updateStruct', $value);
            $specification->setAttribute($attributeDefinitionId, $value);
        }

        $contentUpdateStruct = $updateStruct->getContentUpdateStruct();
        $contentUpdateStruct->setField($productField->fieldDefIdentifier, $specification);

        $this->transactionHandler->beginTransaction();
        try {
            $draft = $this->contentService->createContentDraft(
                $content->getVersionInfo()->getContentInfo(),
                $content->getVersionInfo()
            );

            $versionInfo = $draft->getVersionInfo();

            $this->contentService->updateContent($versionInfo, $contentUpdateStruct);
            $this->contentService->publishVersion($versionInfo);

            if ($this->isProductCodeChanged($updateStruct)) {
                /** @var string $newCode */
                $newCode = $updateStruct->getCode();

                $this->eventDispatcher->dispatch(
                    new ProductCodeChangedEvent(
                        $product,
                        $updateStruct->getProduct()->getCode(),
                        $newCode
                    )
                );
            }

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }

        return $this->getProduct($updateStruct->getCode() ?? $product->getCode());
    }

    public function updateProductVariant(
        ProductVariantInterface $productVariant,
        ProductVariantUpdateStruct $updateStruct
    ): ProductVariantInterface {
        $productType = $productVariant->getProductType();

        $spiProduct = $this->productHandler->findByCode($productVariant->getCode());
        $spiProduct->attributes = $this->attributeHandler->findByProduct($spiProduct->id);

        $spiProductUpdateStruct = null;
        if ($updateStruct->getCode() !== null && $productVariant->getCode() !== $updateStruct->getCode()) {
            $this->assertProductCodeIsValid('$updateStruct', $updateStruct->getCode());

            $spiProductUpdateStruct = new SPIProductVariantUpdateStruct(
                $spiProduct->id,
                $updateStruct->getCode()
            );
        }

        $attributeUpdateStructs = [];
        $attributeCreateStructs = [];
        if ($updateStruct->hasAttributes()) {
            foreach ($productType->getAttributesDefinitions() as $assignment) {
                $identifier = $assignment->getAttributeDefinition()->getIdentifier();

                if ($assignment->isDiscriminator() && $updateStruct->hasAttribute($identifier)) {
                    $this->assertValidAttributeValue(
                        $assignment,
                        $identifier,
                        $updateStruct->getAttribute($identifier)
                    );
                }
            }

            $discriminatorsMap = $this->getDiscriminatorsIdentityMap($productType);
            foreach ($updateStruct->getAttributes() as $identifier => $value) {
                if (!array_key_exists($identifier, $discriminatorsMap)) {
                    throw new InvalidArgumentException(
                        '$updateStruct',
                        sprintf("Attribute '%s' is not a variant discriminator", $identifier)
                    );
                }

                $attributeDefinitionId = $discriminatorsMap[$identifier];

                $updated = false;
                foreach ($spiProduct->attributes as $attribute) {
                    if ($attribute->getAttributeDefinitionId() === $attributeDefinitionId) {
                        $attributeUpdateStructs[] = new AttributeUpdateStruct(
                            $attribute->getId(),
                            $attribute->getDiscriminator(),
                            $value
                        );

                        $updated = true;
                        break;
                    }
                }

                if (!$updated) {
                    $attributeCreateStructs[] = new AttributeCreateStruct(
                        $spiProduct->id,
                        $attributeDefinitionId,
                        $value
                    );
                }
            }
        }

        $this->repository->beginTransaction();
        try {
            if ($spiProductUpdateStruct !== null) {
                $this->productHandler->updateVariant($spiProductUpdateStruct);
            }

            foreach ($attributeUpdateStructs as $updateStruct) {
                $this->attributeHandler->update($updateStruct);
            }

            foreach ($attributeCreateStructs as $createStruct) {
                $this->attributeHandler->create($createStruct);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->domainMapper->createVariant(
            $productVariant->getBaseProduct(),
            new SPIProductVariant([
                'id' => $spiProduct->id,
                'code' => $spiProductUpdateStruct->code ?? $productVariant->getCode(),
            ])
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    private function findAttributeDefinitionInProduct(
        ProductInterface $product,
        string $identifier
    ): AttributeDefinitionAssignmentInterface {
        $productType = $product->getProductType();
        $attributeDefinitions = $productType->getAttributesDefinitions();

        foreach ($attributeDefinitions as $attributeDefinition) {
            if ($attributeDefinition->getAttributeDefinition()->getIdentifier() === $identifier) {
                return $attributeDefinition;
            }
        }

        throw new NotFoundException(AttributeDefinitionAssignmentInterface::class, $identifier);
    }

    public function deleteProduct(ProductInterface $product): void
    {
        $this->permissionResolver->assertPolicy(new Delete($product));

        if ($product instanceof ProductVariantInterface) {
            $this->deleteVariant($product);
        } else {
            $this->deleteBaseProduct($product);
        }
    }

    /**
     * @return string[]
     */
    public function deleteProductVariantsByBaseProduct(ProductInterface $baseProduct): array
    {
        $this->permissionResolver->assertPolicy(new Delete($baseProduct));

        return $this->productHandler->deleteVariantsByBaseProductId(
            $this->productSpecificationLocator->findField($baseProduct)->id
        );
    }

    private function deleteBaseProduct(ProductInterface $product): void
    {
        if (!$product instanceof ContentAwareProductInterface) {
            throw new InvalidArgumentException('product', 'must be an instance of ' . ContentAwareProductInterface::class);
        }

        $this->repository->beginTransaction();
        try {
            $this->deleteProductVariantsByBaseProduct($product);

            $this->repository->sudo(
                fn () => $this->contentService->deleteContent($product->getContent()->contentInfo)
            );

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    private function deleteVariant(ProductVariantInterface $productVariant): void
    {
        $this->repository->beginTransaction();
        try {
            $this->productHandler->deleteVariant($productVariant->getCode());
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    public function deleteProductTranslation(ProductInterface $product, Language $language): void
    {
        if (!($product instanceof ContentAwareProductInterface)) {
            throw new InvalidArgumentException('product', 'must be an instance of ' . ContentAwareProductInterface::class);
        }

        $this->permissionResolver->assertPolicy(new Delete());

        $this->repository->sudo(function () use ($product, $language): void {
            $this->contentService->deleteTranslation(
                $product->getContent()->contentInfo,
                $language->languageCode
            );
        });
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function internalLoadProduct(string $code, ?LanguageSettings $settings = null): ProductInterface
    {
        $settings = $settings ?? new LanguageSettings();

        $spiProduct = $this->productHandler->findByCode($code);

        if ($spiProduct instanceof SPIProductVariant) {
            return $this->internalLoadVariant($spiProduct, $settings);
        }

        return $this->internalLoadBaseProduct($spiProduct, $settings);
    }

    private function internalLoadVariant(SPIProductVariant $spiProduct, LanguageSettings $settings): ProductVariant
    {
        $spiProduct->attributes = $this->attributeHandler->findByProduct($spiProduct->id);

        $baseProduct = $this->internalLoadBaseProduct(
            $this->productHandler->findById($spiProduct->baseProductId),
            $settings
        );

        return $this->domainMapper->createVariant($baseProduct, $spiProduct);
    }

    private function internalLoadBaseProduct(AbstractProduct $abstractProduct, LanguageSettings $settings): Product
    {
        $abstractProduct->attributes = $this->attributeHandler->findByProduct($abstractProduct->id);

        $content = $this->proxyDomainMapper->createContentProxy(
            $abstractProduct->contentId,
            $settings->getLanguages(),
            $settings->getUseAlwaysAvailable(),
        );

        $productType = $this->proxyDomainMapper->createProductTypeProxyFromContent($content);

        return $this->domainMapper->createProduct(
            $productType,
            $content,
            $abstractProduct->code,
            $abstractProduct->attributes
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct[]
     */
    private function createProductLocationCreateStruct(): array
    {
        $rootLocationRemoteId = $this->configProvider->getEngineOption('root_location_remote_id');
        if ($rootLocationRemoteId !== null) {
            $rootLocation = $this->locationService->loadLocationByRemoteId($rootLocationRemoteId);

            return [
                $this->locationService->newLocationCreateStruct($rootLocation->id),
            ];
        }

        return [];
    }

    /**
     * @param mixed $value
     */
    private function assertValidAttributeValue(
        AttributeDefinitionAssignmentInterface $assignment,
        string $argumentName,
        $value
    ): void {
        $definition = $assignment->getAttributeDefinition();

        if ($value === null && $assignment->isRequired()) {
            throw new InvalidArgumentValue(
                $argumentName,
                $value
            );
        }

        $errors = $this->valueValidatorDispatcher->validateValue($definition, $value);
        if (!empty($errors)) {
            throw new InvalidArgumentValue(
                $argumentName,
                $value
            );
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertProductCodeIsValid(string $argumentName, ?string $code): void
    {
        $this->assertProductCodeIsNotEmpty($argumentName, $code);
        $this->assertProductCodeLength($argumentName, $code);
        $this->assertProductCodePattern($argumentName, $code);
        $this->assertProductCodeIsUnique($argumentName, $code);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertProductCodeIsUnique(string $argumentName, string $code): void
    {
        if (!$this->productHandler->isCodeUnique($code)) {
            throw new InvalidArgumentException(
                $argumentName,
                "Product with code '$code' already exists"
            );
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertProductCodeLength(string $argumentName, string $code): void
    {
        if (mb_strlen($code) > ProductSpecificationFieldType::PRODUCT_CODE_MAX_LENGTH) {
            throw new InvalidArgumentException(
                $argumentName,
                sprintf(
                    'Product code is too long. It should have %d character or less',
                    ProductSpecificationFieldType::PRODUCT_CODE_MAX_LENGTH
                )
            );
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertProductCodePattern(string $argumentName, string $code): void
    {
        if (!preg_match(ProductSpecificationFieldType::PRODUCT_CODE_PATTERN, $code)) {
            throw new InvalidArgumentException(
                $argumentName,
                'Product code may only contain letters from "a" to "z", numbers, underscores and dashes',
            );
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertProductCodeIsNotEmpty(string $argumentName, ?string $code): void
    {
        if (empty($code)) {
            throw new InvalidArgumentException($argumentName, 'Product code must be set');
        }
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface $productType
     *
     * @return int[]|string[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getDiscriminatorsIdentityMap(ProductTypeInterface $productType): array
    {
        if (!$productType instanceof ContentTypeAwareProductTypeInterface) {
            throw new InvalidArgumentException(
                'productType',
                'must be an instance of ' . ContentTypeAwareProductTypeInterface::class
            );
        }

        $specificationFieldDefinitionId = $this
            ->productSpecificationLocator
            ->findFieldDefinition($productType)
            ->id;

        $discriminatorsMap = $this->attributeDefinitionAssignmentHandler->getIdentityMap(
            $specificationFieldDefinitionId,
            true
        );

        return array_flip($discriminatorsMap);
    }

    private function isProductCodeChanged(
        ProductUpdateStruct $updateStruct
    ): bool {
        if ($updateStruct->getCode() === null) {
            return false;
        }

        return $updateStruct->getCode() !== $updateStruct->getProduct()->getCode();
    }
}
