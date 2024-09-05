<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AvailabilityTask extends AbstractTask
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Availability") */ 'product.completeness.availability.label', [], 'ibexa_product_catalog');
    }

    public function getIdentifier(): string
    {
        return 'availability';
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        if (!$product instanceof AvailabilityAwareInterface || $product->isBaseProduct()) {
            return null;
        }

        return new BooleanEntry($product->hasAvailability());
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        return null;
    }

    /**
     * @phpstan-return int<1, max>
     */
    public function getWeight(): int
    {
        return 1;
    }

    public function getEditUrl(ProductInterface $product): ?string
    {
        return null;
    }
}
