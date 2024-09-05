<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Product\Query\Criterion;

use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;

/**
 * @extends \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface,
 * >
 */
abstract class AbstractMeasurementAttribute extends AbstractAttribute
{
    private SimpleValueInterface $value;

    public function __construct(string $identifier, SimpleValueInterface $value)
    {
        parent::__construct($identifier);
        $this->value = $value;
    }

    public function getValue(): SimpleValueInterface
    {
        return $this->value;
    }
}
