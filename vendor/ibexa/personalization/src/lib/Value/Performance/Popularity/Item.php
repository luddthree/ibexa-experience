<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Popularity;

final class Item
{
    private int $id;

    private int $type;

    private string $typeName;

    private ?string $title;

    public function __construct(
        int $id,
        int $type,
        string $typeName,
        ?string $title
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->typeName = $typeName;
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

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @phpstan-param array{
     *  'id': int,
     *  'type': int,
     *  'typeName': string,
     *  'title': ?string
     * } $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            (int)$properties['id'],
            (int)$properties['type'],
            $properties['typeName'],
            $properties['title'] ?? null,
        );
    }
}
