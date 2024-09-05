<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Exception;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Symfony\Component\HttpFoundation\Request;

final class PriceController extends RestController
{
    private ProductServiceInterface $productService;

    private ProductPriceServiceInterface $priceService;

    private CurrencyServiceInterface $currencyService;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        ProductServiceInterface $productService,
        ProductPriceServiceInterface $priceService,
        CurrencyServiceInterface $currencyService,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->productService = $productService;
        $this->priceService = $priceService;
        $this->currencyService = $currencyService;
        $this->customerGroupService = $customerGroupService;
    }

    public function getPrice(
        string $productCode,
        string $currencyCode,
        ?string $customerGroupIdentifier = null
    ): PriceInterface {
        $price = $this->priceService->getPriceByProductAndCurrency(
            $this->productService->getProduct($productCode),
            $this->currencyService->getCurrencyByCode($currencyCode)
        );

        if ($customerGroupIdentifier !== null) {
            $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier(
                $customerGroupIdentifier
            );

            if ($customerGroup === null) {
                throw new RestNotFoundException();
            }

            $price = $this->priceService->findOneForCustomerGroup($price, $customerGroup);
            if ($price === null) {
                throw new RestNotFoundException();
            }
        }

        return $price;
    }

    public function getPrices(string $productCode): PriceListInterface
    {
        return $this->priceService->findPricesByProductCode($productCode);
    }

    public function createPrice(Request $request, string $productCode): PriceInterface
    {
        $product = $this->productService->getProduct($productCode);

        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AbstractPriceCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();
        try {
            $price = $this->priceService->createProductPrice($input->toCreateStruct($product));
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $price;
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     */
    public function updatePrice(Request $request, string $productCode, int $id): PriceInterface
    {
        $price = $this->priceService->getPriceById($id);
        if ($price->getProduct()->getCode() !== $productCode) {
            throw new RestNotFoundException();
        }

        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\AbstractPriceUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();
        try {
            $price = $this->priceService->updateProductPrice($input->toUpdateStruct($price));
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $price;
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     */
    public function deletePrice(string $productCode, int $id): NoContent
    {
        $price = $this->priceService->getPriceById($id);
        if ($price->getProduct()->getCode() !== $productCode) {
            throw new RestNotFoundException();
        }

        $this->repository->beginTransaction();
        try {
            $this->priceService->deletePrice(new ProductPriceDeleteStruct($price));
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return new NoContent();
    }
}
