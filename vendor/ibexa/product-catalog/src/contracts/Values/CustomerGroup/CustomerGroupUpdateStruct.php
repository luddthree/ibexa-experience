<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\CustomerGroup;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class CustomerGroupUpdateStruct extends ValueObject
{
    private int $id;

    private ?string $identifier;

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
    private array $names = [];

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
    private array $descriptions = [];

    /** @var numeric-string|null */
    private ?string $globalPriceRate;

    /**
     * @param array<int, string> $names
     * @param array<int, string> $descriptions
     * @param numeric-string|null $globalPriceRate
     */
    public function __construct(
        int $id,
        ?string $identifier = null,
        array $names = [],
        array $descriptions = [],
        ?string $globalPriceRate = null
    ) {
        parent::__construct();

        $this->id = $id;
        $this->identifier = $identifier;
        foreach ($names as $languageId => $name) {
            $this->addName($languageId, $name);
        }
        foreach ($descriptions as $languageId => $description) {
            $this->addDescription($languageId, $description);
        }
        $this->globalPriceRate = $globalPriceRate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
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
     * @return numeric-string|null
     */
    public function getGlobalPriceRate(): ?string
    {
        return $this->globalPriceRate;
    }
}
