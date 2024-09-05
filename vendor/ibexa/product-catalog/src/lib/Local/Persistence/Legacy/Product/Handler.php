<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Product;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct;
use Ibexa\ProductCatalog\Local\Persistence\Values\Product;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariant;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantUpdateStruct;

/**
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGateway
 */
final class Handler implements HandlerInterface
{
    /** @phpstan-var \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGatewayInterface<Data> */
    private StorageGatewayInterface $contentProductGateway;

    private GatewayInterface $productGateway;

    private Mapper $mapper;

    /**
     * @phpstan-param \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGatewayInterface<Data> $gateway
     */
    public function __construct(
        StorageGatewayInterface $gateway,
        GatewayInterface $productGateway,
        Mapper $mapper
    ) {
        $this->contentProductGateway = $gateway;
        $this->productGateway = $productGateway;
        $this->mapper = $mapper;
    }

    public function isCodeUnique(string $code): bool
    {
        return $this->contentProductGateway->findByCode($code) === null;
    }

    public function findById(int $id): AbstractProduct
    {
        $data = $this->contentProductGateway->findByFieldId($id);
        if ($data === null) {
            throw new NotFoundException(Product::class, $id);
        }

        return $this->mapper->createFromRow($data);
    }

    public function findByCode(string $code): AbstractProduct
    {
        $data = $this->contentProductGateway->findByCode($code);
        if ($data === null) {
            throw new NotFoundException(Product::class, $code);
        }

        return $this->mapper->createFromRow($data);
    }

    public function countVariants(int $productId): int
    {
        return $this->contentProductGateway->countVariants($productId);
    }

    /**
     * @return iterable<\Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct>
     */
    public function findVariants(int $productId, int $offset, int $limit): iterable
    {
        $variants = [];
        foreach ($this->contentProductGateway->findVariants($productId, $offset, $limit) as $variant) {
            $variants[] = $variant;
        }

        return $this->mapper->createFromRows($variants);
    }

    public function createVariant(ProductVariantCreateStruct $createStruct): ProductVariant
    {
        $baseProduct = $this->productGateway->findOneByCode($createStruct->baseProductCode);
        if ($baseProduct === null) {
            throw new \InvalidArgumentException(sprintf(
                'Base product (code: %s) not found',
                $createStruct->baseProductCode,
            ));
        }

        $productId = $this->productGateway->insert($createStruct->code, $baseProduct['id']);

        $contentProduct = $this->contentProductGateway->findByCode($createStruct->baseProductCode);
        if ($contentProduct === null) {
            throw new \InvalidArgumentException(sprintf(
                'Base product (code: %s) not found',
                $createStruct->baseProductCode,
            ));
        }
        $contentProductId = $contentProduct['id'];

        $id = $this->contentProductGateway->insertVariant(
            $createStruct->code,
            $createStruct->fieldId,
            $productId,
        );

        return $this->mapper->createVariantFromCreateStruct(
            $id,
            $createStruct->code,
            $contentProductId,
        );
    }

    public function deleteVariant(string $code): void
    {
        $this->contentProductGateway->deleteVariant($code);
    }

    /**
     * @return string[]
     */
    public function deleteVariantsByBaseProductId(int $baseProductId): array
    {
        return $this->contentProductGateway->deleteVariantsByBaseProductId($baseProductId);
    }

    public function updateVariant(ProductVariantUpdateStruct $updateStruct): void
    {
        $this->contentProductGateway->updateVariant($updateStruct);
    }
}
