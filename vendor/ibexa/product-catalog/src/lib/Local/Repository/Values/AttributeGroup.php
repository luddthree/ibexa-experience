<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

final class AttributeGroup extends ValueObject implements AttributeGroupInterface, TranslatableInterface
{
    protected int $id;

    protected string $identifier;

    protected string $name;

    protected int $position;

    /** @var array<int,string> */
    protected array $languages;

    /** @var array<string, string> */
    private array $names;

    /**
     * @param array<int,string> $languages
     * @param array<string, string> $names
     */
    public function __construct(
        int $id,
        string $identifier,
        string $name,
        int $position,
        array $languages,
        array $names
    ) {
        parent::__construct();

        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->position = $position;
        $this->languages = $languages;
        $this->names = $names;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(string $languageCode = null): string
    {
        if ($languageCode === null) {
            return $this->name;
        }

        return $this->names[$languageCode] ?? $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }
}
