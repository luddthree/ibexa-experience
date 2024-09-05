<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Revenue;

use DateTimeImmutable;
use JsonSerializable;

final class RevenueDetails implements JsonSerializable
{
    /** @var \DateTimeImmutable */
    private $timeRecommended;

    /** @var \DateTimeImmutable */
    private $timeConsumed;

    /** @var \Ibexa\Personalization\Value\Performance\Revenue\Item */
    private $item;

    /** @var float|null */
    private $price;

    /** @var int|null */
    private $quantity;

    /** @var string|null */
    private $currency;

    private function __construct(
        DateTimeImmutable $timeRecommended,
        DateTimeImmutable $timeConsumed,
        Item $item,
        ?float $price = null,
        ?int $quantity = null,
        ?string $currency = null
    ) {
        $this->timeRecommended = $timeRecommended;
        $this->timeConsumed = $timeConsumed;
        $this->item = $item;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->currency = $currency;
    }

    public function getTimeRecommended(): DateTimeImmutable
    {
        return $this->timeRecommended;
    }

    public function getTimeConsumed(): DateTimeImmutable
    {
        return $this->timeConsumed;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function jsonSerialize(): array
    {
        return [
            'timeRecommended' => $this->getTimeRecommended(),
            'timeConsumed' => $this->getTimeConsumed(),
            'item' => $this->getItem(),
            'price' => $this->getPrice(),
            'quantity' => $this->getQuantity(),
            'currency' => $this->getCurrency(),
        ];
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            new DateTimeImmutable($properties['timeRecommended']),
            new DateTimeImmutable($properties['timeConsumed']),
            Item::fromArray($properties['item']),
            isset($properties['price']) ? (float)$properties['price'] : null,
            isset($properties['quantity']) ? (int)$properties['quantity'] : null,
            isset($properties['currency']) ? $properties['currency'] : null,
        );
    }
}

class_alias(RevenueDetails::class, 'Ibexa\Platform\Personalization\Value\Performance\Revenue\RevenueDetails');
