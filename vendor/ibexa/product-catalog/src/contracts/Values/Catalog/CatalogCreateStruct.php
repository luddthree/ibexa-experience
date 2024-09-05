<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Catalog;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Status;

final class CatalogCreateStruct extends ValueObject
{
    private string $identifier;

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
     * ['eng-GB' => '<description_eng>', 4 => '<description_de>'];
     * </code>
     *
     * @var array<string, string>
     */
    private array $descriptions = [];

    private ?int $creatorId;

    private string $status;

    private CriterionInterface $criterion;

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     */
    public function __construct(
        string $identifier,
        CriterionInterface $criterion,
        array $names,
        array $descriptions,
        string $status = Status::DRAFT_PLACE,
        ?int $creatorId = null
    ) {
        parent::__construct();
        $this->identifier = $identifier;
        foreach ($names as $languageId => $name) {
            $this->addName($languageId, $name);
        }
        foreach ($descriptions as $languageId => $description) {
            $this->addDescription($languageId, $description);
        }
        $this->status = $status;
        $this->creatorId = $creatorId;
        $this->criterion = $criterion instanceof LogicalAnd ? $criterion : new LogicalAnd([$criterion]);
    }

    public function getIdentifier(): string
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

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }

    public function setCriterion(CriterionInterface $criterion): void
    {
        $this->criterion = $criterion;
    }
}
