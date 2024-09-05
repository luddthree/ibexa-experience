<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeGroupCreateStep implements StepInterface
{
    private string $identifier;

    /** @var array<string, string> */
    private array $names;

    private int $position;

    /**
     * @param array<string, string> $names
     */
    public function __construct(string $identifier, array $names, int $position)
    {
        $this->identifier = $identifier;
        $this->names = $names;
        $this->position = $position;
    }

    public function getIdentifier(): string
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

    public function getPosition(): int
    {
        return $this->position;
    }
}
