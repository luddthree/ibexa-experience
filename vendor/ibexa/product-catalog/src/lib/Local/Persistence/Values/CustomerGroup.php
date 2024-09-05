<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class CustomerGroup extends ValueObject
{
    private const ZERO_GLOBAL_PRICE_RATE = '0.0';

    public ?int $id;

    public string $identifier;

    /** @var numeric-string */
    public string $globalPriceRate;

    /**
     * Human-readable name of the attribute definition.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<name_eng>', 4 => '<name_de>'];
     * </code>
     *
     * @var array<int,string>
     */
    private array $names = [];

    /**
     * Human-readable description of the attribute definition.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<description_eng>', 4 => '<description_de>'];
     * </code>
     *
     * @var array<int,?string>
     */
    private array $descriptions = [];

    /**
     * @return array<int,string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function setName(int $languageId, string $name): void
    {
        $this->names[$languageId] = $name;
    }

    public function hasName(int $languageId): bool
    {
        return array_key_exists($languageId, $this->names);
    }

    public function getName(int $languageId): string
    {
        return $this->names[$languageId];
    }

    /**
     * @return array<int,?string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function setDescription(int $languageId, ?string $name): void
    {
        $this->descriptions[$languageId] = $name;
    }

    public function hasDescription(int $languageId): bool
    {
        return array_key_exists($languageId, $this->descriptions);
    }

    public function getDescription(int $languageId): ?string
    {
        return $this->descriptions[$languageId];
    }

    public function hasGlobalPriceRate(): bool
    {
        return $this->globalPriceRate !== self::ZERO_GLOBAL_PRICE_RATE;
    }
}
