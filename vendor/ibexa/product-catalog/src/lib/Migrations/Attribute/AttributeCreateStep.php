<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeCreateStep implements StepInterface
{
    private string $identifier;

    private string $attributeTypeIdentifier;

    private string $attributeGroupIdentifier;

    private int $position;

    /** @var array<string, string> */
    private array $names;

    /** @var array<string, string|null> */
    private array $descriptions;

    /** @var array<mixed> */
    private array $options;

    /**
     * @param array<string, string> $names
     * @param array<string, string|null> $descriptions
     * @param array<mixed> $options
     */
    public function __construct(
        string $identifier,
        string $attributeGroupIdentifier,
        string $attributeTypeIdentifier,
        int $position,
        array $names,
        array $descriptions,
        array $options
    ) {
        $this->identifier = $identifier;
        $this->attributeTypeIdentifier = $attributeTypeIdentifier;
        $this->attributeGroupIdentifier = $attributeGroupIdentifier;
        $this->position = $position;
        $this->names = $names;
        $this->descriptions = $descriptions;
        $this->options = $options;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAttributeTypeIdentifier(): string
    {
        return $this->attributeTypeIdentifier;
    }

    public function getAttributeGroupIdentifier(): string
    {
        return $this->attributeGroupIdentifier;
    }

    public function getPosition(): int
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
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
