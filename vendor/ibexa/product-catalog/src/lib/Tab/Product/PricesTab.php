<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Bundle\ProductCatalog\Form\Data\CurrencySwitchData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CurrencySwitchType;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CustomerGroupListAdapter;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CustomPricesAdapter;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PricesTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-prices';

    private CurrencyServiceInterface $currencyService;

    private ProductPriceServiceInterface $priceService;

    private FormFactoryInterface $formFactory;

    private CustomerGroupServiceInterface $customerGroupService;

    private ConfigResolverInterface $configResolver;

    private RequestStack $requestStack;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        CurrencyServiceInterface $currencyService,
        ProductPriceServiceInterface $priceService,
        CustomerGroupServiceInterface $customerGroupService,
        FormFactoryInterface $formFactory,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->currencyService = $currencyService;
        $this->priceService = $priceService;
        $this->customerGroupService = $customerGroupService;
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/price/prices.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $contextParameters['product'];

        $currency = $this->resolveCurrency($product, $contextParameters['currency'] ?? null);
        if ($currency === null) {
            return array_replace(
                $contextParameters,
                [
                    'currency' => null,
                    'currency_choice_form' => $this->createCurrencyChoiceForm(),
                    'no_currencies' => $this->isMissingAtLeastOneEnabledCurrency(),
                ]
            );
        }

        $mainPrice = $this->getMainPrice($product, $currency);

        $customPriceItems = $mainPrice === null
            ? $this->getCustomerGroups()
            : $this->getCustomPricing($mainPrice);

        $viewParameters = [
            'custom_price_items' => $customPriceItems,
            'currency' => $currency,
            'currency_choice_form' => $this->createCurrencyChoiceForm($currency),
            'main_price' => $mainPrice,
            'no_currencies' => false,
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    public function getIdentifier(): string
    {
        return 'prices';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Prices") */ 'tab.name.prices', [], 'ibexa_product_catalog');
    }

    public function getOrder(): int
    {
        return 300;
    }

    private function createCurrencyChoiceForm(?CurrencyInterface $currency = null): FormView
    {
        $form = $this->formFactory->createNamed(
            '',
            CurrencySwitchType::class,
            new CurrencySwitchData($currency)
        );

        return $form->createView();
    }

    private function getMainPrice(ProductInterface $product, CurrencyInterface $currency): ?PriceInterface
    {
        try {
            return $this->priceService->getPriceByProductAndCurrency($product, $currency);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Ibexa\Bundle\ProductCatalog\UI\CustomPrice>
     */
    private function getCustomPricing(?PriceInterface $mainPrice): Pagerfanta
    {
        $adapter = new NullAdapter();
        if ($mainPrice !== null) {
            $adapter = new CustomPricesAdapter(
                new CustomerGroupListAdapter($this->customerGroupService),
                $this->priceService,
                $mainPrice
            );
        }

        $maxPerPage = $this->configResolver->getParameter(
            'product_catalog.pagination.product_view_custom_prices_limit'
        );

        $customPrices = new Pagerfanta($adapter);
        $customPrices->setMaxPerPage($maxPerPage);
        $customPrices->setCurrentPage($this->resolveCurrentPage());

        return $customPrices;
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup>
     */
    private function getCustomerGroups(): Pagerfanta
    {
        $adapter = new CustomerGroupListAdapter($this->customerGroupService);
        $maxPerPage = $this->configResolver->getParameter(
            'product_catalog.pagination.customer_groups_limit'
        );

        $customerGroups = new Pagerfanta($adapter);
        $customerGroups->setMaxPerPage($maxPerPage);
        $customerGroups->setCurrentPage($this->resolveCurrentPage());

        return $customerGroups;
    }

    private function resolveCurrentPage(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $page = $request->query->all('page');

            return (int)($page['custom_prices'] ?? 1);
        }

        return 1;
    }

    private function resolveCurrency(ProductInterface $product, ?string $currencyCode): ?CurrencyInterface
    {
        if ($currencyCode !== null) {
            try {
                $currency = $this->currencyService->getCurrencyByCode($currencyCode);
                if ($currency->isEnabled()) {
                    return $currency;
                }
            } catch (NotFoundException $e) {
            }
        }

        // Get currency from first defined price as a fallback
        $priceList = $this->priceService->findPricesByProductCode($product->getCode());
        foreach ($priceList as $price) {
            return $price->getCurrency();
        }

        return $this->getFirstEnabledCurrency();
    }

    private function isMissingAtLeastOneEnabledCurrency(): bool
    {
        return $this->getFirstEnabledCurrency() === null;
    }

    private function getFirstEnabledCurrency(): ?CurrencyInterface
    {
        $query = new CurrencyQuery(new IsCurrencyEnabledCriterion());
        $query->setLimit(1);

        $currencies = $this->currencyService->findCurrencies($query);
        foreach ($currencies as $currency) {
            return $currency;
        }

        return null;
    }
}
