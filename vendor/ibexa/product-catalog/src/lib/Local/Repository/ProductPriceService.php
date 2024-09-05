<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use DateTime;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\SortClause\FieldValueSortClause;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceQuery;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Exception\MissingHandlingServiceException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\DomainMapperInterface as CurrencyDomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Repository\Values\PriceList;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use LogicException;
use Money\Currency as MoneyCurrency;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

final class ProductPriceService implements ProductPriceServiceInterface
{
    private HandlerInterface $handler;

    private TransactionHandler $transactionHandler;

    private ProductServiceInterface $productService;

    private DecimalMoneyFactory $decimalMoneyParserFactory;

    private PermissionResolverInterface $permissionResolver;

    private CurrencyDomainMapperInterface $currencyDomainMapper;

    private ProxyDomainMapper $proxyDomainMapper;

    private ContentService $contentService;

    private CriterionMapper $criterionMapper;

    /** @var iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface> */
    private iterable $domainMappers;

    private ValidatorInterface $validator;

    /**
     * @param iterable<\Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface> $domainMappers
     */
    public function __construct(
        HandlerInterface $handler,
        TransactionHandler $transactionHandler,
        ProductServiceInterface $productService,
        DecimalMoneyFactory $decimalMoneyParserFactory,
        CurrencyDomainMapperInterface $currencyInstantiator,
        PermissionResolverInterface $permissionResolver,
        ProxyDomainMapper $proxyDomainMapper,
        ContentService $contentService,
        CriterionMapper $criterionMapper,
        ValidatorInterface $validator,
        iterable $domainMappers
    ) {
        $this->handler = $handler;
        $this->transactionHandler = $transactionHandler;
        $this->productService = $productService;
        $this->permissionResolver = $permissionResolver;
        $this->decimalMoneyParserFactory = $decimalMoneyParserFactory;
        $this->domainMappers = $domainMappers;
        $this->proxyDomainMapper = $proxyDomainMapper;
        $this->contentService = $contentService;
        $this->criterionMapper = $criterionMapper;
        $this->currencyDomainMapper = $currencyInstantiator;
        $this->validator = $validator;
    }

    public function findOneForCustomerGroup(
        PriceInterface $price,
        CustomerGroupInterface $customerGroup
    ): ?CustomPriceAwareInterface {
        $criteria = [
            'currency_id' => $price->getCurrency()->getId(),
            'product_code' => $price->getProduct()->getCode(),
            'customer_group_id' => $customerGroup->getId(),
            'discriminator' => 'customer_group',
        ];

        $spiPrice = $this->handler->findOneBy($criteria);

        if ($spiPrice === null) {
            return null;
        }

        $price = $this->buildDomainPriceObject($spiPrice);

        if (!$price instanceof CustomPriceAwareInterface) {
            throw new LogicException(sprintf(
                'Expected %s to return an instance of %s from %s method with %s discriminator',
                __METHOD__,
                CustomPriceAwareInterface::class,
                self::class . '::buildDomainPriceObject',
                'customer_group',
            ));
        }

        return $price;
    }

    public function findPricesByProductCode(string $code): PriceListInterface
    {
        $product = $this->productService->getProduct($code);

        $prices = $this->handler->findBy([
            'product_code' => $code,
            'discriminator' => 'main',
        ]);

        $results = [];
        foreach ($prices as $price) {
            $results[] = $this->buildDomainPriceObject($price, $product);
        }

        return new PriceList($results, count($results));
    }

    public function getPriceByProductAndCurrency(ProductInterface $product, CurrencyInterface $currency): PriceInterface
    {
        $criteria = [
            'currency_id' => $currency->getId(),
            'product_code' => $product->getCode(),
            'discriminator' => 'main',
        ];

        $spiPrice = $this->handler->findOneBy($criteria);
        if ($spiPrice === null) {
            $identifier = [
                'product_code' => $product->getCode(),
                'currency_code' => $currency->getCode(),
                'type' => 'main',
            ];

            throw new NotFoundException(PriceInterface::class, $identifier);
        }

        return $this->buildDomainPriceObject($spiPrice, $product);
    }

    public function getPriceById(int $id): PriceInterface
    {
        $productPrice = $this->handler->find($id);

        return $this->buildDomainPriceObject(
            $productPrice,
            $this->productService->getProduct($productPrice->getProductCode())
        );
    }

