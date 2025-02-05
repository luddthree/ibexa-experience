<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class CustomerGroupCreateStruct extends ValueObject
{
    private string $identifier;

    /** @var array<int, string> */
    private array $names;

    /** @var array<int, string> */
    private array $descriptions;

    /** @var numeric-string */
    private string $globalPriceRate;

    /**
     * @param array<int, string> $names
     * @param array<int, string> $descriptions
     * @param numeric-string $globalPriceRate
     */
    public function __construct(string $identifier, array $names, array $descriptions, string $globalPriceRate)
    {
        parent::__construct();

        $this->identifier = $identifier;
        $this->names = $names;
        $this->descriptions = $descriptions;
        $this->globalPriceRate = $globalPriceRate;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return array<int, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @return array<int, string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * @return numeric-string
     */
    public function getGlobalPriceRate(): string
    {
        return $this->globalPriceRate;
    }
}
