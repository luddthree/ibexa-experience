<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class CatalogUpdateStruct extends ValueObject
{
    private ?string $identifier;

    private ?string $transition;

    private ?CriterionInterface $criterion;

    /** @var array<string, string> */
    private array $names = [];

    /** @var array<string, string> */
    private array $descriptions = [];

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     */
    public function __construct(
        ?string $transition = null,
        ?string $identifier = null,
        ?CriterionInterface $criterion = null,
        array $names = [],
        array $descriptions = []
    ) {
        parent::__construct();

        $this->transition = $transition;
        $this->identifier = $identifier;
        $this->criterion = $criterion;
        foreach ($names as $languageCode => $name) {
            $this->addName($languageCode, $name);
        }
        foreach ($descriptions as $languageCode => $description) {
            $this->addDescription($languageCode, $description);
        }
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getName(string $languageCode): string
    {
        return $this->names[$languageCode];
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

    public function getDescription(string $languageCode): string
    {
        return $this->descriptions[$languageCode] ?? '';
    }

    /**
     * @return array<string, string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function getTransition(): ?string
    {
        return $this->transition;
    }

    public function getCriterion(): ?CriterionInterface
    {
        return $this->criterion;
    }
}
