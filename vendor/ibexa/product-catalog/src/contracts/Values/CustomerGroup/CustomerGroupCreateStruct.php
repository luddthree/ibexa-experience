<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\CustomerGroup;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class CustomerGroupCreateStruct extends ValueObject
{
    private string $identifier;

    /** @var numeric-string */
    private string $globalPriceRate;

    /**
     * Human-readable name of the customer group.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<name_eng>', 4 => '<name_de>'];
     * </code>
     *
     * @var array<int, string>
     */
    public array $names = [];

    /**
     * Human-readable description of the customer group.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<description_eng>', 4 => '<description_de>'];
     * </code>
     *
     * @var array<int, string>
     */
    public array $descriptions = [];

    /**
     * @param array<int, string> $names
     * @param array<int, string> $descriptions
     * @param numeric-string $globalPriceRate
     */
    public function __construct(string $identifier, array $names, array $descriptions, string $globalPriceRate = '100.0')
    {
        parent::__construct();
        $this->identifier = $identifier;
        foreach ($names as $languageId => $name) {
            $this->addName($languageId, $name);
        }
        foreach ($descriptions as $languageId => $description) {
            $this->addDescription($languageId, $description);
        }
        $this->globalPriceRate = $globalPriceRate;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(int $languageId): string
    {
        return $this->names[$languageId];
    }

    public function addName(int $languageId, string $name): void
    {
        $this->names[$languageId] = $name;
    }

    /**
     * @return array<int, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function addDescription(int $languageId, string $description): void
    {
        $this->descriptions[$languageId] = $description;
    }

    public function getDescription(int $languageId): string
    {
        return $this->descriptions[$languageId] ?? '';
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
