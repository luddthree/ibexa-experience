<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

abstract class AbstractProductPrice extends ValueObject
{
    public int $id;

    /** @var numeric-string */
    private string $amount;

    private Currency $currency;

    /** @var non-empty-string */
    private string $productCode;

    /**
     * @param numeric-string $amount
     * @param non-empty-string $productCode
     */
    public function __construct(
        int $id,
        string $amount,
        Currency $currency,
        string $productCode
    ) {
        parent::__construct();
        $this->id = $id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->productCode = $productCode;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return numeric-string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return non-empty-string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }
}
