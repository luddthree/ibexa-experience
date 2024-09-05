<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Value;

use Ibexa\Contracts\Seo\Value\ValueInterface;
use JsonSerializable;
use Webmozart\Assert\Assert;

final class SeoTypesValue implements ValueInterface, JsonSerializable
{
    /** @var SeoTypeValue[] */
    private array $types = [];

    /**
     * @param array<string|int, SeoTypeValue> $types
     */
    public function __construct(array $types = [])
    {
        foreach ($types as $typeName => $type) {
            $typeNameKey = is_string($typeName) ? $typeName : $type->getType();
            Assert::string($typeNameKey);
            Assert::isInstanceOf($type, SeoTypeValue::class);
            $this->types[$typeNameKey] = $type;
        }
    }

    public function setType(string $typeName, SeoTypeValue $type): self
    {
        $this->types[$typeName] = $type;

        return $this;
    }

    /**
     * @param \Ibexa\Seo\Value\SeoTypeValue[] $types
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }

    /**
     * @return \Ibexa\Seo\Value\SeoTypeValue[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function getSeoTypesValues(): array
    {
        return $this->getTypes();
    }

    public function __toString(): string
    {
        return '';
    }

    /**
     * @return \Ibexa\Seo\Value\SeoTypeValue[]
     */
    public function jsonSerialize(): array
    {
        return $this->types;
    }
}
