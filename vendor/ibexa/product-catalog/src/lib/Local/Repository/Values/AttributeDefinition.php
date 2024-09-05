<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

final class AttributeDefinition extends ValueObject implements AttributeDefinitionInterface, TranslatableInterface
{
    protected int $id;

    protected string $name;

    protected ?string $description;

    protected string $identifier;

    protected AttributeTypeInterface $type;

    protected AttributeGroupInterface $group;

    protected int $position;

    protected AttributeDefinitionOptions $options;

    /** @var array<int,string> */
    protected array $languages;

    /** @var array<string, string> */
    private array $names;

    /** @var array<string, string|null> */
    private array $descriptions;

    /**
     * @param array<int,string> $languages
     * @param array<string, string> $names
     * @param array<string, string|null> $descriptions
     * @param array<string,mixed> $options
     */
    public function __construct(
        int $id,
        string $identifier,
        AttributeTypeInterface $type,
        AttributeGroupInterface $group,
        string $name,
        int $position,
        array $languages,
        ?string $description,
        array $names,
        array $descriptions,
        array $options = []
    ) {
        parent::__construct();

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->identifier = $identifier;
        $this->type = $type;
        $this->group = $group;
        $this->position = $position;
        $this->languages = $languages;
        $this->names = $names;
        $this->descriptions = $descriptions;
        $this->options = new AttributeDefinitionOptions($options);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(string $languageCode = null): string
    {
        if ($languageCode === null) {
            return $this->name;
        }

        return $this->names[$languageCode] ?? $this->name;
    }

    public function getDescription(string $languageCode = null): ?string
    {
        if ($languageCode === null) {
            return $this->description;
        }

        return $this->descriptions[$languageCode] ?? $this->description;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getType(): AttributeTypeInterface
    {
        return $this->type;
    }

    public function getGroup(): AttributeGroupInterface
    {
        return $this->group;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getOptions(): OptionsBag
    {
        return $this->options;
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }
}
