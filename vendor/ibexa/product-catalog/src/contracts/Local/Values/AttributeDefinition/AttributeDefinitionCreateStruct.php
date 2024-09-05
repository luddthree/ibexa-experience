<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;

final class AttributeDefinitionCreateStruct extends ValueObject
{
    private string $identifier;

    private AttributeTypeInterface $type;

    private AttributeGroupInterface $group;

    /** @var array<string, string> */
    private array $names;

    /** @var array<string, string> */
    private array $descriptions;

    private int $position;

    /**
     * @var array<string,mixed>
     */
    private array $options;

    public function __construct(string $identifier)
    {
        parent::__construct();

        $this->identifier = $identifier;
        $this->names = [];
        $this->descriptions = [];
        $this->position = 0;
        $this->options = [];
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getGroup(): AttributeGroupInterface
    {
        return $this->group;
    }

    public function setGroup(AttributeGroupInterface $group): void
    {
        $this->group = $group;
    }

    public function getType(): AttributeTypeInterface
    {
        return $this->type;
    }

    public function setType(AttributeTypeInterface $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    public function setName(string $languageCode, string $name): void
    {
        $this->names[$languageCode] = $name;
    }

    /**
     * @return array<string, string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function setDescription(string $languageCode, string $description): void
    {
        $this->descriptions[$languageCode] = $description;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array<string,mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string,mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
