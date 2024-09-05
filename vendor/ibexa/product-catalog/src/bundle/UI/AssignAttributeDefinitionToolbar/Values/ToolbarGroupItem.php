<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values;

final class ToolbarGroupItem
{
    private string $identifier;

    private string $name;

    private string $type;

    private bool $assigned;

    public function __construct(string $identifier, string $name, string $type, bool $assigned = false)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->type = $type;
        $this->assigned = $assigned;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isAssigned(): bool
    {
        return $this->assigned;
    }
}
