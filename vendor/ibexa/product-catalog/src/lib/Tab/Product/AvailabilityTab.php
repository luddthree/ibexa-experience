<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AvailabilityTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-availability';

    private ProductAvailabilityServiceInterface $availabilityService;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ProductAvailabilityServiceInterface $availabilityService
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->availabilityService = $availabilityService;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        $product = $parameters['product'] ?? null;

        if ($product instanceof ProductInterface) {
            return !$product->isBaseProduct() || $product->isVariant();
        }

        return false;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/availability.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $contextParameters['product'];

        $hasAvailability = $this->availabilityService->hasAvailability($product);
        $availability = $hasAvailability ? $this->availabilityService->getAvailability($product) : null;

        $viewParameters = [
            'has_availability' => $hasAvailability,
            'availability' => $availability,
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    public function getIdentifier(): string
    {
        return 'availability';
    }

    public function getName(): string
    {
        /** @Desc("Availability") */
        return $this->translator->trans('tab.name.availability', [], 'ibexa_product_catalog');
    }

    public function getOrder(): int
    {
        return 400;
    }
}
