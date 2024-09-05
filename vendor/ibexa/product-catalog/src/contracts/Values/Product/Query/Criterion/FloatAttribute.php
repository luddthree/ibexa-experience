<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

/**
 * @extends \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<float>
 */
final class FloatAttribute extends AbstractAttribute
{
    private ?float $value;

    public function __construct(string $identifier, ?float $value)
    {
        parent::__construct($identifier);
        $this->value = $value;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }
}
