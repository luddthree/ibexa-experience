<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Value;

use JsonSerializable;
use Webmozart\Assert\Assert;

final class SeoTypeValue implements JsonSerializable
{
    private ?string $type;

    /** @var array<string, string> */
    private array $fields;

    /**
     * @param array<string, string> $fields
     */
    public function __construct(?string $type = null, array $fields = [])
    {
        foreach ($fields as $field => $value) {
            Assert::string($field);
            Assert::nullOrStringNotEmpty($value);
        }

        $this->type = $type;
        $this->fields = $fields;
    }

    /**
     * @param array<string, string> $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return array<string, string>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getField(string $fieldName): ?string
    {
        return $this->fields[$fieldName] ?? null;
    }

    /**
     * @return array{
     *     type: string|null,
     *     fields: array<string, string|null>,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'fields' => $this->fields,
        ];
    }
}
