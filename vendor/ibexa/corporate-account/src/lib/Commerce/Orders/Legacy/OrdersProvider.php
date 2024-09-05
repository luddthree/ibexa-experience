<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Legacy;

use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Commerce\Checkout\Service\BasketService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersFilter;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersProviderInterface;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersSum;
use Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\Handler;

final class OrdersProvider implements OrdersProviderInterface
{
    private const BASKET_STATE_MAP = [
        OrdersFilter::STATUS_PAID => BasketService::STATE_PAYED,
        OrdersFilter::STATUS_CONFIRMED => BasketService::STATE_CONFIRMED,
    ];

    private Handler $handler;

    private EntityManagerInterface $entityManager;

    private ConfigResolverInterface $configResolver;

    private OrderFactoryInterface $orderFactory;

    public function __construct(
        Handler $handler,
        EntityManagerInterface $entityManager,
        OrderFactoryInterface $orderFactory,
        ConfigResolverInterface $configResolver
    ) {
        $this->handler = $handler;
        $this->entityManager = $entityManager;
        $this->orderFactory = $orderFactory;
        $this->configResolver = $configResolver;
    }

    public function getCompanyOrderList(OrdersFilter $ordersFilter, int $offset = 0, ?int $limit = 30): array
    {
        $list = [];
        foreach ($this->findOrders($ordersFilter, $offset, $limit) as $basket) {
            $list[] = $this->orderFactory->createFromBasket(
                $ordersFilter->company,
                $basket
            );
        }

        return $list;
    }

    public function getCompanyOrdersCount(OrdersFilter $ordersFilter): int
    {
        $corporateData = $this->handler->findBy(['company_id' => $ordersFilter->company->getId()]);
        $membersIdList = array_column($corporateData, 'memberId');

        return count(
            $this->getAllBasketsByFilter(
                $membersIdList,
                $ordersFilter->orderStatusList,
                $ordersFilter->transactionId,
            )
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getResult()
        );
    }

    /**
     * @param int[] $membersIdList
     * @param string[] $statuses
     */
    private function getAllBasketsByFilter(
        array $membersIdList,
        array $statuses = [],
        ?string $paymentTransactionId = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null,
        ?int $limit = 30,
        ?int $offset = 0
    ): Query {
        $parameters = [];

        $dql = 'SELECT b FROM IbexaCommerceCheckoutBundle:Basket b'
            . ' WHERE b.type = :basketType';

        $parameters['basketType'] = BasketService::TYPE_BASKET;

        $dql .= ' AND b.userId IN (:usersIds)';
        $parameters['usersIds'] = $membersIdList;

        if (!empty($statuses)) {
            $dql .= ' AND b.state IN (:state)';
            $parameters['state'] = $this->mapOrderStatuses($statuses);
        }

        if ($paymentTransactionId !== null) {
            $dql .= ' AND b.paymentTransactionId = :paymentTransactionId';
            $parameters['paymentTransactionId'] = $paymentTransactionId;
        }

        if ($dateFrom != null) {
            $dql .= ' AND b.dateLastModified >= :fromDate';
            $parameters['fromDate'] = $dateFrom->format('Y-m-d H:i:s');
        }

        if ($dateTo != null) {
            $dql .= ' AND b.dateLastModified <= :toDate';
            $parameters['toDate'] = $dateTo->format('Y-m-d H:i:s');
        }

        $dql .= ' ORDER BY b.dateLastModified DESC';

        return $this->getQuery($dql, $parameters, $limit, $offset);
    }

    /**
     * @param string[] $statuses
     *
     * @return string[]
     */
    private function mapOrderStatuses(array $statuses): array
    {
        return array_map(
            static fn (string $status): string => self::BASKET_STATE_MAP[$status],
            $statuses
        );
    }

    /**
     * @return \Ibexa\Bundle\Commerce\Checkout\Entity\Basket[]
     */
    private function findOrders(OrdersFilter $ordersFilter, int $offset = 0, ?int $limit = 30): array
    {
        $corporateData = $this->handler->findBy(['company_id' => $ordersFilter->company->getId()]);
        $membersIdList = array_column($corporateData, 'memberId');

        return $this->getAllBasketsByFilter(
            $membersIdList,
            $ordersFilter->orderStatusList,
            $ordersFilter->transactionId,
            $ordersFilter->from,
            $ordersFilter->to,
            $limit,
            $offset
        )->getResult();
    }

    /** @param array<string, mixed> $parameters */
    private function getQuery(
        string $dql,
        array $parameters,
        ?int $limit,
        ?int $offset
    ): Query {
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($parameters);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query;
    }

    public function getOrdersSum(OrdersFilter $filter): OrdersSum
    {
        $orders = $this->findOrders($filter, 0, null);

        $grossTotals = array_map(
            fn (Basket $basket) => $this->convertToCurrency($basket->getTotalsSum()->getTotalGross(), $basket->getCurrency()),
            $orders
        );

        return new OrdersSum(
            array_sum($grossTotals),
            $this->configResolver->getParameter('standard_price_factory.base_currency', 'ibexa.commerce.site_access.config.core'),
            count($orders)
        );
    }

    private function convertToCurrency(float $price, string $currency): float
    {
        $currencyRates = $this->configResolver->getParameter('currency_list', 'ibexa.commerce.site_access.config.core');
        $baseCurrency = $this->configResolver->getParameter('standard_price_factory.base_currency', 'ibexa.commerce.site_access.config.core');
        $autoConversion = $this->configResolver->getParameter('automatic_currency_conversion', 'ibexa.commerce.site_access.config.core');

        if ($baseCurrency === $currency) {
            return $price;
        }

        if (!$autoConversion) {
            return 0.0;
        }

        if (isset($currencyRates[$currency])) {
            return $price * $currencyRates[$currency];
        }

        return 0.0;
    }
}
