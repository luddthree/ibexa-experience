<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Bridge;

use Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceConstants;
use Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceLine;
use Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceLineAmounts;
use Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceRequest;
use Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceResponse;
use Ibexa\Bundle\Commerce\Price\Api\AbstractPriceProvider;
use Ibexa\Bundle\Commerce\Price\Api\PriceProviderInterface;
use Ibexa\Bundle\Commerce\Price\Api\VatServiceInterface;
use Ibexa\Bundle\Commerce\PriceEngine\Service\ShippingCostCalculator;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated since 4.6, will be removed in 5.0. Use ibexa/checkout and ibexa/order-management packages instead
 */
final class PriceProvider extends AbstractPriceProvider implements PriceProviderInterface, TranslationContainerInterface
{
    public const SOURCE_TYPE = 'Ibexa';

    private ConfigResolverInterface $configResolver;

    private ShippingCostCalculator $shippingCostCalculator;

    private TranslatorInterface $translator;

    private VatServiceInterface $vatService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        ShippingCostCalculator $shippingCostCalculator,
        TranslatorInterface $translator,
        VatServiceInterface $vatService
    ) {
        $this->configResolver = $configResolver;
        $this->shippingCostCalculator = $shippingCostCalculator;
        $this->vatService = $vatService;
        $this->translator = $translator;
    }

    public function calculatePrices(PriceRequest $request): PriceResponse
    {
        $lines = [];
        foreach ($request->getLines() as $line) {
            $lines[] = $this->processLine($line);
        }

        $totals = $this->calculateTotalsForLines($lines);
        $totalGross = $totals->getTotalGross();

        $additionalLines = [];
        $shippingPriceLine = $this->createShippingPriceLine($totalGross, $request);
        if ($shippingPriceLine instanceof PriceLine) {
            $additionalLines[] = $shippingPriceLine;
        }

        $response = new PriceResponse();
        $response->setGeneralCurrencyCode($request->getCustomerCurrency());
        $response->setSourceType(self::SOURCE_TYPE);
        $response->setLines($lines);
        $response->setAdditionalLines($additionalLines);
        $response->setTotals($this->getTotals($lines, $additionalLines));

        return $response;
    }

    private function createShippingPriceLine(
        float $totalGross,
        PriceRequest $priceRequest
    ): ?PriceLine {
        $this->shippingCostCalculator->setPriceRequest($priceRequest);

        $shopId = $this->configResolver->getParameter('shop_id', 'ibexa.commerce.site_access.config.core');
        $shippingCost = $this->shippingCostCalculator->calculateShipping($shopId, $totalGross);

        if ($shippingCost == null) {
            return null;
        }

        $priceLine = new PriceLine();
        $priceLine->setType(PriceConstants::PRICE_RESPONSE_LINE_TYPE_SHIPPING);

        $shippingVatCountry = $this->configResolver->getParameter('shipping_vat_country', 'ibexa.commerce.site_access.config.core');
        $shippingVatCode = $this->configResolver->getParameter('shipping_vat_code', 'ibexa.commerce.site_access.config.core');
        $vatPercent = $this->vatService->getVatPercent($shippingVatCountry, $shippingVatCode);

        $shippingPrice = new PriceLineAmounts();
        $shippingCostVat = ($shippingCost * $vatPercent) / (100 + $vatPercent);
        $shippingCostNet = $shippingCost - $shippingCostVat;
        $shippingPrice->setLineAmountGross($shippingCost);
        $shippingPrice->setLineAmountNet($shippingCostNet);
        $shippingPrice->setLineAmountVat($shippingCostVat);
        $shippingPrice->setUnitPriceGross($shippingCost);
        $shippingPrice->setUnitPriceNet($shippingCostNet);
        $shippingPrice->setUnitPriceVat($shippingCostVat);
        $shippingPrice->setSource(self::SOURCE_TYPE);

        $prices[PriceConstants::PRICE_RESPONSE_PRICE_TYPE_CUSTOM] = $shippingPrice;
        $prices[PriceConstants::PRICE_RESPONSE_PRICE_TYPE_LIST] = $shippingPrice;
        $priceLine->setPrices($prices);
        $priceLine->setVatPercent($vatPercent);

        $extendedData = [
            'LineType' => 1,
            'CostType' => PriceConstants::PRICE_RESPONSE_LINE_TYPE_SHIPPING,
            'StockNumeric' => '',
            'AvailabilityColor' => '',
            'VatPercent' => $vatPercent,
            'PriceAmountGross' => $shippingCost,
            'PriceIsIncVat' => 1,
            'BelongsToLine' => '',
            /** @Ignore */
            'name' => $this->translator->trans('shipping_method.' . $priceRequest->getShippingMethod(), [], 'price_provider'),
        ];
        $priceLine->setExtendedData($extendedData);

        return $priceLine;
    }

    /**
     * @param \Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceLine[] $lines
     * @param \Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceLine[] $additionalLines
     *
     * @return array{
     *     lines?: \Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceResponseTotals,
     *     additional_lines?: \Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceResponseTotals,
     *     sum?: \Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceResponseTotals,
     * }
     */
    private function getTotals(array $lines, array $additionalLines): array
    {
        if (empty($lines) && empty($additionalLines)) {
            return [];
        }

        $totalForLines = $this->calculateTotalsForLines($lines);
        $totalForAdditionalLines = $this->calculateTotalsForLines($additionalLines);

        $response = [];
        $response[PriceConstants::PRICE_RESPONSE_TOTALS_LINES] = $totalForLines;
        $response[PriceConstants::PRICE_RESPONSE_TOTALS_ADDITIONAL_LINES] = $totalForAdditionalLines;
        $response[PriceConstants::PRICE_RESPONSE_TOTALS_SUM] = $this->calculateTotalsSum([$totalForLines, $totalForAdditionalLines]);

        return $response;
    }

    private function processLine(PriceLine $line): PriceLine
    {
        $prices = $line->getPrices();
        /** @var \Ibexa\Bundle\Commerce\Eshop\Model\Price\PriceLineAmounts $basePrice */
        $basePrice = $prices[PriceConstants::PRICE_REQUEST_PRICE_TYPE_BASE];

        $listPrice = $this->determinePriceFromRequest(
            $basePrice,
            $line->getQuantity(),
            $line->getVatPercent(),
        );

        $customerPrice = $this->determinePriceFromRequest(
            $basePrice,
            $line->getQuantity(),
            $line->getVatPercent(),
        );

        $response = new PriceLine();
        $response->setType(PriceConstants::PRICE_RESPONSE_LINE_TYPE_PRODUCT);
        $response->setId($line->getId());
        $response->setQuantity($line->getQuantity());
        $response->setSku($line->getSku());
        $response->setStockNumeric($line->getStockNumeric());
        $response->setVatCode($line->getVatCode());
        $response->setVatPercent($line->getVatPercent());
        $response->setPrices([
            PriceConstants::PRICE_RESPONSE_PRICE_TYPE_LIST => $listPrice,
            PriceConstants::PRICE_RESPONSE_PRICE_TYPE_CUSTOM => $customerPrice,
        ]);

        return $response;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('shipping_method.standard', 'price_provider')->setDesc('Standard'),
            Message::create('shipping_method.expressDel', 'price_provider')->setDesc('Express delivery'),
        ];
    }
}
