<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType;

use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Core\FieldType\Value;

/**
 * @experimental
 */
class MeasurementValue extends Value
{
    private ?ValueInterface $value;

    public function __construct(?ValueInterface $value = null)
    {
        $this->value = $value;
        parent::__construct();
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function getValue(): ?ValueInterface
    {
        return $this->value;
    }
}
