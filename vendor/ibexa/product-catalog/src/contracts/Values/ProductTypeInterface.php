<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

interface ProductTypeInterface
{
    public function getIdentifier(): string;

    public function getName(): string;

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface[]
     */
    public function getAttributesDefinitions(): iterable;

    public function getVatCategory(RegionInterface $region): ?VatCategoryInterface;

    public function isVirtual(): bool;
}
