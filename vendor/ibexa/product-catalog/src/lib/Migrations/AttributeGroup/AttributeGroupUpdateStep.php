<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeGroupUpdateStep implements StepInterface
{
    private CriterionInterface $criterion;

    private ?string $identifier;

    /** @var array<string, string> */
    private array $names;

    private ?int $position;

    /**
     * @param array<string, string> $names
     */
    public function __construct(CriterionInterface $criterion, ?string $identifier, ?array $names, ?int $position)
    {
        $this->criterion = $criterion;
        $this->identifier = $identifier;
        $this->names = $names ?? [];
        $this->position = $position;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
}
