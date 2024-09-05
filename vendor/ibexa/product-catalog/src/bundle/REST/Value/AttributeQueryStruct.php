<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;

final class AttributeQueryStruct extends ValueObject
{
    private ?string $groupNamePrefix;

    private ?string $namePrefix;

    private int $offset;

    private int $limit;

    public function __construct(
        ?string $groupNamePrefix = null,
        ?string $namePrefix = null,
        int $offset = 0,
        int $limit = AttributeGroupQuery::DEFAULT_LIMIT
    ) {
        parent::__construct();

        $this->groupNamePrefix = $groupNamePrefix;
        $this->namePrefix = $namePrefix;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getGroupNamePrefix(): ?string
    {
        return $this->groupNamePrefix;
    }

    public function getNamePrefix(): ?string
    {
        return $this->namePrefix;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
