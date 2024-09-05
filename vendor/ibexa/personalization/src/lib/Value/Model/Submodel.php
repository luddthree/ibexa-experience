<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

class Submodel
{
    public const TYPE_NUMERIC = 'NUMERIC';
    public const TYPE_NOMINAL = 'NOMINAL';

    /** @var string */
    protected $attributeKey;

    /** @var string */
    protected $attributeSource;

    /** @var string|null */
    protected $source;

    /** @var array */
    protected $attributeValues;

    /** @var string */
    protected $type;

    public function getAttributeKey(): string
    {
        return $this->attributeKey;
    }

    public function getAttributeSource(): string
    {
        return $this->attributeSource;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getAttributeValues(): ?array
    {
        return $this->attributeValues;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function __construct(
        string $attributeKey,
        string $attributeSource,
        string $type,
        ?string $source = null,
        ?array $attributeValues = null
    ) {
        $this->attributeKey = $attributeKey;
        $this->attributeSource = $attributeSource;
        $this->source = $source;
        $this->attributeValues = $attributeValues;
        $this->type = $type;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['attributeKey'],
            $properties['attributeSource'],
            $properties['submodelType'],
            $properties['source'] ?? null,
            self::processAttributeValues(
                $properties['submodelType'],
                $properties['attributeValues'] ?? $properties['intervals'] ?? null
            )
        );
    }

    protected static function processAttributeValues(
        string $attributeType,
        ?array $attributeValues = null
    ): array {
        $attributeValues = $attributeValues ?? [];

        if ($attributeType === Attribute::TYPE_NOMINAL) {
            $groupedAttributeValues = [];
            foreach ($attributeValues as $attributeValue) {
                $groupedAttributeValues[$attributeValue['group']][] = $attributeValue['attributeValue'];
            }

            return $groupedAttributeValues;
        }

        return $attributeValues;
    }
}

class_alias(Submodel::class, 'Ibexa\Platform\Personalization\Value\Model\Submodel');
