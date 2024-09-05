<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class AttributeDefinitionUpdateStruct extends ValueObject
{
    private ?string $identifier = null;

    private ?AttributeGroupInterface $group = null;

    /** @var array<string,string> */
    private array $names = [];

    /** @var array<string,string> */
    private array $descriptions = [];

    private ?int $position = null;

    /**
     * @var array<string,mixed>|null
     */
    private ?array $options = null;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getGroup(): ?AttributeGroupInterface
    {
        return $this->group;
    }

    public function setGroup(?AttributeGroupInterface $group): void
    {
        $this->group = $group;
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

    public function setName(string $languageCode, string $name): void
    {
        $this->names[$languageCode] = $name;
    }

    /**
     * @return array<string,string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * @param array<string,string> $descriptions
     */
    public function setDescriptions(array $descriptions): void
    {
        $this->descriptions = $descriptions;
    }

    public function setDescription(string $languageCode, string $description): void
    {
        $this->descriptions[$languageCode] = $description;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array<string,mixed>|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param array<string,mixed>|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }
}
