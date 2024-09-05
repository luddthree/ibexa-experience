<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product as ProductPolicy;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException as BaseNotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset\HandlerInterface as AssetHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Asset as SPIAsset;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetCreateStruct as SPIAssetCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetUpdateStruct as SPIAssetUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\Asset;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\AssetCollection;

final class AssetService implements LocalAssetServiceInterface
{
    private const URI_REGEXP = '/^ezcontent:\/\/\d+$/';

    private Repository $repository;

    private ProxyDomainMapper $proxyDomainMapper;

    private PermissionResolverInterface $permissionResolver;

    private AssetHandlerInterface $handler;

    private ProductSpecificationLocator $productSpecificationLocator;

    /** @var iterable<\Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface<scalar, scalar>> */
    private iterable $assetTagsStorageConverters;

    /**
     * @param iterable<\Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface<scalar, scalar>> $assetTagsStorageConverters
     */
    public function __construct(
        Repository $repository,
        ProxyDomainMapper $proxyDomainMapper,
        PermissionResolverInterface $permissionResolver,
        AssetHandlerInterface $handler,
        ProductSpecificationLocator $productSpecificationLocator,
        iterable $assetTagsStorageConverters
    ) {
        $this->repository = $repository;
        $this->proxyDomainMapper = $proxyDomainMapper;
        $this->permissionResolver = $permissionResolver;
        $this->handler = $handler;
        $this->productSpecificationLocator = $productSpecificationLocator;
        $this->assetTagsStorageConverters = $assetTagsStorageConverters;
    }

    public function getAsset(ProductInterface $product, string $identifier): AssetInterface
    {
        $this->permissionResolver->assertPolicy(new ProductPolicy\View($product));

        try {
            return $this->createFromPersistence(
                $product,
                $this->handler->load($this->getIdFromIdentifier($identifier))
            );
        } catch (NotFoundException $e) {
            throw new BaseNotFoundException(ProductInterface::class, $identifier);
        }
    }

