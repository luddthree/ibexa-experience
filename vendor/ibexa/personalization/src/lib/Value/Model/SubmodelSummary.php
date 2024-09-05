<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

final class SubmodelSummary
{
    /** @var string */
    private $attributeKey;

    /** @var string */
    private $attributeSource;

    /** @var string */
    private $attributeType;

    /** @var int */
    private $groupCount;

    /** @var string|null */
    private $source;

    public function __construct(
        string $attributeKey,
        string $attributeSource,
        string $attributeType,
        int $groupCount,
        ?string $source
    ) {
        $this->attributeKey = $attributeKey;
        $this->attributeSource = $attributeSource;
        $this->attributeType = $attributeType;
        $this->groupCount = $groupCount;
        $this->source = $source;
    }

    public function getAttributeKey(): string
    {
        return $this->attributeKey;
    }

    public function getAttributeSource(): string
    {
        return $this->attributeSource;
    }

    public function getAttributeType(): string
    {
        return $this->attributeType;
    }

    public function getGroupCount(): int
    {
        return $this->groupCount;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['attributeKey'],
            $properties['attributeSource'],
            $properties['attributeType'],
            $properties['groupCount'],
            $properties['source'] ?? null
        );
    }
}

class_alias(SubmodelSummary::class, 'Ibexa\Platform\Personalization\Value\Model\SubmodelSummary');
