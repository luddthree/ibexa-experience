<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CustomerGroupCreateStep implements StepInterface
{
    private string $identifier;

    /**
     * @var array<string, string>
     */
    private array $names = [];

    /**
     * @var array<string, string>
     */
    private array $descriptions = [];

    /** @var numeric-string */
    private string $globalPriceRate;

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     * @param numeric-string $globalPriceRate
     */
    public function __construct(string $identifier, array $names, array $descriptions, string $globalPriceRate)
    {
        $this->identifier = $identifier;
        foreach ($names as $languageCode => $name) {
            $this->addName($languageCode, $name);
        }
        foreach ($descriptions as $languageCode => $description) {
            $this->addDescription($languageCode, $description);
        }
        $this->globalPriceRate = $globalPriceRate;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function addName(string $languageCode, string $name): void
    {
        $this->names[$languageCode] = $name;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function addDescription(string $languageCode, string $description): void
    {
        $this->descriptions[$languageCode] = $description;
    }

    /**
     * @return array<string, string>
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
