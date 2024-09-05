<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value representing product specification.
 */
final class Value extends BaseValue
{
    private ?int $id;

    private ?string $code;

    private ?string $originalCode;

    /** @var array<int,mixed> */
    private array $attributes;

    private bool $isVirtual;

    /**
     * @param array<int,mixed> $attributes
     */
    public function __construct(
        ?int $id = null,
        ?string $code = null,
        array $attributes = [],
        bool $isVirtual = false
    ) {
        parent::__construct();

        $this->id = $id;
        $this->code = $code;
        $this->originalCode = $code;
        $this->attributes = $attributes;
        $this->isVirtual = $isVirtual;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getOriginalCode(): ?string
    {
        return $this->originalCode;
    }

    /**
     * @internal
     */
    public function isCodeChanged(): bool
    {
        return $this->originalCode === null || $this->originalCode !== $this->code;
    }

    /**
     * @return array<int,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param int $attributeDefinitionId
     * @param mixed $value
     */
    public function setAttribute(int $attributeDefinitionId, $value): void
    {
        $this->attributes[$attributeDefinitionId] = $value;
    }

    /**
     * @param array<int,mixed> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    public function setVirtual(bool $isVirtual): void
    {
        $this->isVirtual = $isVirtual;
    }

    public function __toString(): string
    {
        return (string)$this->code;
    }
}
