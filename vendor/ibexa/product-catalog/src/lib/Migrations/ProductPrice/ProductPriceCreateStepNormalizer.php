<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductPrice;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency;
use Money\MoneyParser;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductPriceCreateStep
 * >
 */
final class ProductPriceCreateStepNormalizer extends AbstractStepNormalizer
{
    private DecimalMoneyFactory $decimalMoneyFactory;

    public function __construct(DecimalMoneyFactory $decimalMoneyFactory)
    {
        $this->decimalMoneyFactory = $decimalMoneyFactory;
    }

    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductPriceCreateStep $object
     */
    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        $formatter = $this->decimalMoneyFactory->getMoneyFormatter();

        return [
            'product_code' => $object->getProductCode(),
            'amount' => $formatter->format($object->getAmount()),
            'currency_code' => $object->getCurrencyCode(),
        ];
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'product_code');
        Assert::stringNotEmpty($data['product_code']);
        Assert::keyExists($data, 'amount');
        Assert::numeric($data['amount']);
        Assert::keyExists($data, 'currency_code');
        Assert::stringNotEmpty($data['currency_code']);

        $parser = $this->decimalMoneyFactory->getMoneyParser();

        $currency = new Currency($data['currency_code']);

        return new ProductPriceCreateStep(
            $data['product_code'],
            $parser->parse((string)$data['amount'], $currency),
            $data['currency_code'],
            array_map(
                fn (array $data): ProductCustomPrice => $this->denormalizeCustomPrice($parser, $currency, $data),
                $data['custom_prices'] ?? []
            )
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function denormalizeCustomPrice(MoneyParser $parser, Currency $currency, array $data): ProductCustomPrice
    {
        Assert::keyExists($data, 'customer_group');
        Assert::stringNotEmpty($data['customer_group']);

        $baseAmount = null;
        if (isset($data['base_amount'])) {
            Assert::numeric($data['base_amount']);

            $baseAmount = $parser->parse((string)$data['base_amount'], $currency);
        }

        $customAmount = null;
        if (isset($data['custom_amount'])) {
            Assert::numeric($data['custom_amount']);

            $customAmount = $parser->parse((string)$data['custom_amount'], $currency);
        }

        return new ProductCustomPrice(
            $data['customer_group'],
            $baseAmount,
            $customAmount
        );
    }

    public function getHandledClassType(): string
    {
        return ProductPriceCreateStep::class;
    }

    public function getType(): string
    {
        return 'product_price';
    }

    public function getMode(): string
    {
        return 'create';
    }
}
