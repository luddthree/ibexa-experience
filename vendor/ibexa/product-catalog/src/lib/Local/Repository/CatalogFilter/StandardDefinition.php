<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class StandardDefinition implements FilterDefinitionInterface
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getGroupName(): string
    {
        return $this->translator->trans(
            /** @Desc("Standard filters") */
            'filter.group.standard.label',
            [],
            'ibexa_product_catalog'
        );
    }
}
