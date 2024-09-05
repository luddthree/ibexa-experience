<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;

final class ProductQueryStruct extends ValueObject
{
    private int $offset;

    private int $limit;

    /** @var array<string> */
    private array $languages;

    /**
     * @param array<string> $languages
     */
    public function __construct(
        int $offset = 0,
        int $limit = ProductQuery::DEFAULT_LIMIT,
        array $languages = []
    ) {
        parent::__construct();

        $this->offset = $offset;
        $this->limit = $limit;
        $this->languages = $languages;
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
     * @return array<string>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
