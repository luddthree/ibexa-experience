<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\ProductCatalog\Money\IntlMoneyFactory;
use Money\Money;
use Twig\Extension\RuntimeExtensionInterface;

final class PriceRuntime implements RuntimeExtensionInterface
{
    private IntlMoneyFactory $moneyFactory;

    public function __construct(IntlMoneyFactory $moneyFactory)
    {
        $this->moneyFactory = $moneyFactory;
    }

    public function formatPrice(?Money $money): string
    {
        if ($money === null) {
            return '';
        }

        $formatter = $this->moneyFactory->getMoneyFormatter();

        return $formatter->format($money);
    }
}
