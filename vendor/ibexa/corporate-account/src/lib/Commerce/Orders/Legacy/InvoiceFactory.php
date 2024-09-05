<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Legacy;

use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Bundle\Commerce\LocalOrderManagement\Entity\Invoice as InvoiceEntity;
use Ibexa\Bundle\Commerce\LocalOrderManagement\Service\InvoiceService;
use Ibexa\CorporateAccount\Commerce\Orders\InvoiceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @internal
 */
final class InvoiceFactory implements InvoiceFactoryInterface
{
    private ?InvoiceService $invoiceService;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(?InvoiceService $invoiceService, UrlGeneratorInterface $urlGenerator)
    {
        $this->invoiceService = $invoiceService;
        $this->urlGenerator = $urlGenerator;
    }

    public function createInvoiceForBasket(Basket $basket): ?InvoiceInterface
    {
        if ($this->invoiceService === null) {
            return null;
        }

        /** @var \Ibexa\Bundle\Commerce\LocalOrderManagement\Entity\Invoice|null $entity */
        $entity = $this->invoiceService->getInvoiceByBasketId($basket->getBasketId());
        if ($entity instanceof InvoiceEntity) {
            return new Invoice(
                $this->getInvoiceSymbol($entity),
                $this->getInvoiceUrl($entity)
            );
        }

        return null;
    }

    private function getInvoiceSymbol(InvoiceEntity $entity): string
    {
        return (string)$entity->getInvoiceNumber();
    }

    private function getInvoiceUrl(InvoiceEntity $entity): string
    {
        return $this->urlGenerator->generate(
            'ibexa.commerce.menu.admin.order_management.invoice',
            [
                'invoice_number' => $entity->getInvoiceNumber(),
            ]
        );
    }
}
