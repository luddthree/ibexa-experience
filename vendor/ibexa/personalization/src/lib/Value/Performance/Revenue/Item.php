<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Revenue;

use JsonSerializable;

final class Item implements JsonSerializable
{
    private int $id;

    private int $type;

    private ?string $title;

    private function __construct(
        int $id,
        int $type,
        ?string $title = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'title' => $this->getTitle(),
        ];
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (int)$properties['id'],
            (int)$properties['type'],
            $properties['title'],
        );
    }
}

class_alias(Item::class, 'Ibexa\Platform\Personalization\Value\Performance\Revenue\Item');
