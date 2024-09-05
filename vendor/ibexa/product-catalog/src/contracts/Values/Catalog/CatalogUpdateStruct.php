<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Catalog;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class CatalogUpdateStruct extends ValueObject
{
    private int $id;

    private ?string $identifier;

    private ?string $transition;

    private string $status;

    private ?CriterionInterface $criterion;

    /**
     * Human-readable name of the catalog.
     *
     * The structure of this field is:
     *
     * <code>
     * ['eng-GB' => '<name_eng>', 'ger-DE' => '<name_de>'];
     * </code>
     *
     * @var array<string, string>
     */
    private array $names = [];

    /**
     * Human-readable description of the catalog.
     *
     * The structure of this field is:
     *
     * <code>
     * ['eng-GB' => '<description_eng>', 'ger-DE' => '<description_de>'];
     * </code>
     *
     * @var array<string, string>
     */
    private array $descriptions = [];

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     */
    public function __construct(
        int $id,
        ?string $transition = null,
        ?string $identifier = null,
        ?CriterionInterface $criterion = null,
        array $names = [],
        array $descriptions = []
    ) {
        parent::__construct();

        $this->id = $id;
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

    public function getId(): int
    {
        return $this->id;
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

    /**
     * @return string|null
     */
    public function getTransition(): ?string
    {
        return $this->transition;
    }

    /**
     * @param string|null $transition
     */
    public function setTransition(?string $transition): void
    {
        $this->transition = $transition;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCriterion(): ?CriterionInterface
    {
        return $this->criterion;
    }

    public function setCriterion(?CriterionInterface $criterion): void
    {
        $this->criterion = $criterion;
    }
}