    /**
     * @throws \Throwable
     */
    public function createProductPrice(ProductPriceCreateStructInterface $struct): PriceInterface
    {
        $product = $struct->getProduct();

        $this->permissionResolver->assertPolicy(new PreEdit($product));

        $this->transactionHandler->beginTransaction();
        try {
            $errors = $this->validator->validate($struct);
            if ($errors->count() > 0) {
                throw new ValidationFailedException($struct, $errors);
            }

            $id = $this->handler->create($struct);
            $this->updateProductContentMetadata($product);

            $this->transactionHandler->commit();
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $productPrice = $this->handler->find($id);

        return $this->buildDomainPriceObject($productPrice);
    }

    /**
     * @throws \Throwable
     */
    public function updateProductPrice(ProductPriceUpdateStructInterface $struct): PriceInterface
    {
        $price = $struct->getPrice();
        $product = $price->getProduct();

        $this->permissionResolver->assertPolicy(new PreEdit($product));

        $this->transactionHandler->beginTransaction();
        try {
            $this->handler->update($struct);
            $this->updateProductContentMetadata($product);

            $this->transactionHandler->commit();
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $productPrice = $this->handler->find($price->getId());

        return $this->buildDomainPriceObject($productPrice);
    }

    /**
     * @throws \Throwable
     */
    public function execute(iterable $structs): void
    {
        $this->transactionHandler->beginTransaction();
        try {
            foreach ($structs as $struct) {
                $struct->execute($this);
            }

            $this->transactionHandler->commit();
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }
    }

    /**
     * @throws \Throwable
     */
    public function deletePrice(ProductPriceDeleteStructInterface $struct): void
    {
        $price = $struct->getPrice();

        $this->permissionResolver->assertPolicy(new Delete($price->getProduct()));

        $this->transactionHandler->beginTransaction();
        try {
            $this->updateProductContentMetadata($price->getProduct());
            $this->handler->delete($price->getId());

            $this->transactionHandler->commit();
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }
    }

    private function buildDomainPriceObject(
        AbstractProductPrice $spiPrice,
        ?ProductInterface $product = null
    ): PriceInterface {
        $moneyParser = $this->decimalMoneyParserFactory->getMoneyParser();
        $moneyFormatter = $this->decimalMoneyParserFactory->getMoneyFormatter();
        $moneyCurrency = new MoneyCurrency($spiPrice->getCurrency()->code);
        $money = $moneyParser->parse($spiPrice->getAmount(), $moneyCurrency);
        $product = $product ?? $this->proxyDomainMapper->createProductProxy($spiPrice->getProductCode());
        $currency = $this->currencyDomainMapper->createFromSpi($spiPrice->getCurrency());

        foreach ($this->domainMappers as $spiMapper) {
            if ($spiMapper->canMapSpiPrice($spiPrice)) {
                return $spiMapper->mapSpiPrice(
                    $moneyFormatter,
                    $moneyParser,
                    $product,
                    $currency,
                    $spiPrice,
                    $money,
                );
            }
        }

        throw new MissingHandlingServiceException(
            $this->domainMappers,
            $spiPrice,
            DomainMapperInterface::class,
            'ibexa.product_catalog.product_price.inheritance.domain_mapper',
        );
    }

    private function updateProductContentMetadata(ProductInterface $product): void
    {
        if (!$product instanceof ContentAwareProductInterface) {
            return;
        }

        $content = $product->getContent();
        $metadataUpdateStruct = $this->contentService->newContentMetadataUpdateStruct();
        $metadataUpdateStruct->modificationDate = new DateTime();

        $this->contentService->updateContentMetadata(
            $content->contentInfo,
            $metadataUpdateStruct
        );
    }

    public function findPrices(?PriceQuery $query = null): PriceListInterface
    {
        $query ??= new PriceQuery();

        $criteria = $this->convertCriteria($query->getQuery());

        $totalCount = $this->handler->countBy($criteria);
        if ($totalCount === 0) {
            return new PriceList([], 0);
        }

        $items = [];
        if ($query->getLimit() > 0) {
            $prices = $this->handler->findBy(
                $criteria,
                $this->convertSortClauses($query),
                $query->getLimit(),
                $query->getOffset()
            );

            foreach ($prices as $price) {
                $items[] = $this->buildDomainPriceObject($price);
            }
        }

        return new PriceList($items, $totalCount);
    }

    /**
     * @return array<array-key, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression>
     */
    private function convertCriteria(?CriterionInterface $criterion): array
    {
        if ($criterion !== null) {
            return [
                $this->criterionMapper->handle($criterion),
            ];
        }

        return [];
    }

    /**
     * @return array<string, 'ASC'|'DESC'>
     */
    private function convertSortClauses(PriceQuery $query): array
    {
        $sortBy = [];
        foreach ($query->getSortClauses() as $sortClause) {
            if ($sortClause instanceof FieldValueSortClause) {
                $direction = $sortClause->getDirection() === AbstractSortClause::SORT_ASC ? 'ASC' : 'DESC';
                $sortBy[$sortClause->getField()] = $direction;
            }
        }

        return $sortBy;
    }
}
