<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Value\Definition;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;

/**
 * @internal
 */
class GenericUnit implements UnitInterface
{
    private string $symbol;

    private string $identifier;

    private bool $isBaseUnit;

    public function __construct(string $identifier, string $symbol, bool $isBaseUnit = false)
    {
        $this->identifier = $identifier;
        $this->symbol = $symbol;
        $this->isBaseUnit = $isBaseUnit;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function isBaseUnit(): bool
    {
        return $this->isBaseUnit;
    }

    public function equals(UnitInterface $unit): bool
    {
        return $this->identifier === $unit->getIdentifier() && $this->symbol === $unit->getSymbol();
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
