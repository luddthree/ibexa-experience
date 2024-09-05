<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductCode implements CriterionInterface
{
    /** @var string[] */
    private array $codes;

    /**
     * @param string[] $codes
     */
    public function __construct(array $codes)
    {
        $this->codes = $codes;
    }

    /**
     * @return string[]
     */
    public function getCodes(): array
    {
        return $this->codes;
    }
}
