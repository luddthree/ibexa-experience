<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

/**
 * @extends \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<integer>
 */
final class IntegerAttribute extends AbstractAttribute
{
    private ?int $value;

    public function __construct(string $identifier, ?int $value)
    {
        parent::__construct($identifier);
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}
