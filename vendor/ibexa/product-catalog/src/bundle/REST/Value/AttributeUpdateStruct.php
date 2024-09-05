<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class AttributeUpdateStruct extends ValueObject
{
    private ?string $identifier;

    /** @var array<string, string> */
    private array $names;

    /** @var array<string, string> */
    private array $descriptions;

    private ?int $position;

    /**
     * @var array<string,mixed>
     */
    private array $options;

    /**
     * @param array<string, string> $names
     * @param array<string, string> $descriptions
     * @param array<string, mixed> $options
     */
    public function __construct(
        ?string $identifier,
        array $names,
        array $descriptions,
        ?int $position,
        array $options = []
    ) {
        parent::__construct();

        $this->identifier = $identifier;
        $this->names = $names;
        $this->descriptions = $descriptions;
        $this->position = $position;
        $this->options = $options;
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

    /**
     * @return array<string, string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
