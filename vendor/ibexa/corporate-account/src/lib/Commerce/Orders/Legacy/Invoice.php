<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Legacy;

use Ibexa\CorporateAccount\Commerce\Orders\InvoiceInterface;

final class Invoice implements InvoiceInterface
{
    private string $symbol;

    private string $url;

    public function __construct(string $symbol, string $url)
    {
        $this->symbol = $symbol;
        $this->url = $url;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