    /**
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function findAssets(ProductInterface $product): AssetCollectionInterface
    {
        $this->permissionResolver->assertPolicy(new ProductPolicy\View($product));

        if (!$product instanceof ContentAwareProductInterface) {
            return new AssetCollection();
        }

        $id = $this->productSpecificationLocator->findField($product)->id;

        $items = [];
        foreach ($this->handler->findByProduct($id) as $asset) {
            if ($this->isApplicableTo($product, $asset->tags)) {
                $items[] = $this->createFromPersistence($product, $asset);
            }
        }

        return new AssetCollection($items);
    }

    public function newAssetCreateStruct(): AssetCreateStruct
    {
        return new AssetCreateStruct();
    }

    public function newAssetUpdateStruct(): AssetUpdateStruct
    {
        return new AssetUpdateStruct();
    }

    public function createAsset(ProductInterface $product, AssetCreateStruct $createStruct): AssetInterface
    {
        $this->permissionResolver->assertPolicy(new ProductPolicy\PreEdit($product));

        $this->assertValidCreateStruct($createStruct);

        $id = $this->productSpecificationLocator->findField($product)->id;

        $spiCreateStruct = new SPIAssetCreateStruct();
        $spiCreateStruct->uri = $createStruct->getUri();
        $spiCreateStruct->tags = $this->convertTagsToPersistence(
            $product->getProductType(),
            $createStruct->getTags()
        );
        $spiCreateStruct->productSpecificationId = $id;

        $spiAsset = $this->handler->create($spiCreateStruct);

        return $this->createFromPersistence($product, $spiAsset);
    }

    public function updateAsset(
        ProductInterface $product,
        AssetInterface $asset,
        AssetUpdateStruct $updateStruct
    ): AssetInterface {
        $this->permissionResolver->assertPolicy(new ProductPolicy\PreEdit($product));

        $this->assertValidUpdateStruct($updateStruct);

        $spiUpdateStruct = new SPIAssetUpdateStruct();
        $spiUpdateStruct->id = $this->getIdFromAsset($asset);
        $spiUpdateStruct->uri = $updateStruct->getUri() ?? $asset->getUri();
        $spiUpdateStruct->tags = $this->convertTagsToPersistence(
            $product->getProductType(),
            $updateStruct->getTags() ?? $asset->getTags()
        );

        $this->repository->beginTransaction();
        try {
            $this->handler->update($spiUpdateStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->createFromPersistence(
            $product,
            $this->handler->load($this->getIdFromAsset($asset))
        );
    }

    public function deleteAsset(ProductInterface $product, AssetInterface $asset): void
    {
        $this->permissionResolver->assertPolicy(new ProductPolicy\PreEdit($product));

        $this->repository->beginTransaction();
        try {
            $this->handler->delete($this->getIdFromAsset($asset));
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    private function assertValidCreateStruct(AssetCreateStruct $createStruct): void
    {
        if (!$this->isValidURI($createStruct->getUri())) {
            throw new InvalidArgumentException('$createStruct->uri', 'malformed URI');
        }
    }

    private function assertValidUpdateStruct(AssetUpdateStruct $updateStruct): void
    {
        if ($updateStruct->getUri() !== null && !$this->isValidURI($updateStruct->getUri())) {
            throw new InvalidArgumentException('$updateStruct->uri', 'malformed URI');
        }
    }

    private function createFromPersistence(ProductInterface $product, SPIAsset $spiAsset): Asset
    {
        $content = $this->proxyDomainMapper->createContentProxy(
            $this->getContentIdFromUri($spiAsset->uri)
        );

        $tags = [];
        foreach ($spiAsset->tags as $name => $value) {
            if ($value === null) {
                $tags[$name] = null;
                continue;
            }

            $type = null;
            foreach ($product->getProductType()->getAttributesDefinitions() as $assigment) {
                $definition = $assigment->getAttributeDefinition();
                if ($definition->getIdentifier() === $name) {
                    $type = $definition->getType();
                    break;
                }
            }

            if ($type !== null) {
                $converter = $this->getAssetTagsStorageConverter($type->getIdentifier(), $value);
                $tags[$name] = $converter->convertFromStorage($value);
            }
        }

        return new Asset($content, (string)$spiAsset->id, $spiAsset->uri, $tags);
    }

    private function getContentIdFromUri(string $uri): int
    {
        return (int)substr($uri, strlen('ezcontent://'));
    }

    private function getIdFromAsset(AssetInterface $asset): int
    {
        return $this->getIdFromIdentifier($asset->getIdentifier());
    }

    private function getIdFromIdentifier(string $identifier): int
    {
        return (int)$identifier;
    }

    private function isValidURI(?string $uri): bool
    {
        if (is_string($uri) && trim($uri) !== '') {
            return preg_match(self::URI_REGEXP, $uri) !== false;
        }

        return false;
    }

    /**
     * @param array<string,mixed> $tags
     */
    private function isApplicableTo(ProductInterface $product, array $tags): bool
    {
        if (empty($tags) || !$product instanceof ProductVariantInterface) {
            return true;
        }

        foreach ($tags as $name => $value) {
            if ($value === null) {
                continue;
            }

            foreach ($product->getDiscriminatorAttributes() as $attribute) {
                if ($attribute->getIdentifier() === $name && $attribute->getValue() !== $value) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $tags
     *
     * @return array<string, mixed>
     */
    private function convertTagsToPersistence(ProductTypeInterface $productType, array $tags): array
    {
        $result = [];
        foreach ($tags as $name => $value) {
            if ($value === null) {
                $result[$name] = null;
                continue;
            }

            $type = null;
            foreach ($productType->getAttributesDefinitions() as $assigment) {
                $definition = $assigment->getAttributeDefinition();
                if ($definition->getIdentifier() === $name) {
                    $type = $definition->getType();
                    break;
                }
            }

            if ($type !== null) {
                $converter = $this->getAssetTagsStorageConverter($type->getIdentifier(), $value);
                $result[$name] = $converter->convertToStorage($value);
            }
        }

        return $result;
    }

    /**
     * @param scalar $value
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\AssetTags\AssetTagsStorageConverterInterface<scalar, scalar>
     */
    private function getAssetTagsStorageConverter(
        string $attributeTypeIdentifier,
        $value
    ): AssetTagsStorageConverterInterface {
        foreach ($this->assetTagsStorageConverters as $converter) {
            if ($converter->supportsToStorage($attributeTypeIdentifier, $value)) {
                return $converter;
            }
        }

        throw new InvalidArgumentException(
            'assetTagsStorageConverter',
            'Unable to find AssetTagsStorageConverter that can accept attributes of type ' . $attributeTypeIdentifier
        );
    }
}
