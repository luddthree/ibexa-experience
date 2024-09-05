<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;

final class ProductTypeQueryStruct extends ValueObject
{
    private ?string $namePrefix;

    private int $offset;

    private int $limit;

    /** @var array<string> */
    private array $languages;

    /**
     * @param array<string> $languages
     */
    public function __construct(
        ?string $namePrefix = null,
        int $offset = 0,
        int $limit = ProductTypeQuery::DEFAULT_LIMIT,
        array $languages = []
    ) {
        parent::__construct();

        $this->namePrefix = $namePrefix;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->languages = $languages;
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

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
