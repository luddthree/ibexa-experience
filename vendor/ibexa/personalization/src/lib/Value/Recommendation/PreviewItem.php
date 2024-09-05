<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Recommendation;

use JsonSerializable;

final class PreviewItem implements JsonSerializable
{
    /** @var string|null */
    private $title;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $image;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
        ];
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['title'] ?? null,
            $properties['description'] ?? null,
            $properties['image'] ?? null,
        );
    }
}

class_alias(PreviewItem::class, 'Ibexa\Platform\Personalization\Value\Recommendation\PreviewItem');
