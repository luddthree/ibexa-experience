<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CustomerGroupUpdateStep implements StepInterface
{
    private CriterionInterface $criterion;

    private ?string $identifier;

    /** @var array<string, string> */
    private array $names = [];

    /** @var array<string, string> */
    private array $descriptions = [];

    /** @var numeric-string|null */
    private ?string $globalPriceRate;

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     * @param numeric-string|null $globalPriceRate
     */
    public function __construct(
        CriterionInterface $criterion,
        ?string $identifier,
        array $names,
        array $descriptions,
        ?string $globalPriceRate
    ) {
        $this->criterion = $criterion;
        $this->identifier = $identifier;
        foreach ($names as $languageCode => $name) {
            $this->addName($languageCode, $name);
        }
        foreach ($descriptions as $languageCode => $description) {
            $this->addDescription($languageCode, $description);
        }
        $this->globalPriceRate = $globalPriceRate;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }

    public function getIdentifier(): ?string
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
     * @return numeric-string|null
     */
    public function getGlobalPriceRate(): ?string
    {
        return $this->globalPriceRate;
    }
}
