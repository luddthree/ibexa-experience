<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Popularity;

final class Popularity
{
    private Item $item;

    private int $rating;

    private ?int $renderRatings;

    public function __construct(
        Item $item,
        int $rating,
        ?int $renderRatings
    ) {
        $this->item = $item;
        $this->rating = $rating;
        $this->renderRatings = $renderRatings;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getRenderRatings(): ?int
    {
        return $this->renderRatings;
    }

    /**
     * @phpstan-param array{
     *  'item': array{
     *      'id': int,
     *      'type': int,
     *      'typeName': string,
     *      'title': ?string
     *  },
     *  'rating': int,
     *  'renderRatings': ?int,
     * } $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            Item::fromArray($properties['item']),
            (int)$properties['rating'],
            isset($properties['renderRatings']) ? (int)$properties['renderRatings'] : null,
        );
    }
}
