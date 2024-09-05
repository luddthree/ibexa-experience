<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class AttributeGroupCreateStruct extends ValueObject
{
    private string $identifier;

    /** @var array<string,string> */
    private array $names;

    private int $position;

    /**
     * @param array<string,string> $names
     */
    public function __construct(string $identifier, array $names = [], int $position = 0)
    {
        parent::__construct();

        $this->identifier = $identifier;
        $this->position = $position;
        $this->names = $names;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return array<string,string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param array<string,string> $names
     */
    public function setNames(array $names): void
    {
        $this->names = $names;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
