<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Currency;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

final class CurrencyUpdateData extends AbstractCurrencyData
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function createFromCurrency(CurrencyInterface $currency): self
    {
        $self = new self($currency->getId());

        $self
            ->setCode($currency->getCode())
            ->setSubunits($currency->getSubUnits())
            ->setEnabled($currency->isEnabled())
        ;

        return $self;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
