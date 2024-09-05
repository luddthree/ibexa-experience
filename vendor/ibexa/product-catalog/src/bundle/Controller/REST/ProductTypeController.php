<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductType;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeList;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeUsage;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class ProductTypeController extends RestController
{
    private ProductTypeServiceInterface $productTypeService;

    private LocalProductTypeServiceInterface $localProductTypeService;

    public function __construct(
        ProductTypeServiceInterface $productTypeService,
        LocalProductTypeServiceInterface $localProductTypeService
    ) {
        $this->productTypeService = $productTypeService;
        $this->localProductTypeService = $localProductTypeService;
    }

    /**
     * @deprecated since 4.3. Use ibexa.product_catalog.rest.product_types.view route instead.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function listProductTypesAction(Request $request): Value
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.3',
            sprintf(
                '%s route is deprecated and will be removed in 5.0. Use %s route instead.',
                'ibexa.product_catalog.rest.product_types',
                'ibexa.product_catalog.rest.product_types.view'
            ),
        );

        $restProductTypes = [];
        $productTypeQuery = new ProductTypeQuery();
        $languageSettings = null;
        $requestContent = $request->getContent();

        if (!empty($requestContent)) {
            /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeQueryStruct $input */
            $input = $this->inputDispatcher->parse(
                new Message(
                    ['Content-Type' => $request->headers->get('Content-Type')],
                    $requestContent
                )
            );

            $productTypeQuery->setNamePrefix($input->getNamePrefix());
            $productTypeQuery->setOffset($input->getOffset());
            $productTypeQuery->setLimit($input->getLimit());

            $languageSettings = new LanguageSettings($input->getLanguages());
        }

        $productTypes = $this->productTypeService->findProductTypes($productTypeQuery, $languageSettings);

        foreach ($productTypes as $productType) {
            $restProductTypes[] = new ProductType($productType);
        }

        return new ProductTypeList($restProductTypes);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getProductTypeAction(Request $request, string $identifier): Value
    {
        $languageSettings = null;

        if (!empty($request->getContent())) {
            /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeLanguageStruct $input */
            $input = $this->inputDispatcher->parse(
                new Message(
                    ['Content-Type' => $request->headers->get('Content-Type')],
                    $request->getContent()
                )
            );

            $languageSettings = new LanguageSettings($input->getLanguages());
        }

        $productType = $this->productTypeService->getProductType($identifier, $languageSettings);

        return new ProductType($productType);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProductTypeAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $productTypeCreateStruct = new ProductTypeCreateStruct(
            $input->getContentTypeCreateStruct(),
            $input->getMainLanguageCode(),
            $input->getAssignedAttributesDefinitions()
        );

        $productType = $this->localProductTypeService->createProductType($productTypeCreateStruct);

        return new ProductType($productType);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateProductTypeAction(Request $request, string $identifier): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $productType = $this->productTypeService->getProductType($identifier);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        $productTypeUpdateStruct = new ProductTypeUpdateStruct(
            $productType,
            $input->getContentTypeUpdateStruct()
        );

        $productTypeUpdateStruct->setAttributeDefinitionStructs(
            $input->getAttributeDefinitionStructs()
        );

        $productType = $this->localProductTypeService->updateProductType($productTypeUpdateStruct);

        return new ProductType($productType);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProductTypeAction(string $identifier): Value
    {
        $productType = $this->productTypeService->getProductType($identifier);
        $this->localProductTypeService->deleteProductType($productType);

        return new NoContent();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function isProductTypeUsedAction(string $identifier): Value
    {
        $productType = $this->productTypeService->getProductType($identifier);

        return new ProductTypeUsage(
            $this->localProductTypeService->isProductTypeUsed($productType)
        );
    }
}
