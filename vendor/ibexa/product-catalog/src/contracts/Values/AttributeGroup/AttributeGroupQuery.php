<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\AttributeGroup;

final class AttributeGroupQuery
{
    public const DEFAULT_LIMIT = 25;

    private ?string $namePrefix;

    private int $offset;

    private int $limit;

    public function __construct(?string $namePrefix = null, int $offset = 0, int $limit = self::DEFAULT_LIMIT)
    {
        $this->namePrefix = $namePrefix;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getNamePrefix(): ?string
    {
        return $this->namePrefix;
    }

    public function setNamePrefix(?string $namePrefix): void
    {
        $this->namePrefix = $namePrefix;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}
