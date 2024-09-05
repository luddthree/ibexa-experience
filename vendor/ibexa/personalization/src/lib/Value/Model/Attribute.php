<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

final class Attribute
{
    public const TYPE_NOMINAL = 'NOMINAL';
    public const TYPE_NUMERIC = 'NUMERIC';

    /** @var string */
    private $key;

    /** @var string */
    private $type;

    /** @var array */
    private $values;

    /** @var string */
    private $source;

    /** @var string */
    private $attributeSource;

    public function __construct(
        ?string $key = null,
        ?string $type = null,
        ?array $values = null,
        ?string $source = null,
        ?string $attributeSource = null
    ) {
        $this->key = $key;
        $this->type = $type;
        $this->values = $values;
        $this->source = $source;
        $this->attributeSource = $attributeSource;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getValues(): ?array
    {
        return $this->values;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getAttributeSource(): ?string
    {
        return $this->attributeSource;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['key'] ?? null,
            $properties['type'] ?? null,
            $properties['values'] ?? null,
            $properties['source'] ?? null,
            $properties['attributeSource'] ?? null
        );
    }
}

class_alias(Attribute::class, 'Ibexa\Platform\Personalization\Value\Model\Attribute');
