<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\CatalogFilters;

use Symfony\Component\Form\FormBuilderInterface;

interface FilterFormMapperInterface
{
    public function supports(FilterDefinitionInterface $filterDefinition): bool;

    /**
     * @param array<string,mixed> $context
     */
    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void;
}
