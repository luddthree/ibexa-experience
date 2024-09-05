<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Values\Asset;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetUpdateStruct;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function load(int $id): Asset
    {
        $row = $this->gateway->findById($id);
        if ($row === null) {
            throw new NotFoundException(Asset::class, $id);
        }

        return $this->mapper->extractFromRow($row);
    }

    public function findByProduct(int $productSpecificationId): array
    {
        return $this->mapper->extractFromRows(
            $this->gateway->findByProduct($productSpecificationId)
        );
    }

    public function create(AssetCreateStruct $createStruct): Asset
    {
        $id = $this->gateway->create($createStruct);

        $asset = new Asset();
        $asset->id = $id;
        $asset->uri = $createStruct->uri;
        $asset->tags = $createStruct->tags;

        return $asset;
    }

    public function update(AssetUpdateStruct $updateStruct): void
    {
        $this->gateway->update($updateStruct);
    }

    public function delete(int $id): void
    {
        $this->gateway->delete($id);
    }
}
