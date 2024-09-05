<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Exception;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException as APIBadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationFieldType;
use Ibexa\ProductCatalog\Local\Repository\ProductType\ContentTypeFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductTypeList;
use Traversable;

final class ProductTypeService implements LocalProductTypeServiceInterface
{
    private const PRODUCT_SPECIFICATION_IDENTIFIER = 'product_specification';

    private ContentTypeService $contentTypeService;

    private ContentTypeFactoryInterface $contentTypeFactory;

    private PermissionResolverInterface $permissionResolver;

    private DomainMapper $domainMapper;

    private TransactionHandler $transactionHandler;

    private Repository $repository;

    private ConfigProviderInterface $configProvider;

    private LanguageHandler $languageHandler;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentTypeFactoryInterface $contentTypeFactory,
        PermissionResolverInterface $permissionResolver,
        DomainMapper $domainMapper,
        TransactionHandler $transactionHandler,
        LanguageHandler $languageHandler,
        Repository $repository,
        ConfigProviderInterface $configProvider
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeFactory = $contentTypeFactory;
        $this->permissionResolver = $permissionResolver;
        $this->domainMapper = $domainMapper;
        $this->transactionHandler = $transactionHandler;
        $this->languageHandler = $languageHandler;
        $this->repository = $repository;
        $this->configProvider = $configProvider;
    }

    public function createProductType(ProductTypeCreateStruct $createStruct): ProductTypeInterface
    {
        $this->permissionResolver->assertPolicy(new Create($createStruct));

        $contentTypeCreateStruct = $this->contentTypeFactory->createContentTypeCreateStruct(
            $createStruct->getContentTypeCreateStruct()->identifier,
            $createStruct->getMainLanguageCode(),
            $createStruct->getAssignedAttributesDefinitions(),
            $createStruct->isVirtual()
        );

        $contentTypeGroup = $this->getProductContentTypeGroup();

        $this->repository->beginTransaction();
        try {
            $contentTypeDraft = $this->repository->sudo(
                function () use ($contentTypeCreateStruct, $contentTypeGroup): ContentTypeDraft {
                    $contentTypeDraft = $this->contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
                    $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

                    return $contentTypeDraft;
                }
            );

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->domainMapper->createProductType(
            $this->contentTypeService->loadContentType($contentTypeDraft->id),
        );
    }

    public function newProductTypeCreateStruct(string $identifier, string $mainLanguageCode): ProductTypeCreateStruct
    {
        $contentCreateStruct = $this->contentTypeService->newContentTypeCreateStruct($identifier);

        return new ProductTypeCreateStruct($contentCreateStruct, $mainLanguageCode);
    }

    public function newProductTypeUpdateStruct(ProductTypeInterface $productType): ProductTypeUpdateStruct
    {
        $updateStruct = new ProductTypeUpdateStruct(
            $productType,
            $this->contentTypeService->newContentTypeUpdateStruct()
        );

        $attributeDefinitionStructs = [];
        foreach ($productType->getAttributesDefinitions() as $attributesDefinition) {
            $attributeDefinitionStructs[] = new AssignAttributeDefinitionStruct(
                $attributesDefinition->getAttributeDefinition(),
                $attributesDefinition->isRequired(),
                $attributesDefinition->isDiscriminator()
            );
        }

        $updateStruct->setAttributeDefinitionStructs($attributeDefinitionStructs);

        return $updateStruct;
    }

    public function updateProductType(ProductTypeUpdateStruct $updateStruct): ProductTypeInterface
    {
        $this->permissionResolver->assertPolicy(new Edit($updateStruct));

        $productTypeIdentifier = $updateStruct->getProductType()->getIdentifier();
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($productTypeIdentifier);

        try {
            $draft = $this->contentTypeService->loadContentTypeDraft($contentType->id, true);
        } catch (APINotFoundException $e) {
            $draft = $this->contentTypeService->createContentTypeDraft($contentType);
        }

        $productSpecificationFieldDefinition = $draft->getFieldDefinition(self::PRODUCT_SPECIFICATION_IDENTIFIER);

        if ($productSpecificationFieldDefinition !== null) {
            $fieldDefinition = $this->contentTypeService->newFieldDefinitionUpdateStruct();
            $fieldSettings = $this->populateAttributeDefinitionSettings(
                $updateStruct->getAttributeDefinitionStructs()
            );

            $fieldSettings['is_virtual'] = $updateStruct->isVirtual();

            $fieldDefinition->fieldSettings = $fieldSettings;
            $this->contentTypeService->updateFieldDefinition(
                $draft,
                $productSpecificationFieldDefinition,
                $fieldDefinition
            );
        }

        $this->repository->beginTransaction();
        try {
            $this->repository->sudo(
                function () use ($draft, $updateStruct): void {
                    $this->contentTypeService->updateContentTypeDraft($draft, $updateStruct->getContentTypeUpdateStruct());
                    $this->contentTypeService->publishContentTypeDraft($draft);
                }
            );

            $this->repository->commit();
        } catch (\Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->getProductType(
            $updateStruct->getContentTypeUpdateStruct()->identifier ?: $updateStruct->getProductType()->getIdentifier()
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] $assignedAttributesDefinitions
     *
     * @phpstan-return array{
     *     attributes_definitions: array<
     *         string,
     *         non-empty-array<
     *             int,
     *             array{
     *                 attributeDefinition: string,
     *                 discriminator: bool,
     *                 required: bool
     *             }
     *         >
     *     >,
     *     regions: array{},
     * }
     */
    private function populateAttributeDefinitionSettings(iterable $assignedAttributesDefinitions): array
    {
        $fieldSettings = [
            'attributes_definitions' => [],
            'regions' => [],
        ];

        foreach ($assignedAttributesDefinitions as $assignment) {
            $definition = $assignment->getAttributeDefinition();

            $fieldSettings['attributes_definitions'][$definition->getGroup()->getIdentifier()][] = [
                'attributeDefinition' => $definition->getIdentifier(),
                'discriminator' => $assignment->isDiscriminator(),
                'required' => $assignment->isRequired(),
            ];
        }

        return $fieldSettings;
    }

    public function getProductType(
        string $identifier,
        ?LanguageSettings $languageSettings = null
    ): ProductTypeInterface {
        $languageSettings ??= new LanguageSettings();

        $this->permissionResolver->assertPolicy(new View());

        try {
            return $this->domainMapper->createProductType(
                $this->contentTypeService->loadContentTypeByIdentifier(
                    $identifier,
                    $languageSettings->getLanguages()
                ),
                $this->getPersistenceLanguagesFromLanguageSettings($languageSettings)
            );
        } catch (APINotFoundException $e) {
            throw new NotFoundException(ProductType::class, $identifier);
        }
    }

    public function findProductTypes(
        ?ProductTypeQuery $query = null,
        ?LanguageSettings $languageSettings = null
    ): ProductTypeListInterface {
        $languageSettings ??= new LanguageSettings();

        if (!$this->permissionResolver->canUser(new View())) {
            return new ProductTypeList();
        }

        if ($query === null) {
            $query = new ProductTypeQuery();
        }

        $contentTypes = $this->contentTypeService->loadContentTypes(
            $this->getProductContentTypeGroup(),
            $languageSettings->getLanguages()
        );

        if ($contentTypes instanceof Traversable) {
            $contentTypes = iterator_to_array($contentTypes);
        }

        if ($query->getNamePrefix() !== null) {
            $contentTypes = $this->filterContentTypesByNamePrefix($contentTypes, $query->getNamePrefix());
        }

        $productTypes = [];
        foreach ($contentTypes as $contentType) {
            if ($this->isProductType($contentType)) {
                $productTypes[] = $contentType;
            }
        }

        $totalCount = count($productTypes);

        $items = array_slice(
            $productTypes,
            $query->getOffset(),
            $query->getLimit()
        );

        return new ProductTypeList(
            $this->domainMapper->createProductTypeList(
                $items,
                $this->getPersistenceLanguagesFromLanguageSettings($languageSettings)
            ),
            $totalCount
        );
    }

    private function isProductType(ContentType $contentType): bool
    {
        return $contentType->hasFieldDefinitionOfType(ProductSpecificationFieldType::FIELD_TYPE_IDENTIFIER);
    }

    /**
     * @throws \Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException
     */
    private function getProductContentTypeGroup(): ContentTypeGroup
    {
        $identifier = $this->configProvider->getEngineOption('product_type_group_identifier');
        if ($identifier === null) {
            throw new ConfigurationException('Missing product type group configuration');
        }

        try {
            return $this->contentTypeService->loadContentTypeGroupByIdentifier($identifier);
        } catch (APINotFoundException $e) {
            throw new ConfigurationException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[] $contentTypes
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[]
     */
    private function filterContentTypesByNamePrefix(array $contentTypes, string $namePrefix): array
    {
        $namePrefix = mb_strtolower($namePrefix);

        return array_filter($contentTypes, static function (ContentType $contentType) use ($namePrefix): bool {
            $name = $contentType->getName();
            if ($name !== null) {
                return mb_strtolower(mb_substr($name, 0, mb_strlen($namePrefix))) === $namePrefix;
            }

            return false;
        });
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProductType(ProductTypeInterface $productType): void
    {
        $this->permissionResolver->assertPolicy(new Delete());

        $this->transactionHandler->beginTransaction();
        try {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($productType->getIdentifier());
            $this->repository->sudo(
                fn () => $this->contentTypeService->deleteContentType($contentType)
            );

            $this->transactionHandler->commit();
        } catch (APIBadStateException $e) {
            $this->repository->rollback();
            throw new BadStateException(
                '$productTypeId',
                'Product Type with the given ID still has Product items and cannot be deleted'
            );
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }
    }

    public function isProductTypeUsed(ProductTypeInterface $productType): bool
    {
        if (!$productType instanceof ContentTypeAwareProductTypeInterface) {
            throw new InvalidArgumentException('productType', 'must be an instance of ' . ContentTypeAwareProductTypeInterface::class);
        }

        return $this->contentTypeService->isContentTypeUsed($productType->getContentType());
    }

    public function deleteProductTypeTranslation(
        ProductTypeInterface $productType,
        string $languageCode
    ): void {
        if (!$productType instanceof ContentTypeAwareProductTypeInterface) {
            throw new InvalidArgumentException('productType', 'must be an instance of ' . ContentTypeAwareProductTypeInterface::class);
        }

        $this->repository->beginTransaction();
        try {
            $contentTypeDraft = $this->contentTypeService->createContentTypeDraft(
                $productType->getContentType()
            );

            $contentTypeDraft = $this->contentTypeService->removeContentTypeTranslation(
                $contentTypeDraft,
                $languageCode
            );

            $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>|null
     */
    private function getPersistenceLanguagesFromLanguageSettings(LanguageSettings $languageSettings): ?iterable
    {
        if (!empty($languageSettings->getLanguages())) {
            return $this->languageHandler->loadListByLanguageCodes($languageSettings->getLanguages());
        }

        return null;
    }
}
