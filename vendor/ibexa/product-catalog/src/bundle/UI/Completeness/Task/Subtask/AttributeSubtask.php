<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\AbstractTask;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class AttributeSubtask extends AbstractTask
{
    private AttributeInterface $attribute;

    public function __construct(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getIdentifier(): string
    {
        return $this->attribute->getIdentifier() . '_attribute_task';
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        return new BooleanEntry($this->attribute->getValue() !== null);
    }

    public function getName(): string
    {
        return $this->attribute->getAttributeDefinition()->getName();
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
