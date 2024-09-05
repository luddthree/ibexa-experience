<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeUpdateStep implements StepInterface
{
    private CriterionInterface $criterion;

    private ?string $identifier;

    private ?string $attributeGroupIdentifier;

    private ?int $position;

    /** @var array<string, string> */
    private array $names;

    /** @var array<string, string|null> */
    private array $descriptions;

    /** @var array<mixed>|null */
    private ?array $options;

    /**
     * @param array<string, string> $names
     * @param array<string, string|null> $descriptions
     * @param array<mixed>|null $options
     */
    public function __construct(
        CriterionInterface $criterion,
        ?string $identifier = null,
        ?string $attributeGroupIdentifier = null,
        ?int $position = null,
        array $names = [],
        array $descriptions = [],
        ?array $options = null
    ) {
        $this->criterion = $criterion;
        $this->identifier = $identifier;
        $this->attributeGroupIdentifier = $attributeGroupIdentifier;
        $this->position = $position;
        $this->names = $names;
        $this->descriptions = $descriptions;
        $this->options = $options;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getAttributeGroupIdentifier(): ?string
    {
        return $this->attributeGroupIdentifier;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @return array<string, string|null>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * @return array<mixed>|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }
}
