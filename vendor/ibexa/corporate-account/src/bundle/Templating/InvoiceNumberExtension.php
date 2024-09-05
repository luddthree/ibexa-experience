<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Templating;

use Ibexa\Bundle\Commerce\LocalOrderManagement\Twig\LocalOrderManagementExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @internal
 */
class InvoiceNumberExtension extends AbstractExtension
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_commerce_invoice_number_wrapper',
                [$this, 'getInvoiceNumberIfExists'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getInvoiceNumberIfExists(string $basketId, int $shopId = null): ?string
    {
        if ($this->twig->hasExtension(LocalOrderManagementExtension::class)) {
            return $this->twig->getExtension(LocalOrderManagementExtension::class)->getInvoiceNumber($basketId, $shopId);
        }

        return null;
    }
}
