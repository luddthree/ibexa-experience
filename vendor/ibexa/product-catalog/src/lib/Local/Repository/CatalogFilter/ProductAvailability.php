<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability as ProductAvailabilityCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ProductAvailability extends StandardDefinition implements TranslationContainerInterface
{
    public function getIdentifier(): string
    {
        return 'product_availability';
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Availability") */
            'filter.product_availability.label',
            [],
            'ibexa_product_catalog'
        );
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductAvailabilityCriterion;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                'filter.product_availability.value.0',
                'ibexa_product_catalog'
            )->setDesc('Unavailable'),
            Message::create(
                'filter.product_availability.value.1',
                'ibexa_product_catalog'
            )->setDesc('Available'),
        ];
    }
}
