<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Details;

use DateTimeImmutable;
use Ibexa\Personalization\Value\AbstractEventDetail;
use JsonSerializable;

final class Event extends AbstractEventDetail implements JsonSerializable
{
    public const CLICK = 'click';
    public const CLICKED_RECOMMENDED = 'clicked_recommended';
    public const PURCHASE = 'purchase';
    public const PURCHASED_RECOMMENDED = 'purchased_recommended';
    public const CONSUME = 'consume';
    public const RATE = 'rate';
    public const RENDERED = 'rendered';
    public const BLACKLIST = 'blacklist';
    public const BASKET = 'basket';
    public const TIMESPAN_BEGIN = 'timespan_begin';
    public const TIMESPAN_DURATION = 'timespan_duration';

    private int $click;

    private int $clickedRecommended;

    private int $purchase;

    private int $purchasedRecommended;

    private int $consume;

    private int $rate;

    private int $rendered;

    private int $blacklist;

    private int $basket;

    public function __construct(
        int $click,
        int $clickedRecommended,
        int $purchase,
        int $purchasedRecommended,
        int $consume,
        int $rate,
        int $rendered,
        int $blacklist,
        int $basket,
        DateTimeImmutable $timespanBegin,
        string $timespanDuration
    ) {
        $this->click = $click;
        $this->clickedRecommended = $clickedRecommended;
        $this->purchase = $purchase;
        $this->purchasedRecommended = $purchasedRecommended;
        $this->consume = $consume;
        $this->rate = $rate;
        $this->rendered = $rendered;
        $this->blacklist = $blacklist;
        $this->basket = $basket;

        parent::__construct($timespanBegin, $timespanDuration);
    }

    public function getClick(): int
    {
        return $this->click;
    }

    public function getClickedRecommended(): int
    {
        return $this->clickedRecommended;
    }

    public function getPurchase(): int
    {
        return $this->purchase;
    }

    public function getPurchasedRecommended(): int
    {
        return $this->purchasedRecommended;
    }

    public function getConsume(): int
    {
        return $this->consume;
    }

    public function getRate(): int
    {
        return $this->rate;
    }

    public function getRendered(): int
    {
        return $this->rendered;
    }

    public function getBlacklist(): int
    {
        return $this->blacklist;
    }

    public function getBasket(): int
    {
        return $this->basket;
    }

    public function getTimespanDuration(): string
    {
        return parent::getTimespanDuration();
    }

    public function getTimespanBegin(): DateTimeImmutable
    {
        return parent::getTimespanBegin();
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (int)$properties[self::CLICK],
            (int)$properties[self::CLICKED_RECOMMENDED],
            (int)$properties[self::PURCHASE],
            (int)$properties[self::PURCHASED_RECOMMENDED],
            (int)$properties[self::CONSUME],
            (int)$properties[self::RATE],
            (int)$properties[self::RENDERED],
            (int)$properties[self::BLACKLIST],
            (int)$properties[self::BASKET],
            new DateTimeImmutable($properties[self::TIMESPAN_BEGIN]),
            $properties[self::TIMESPAN_DURATION],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'click' => $this->getClick(),
            'clickedRecommended' => $this->getClickedRecommended(),
            'purchase' => $this->getPurchase(),
            'purchasedRecommended' => $this->getPurchasedRecommended(),
            'consume' => $this->getConsume(),
            'rate' => $this->getRate(),
            'rendered' => $this->getRendered(),
            'blacklist' => $this->getBlacklist(),
            'basket' => $this->getBasket(),
            'timespanBegin' => $this->getTimespanBegin(),
            'timespanDuration' => $this->getTimespanDuration(),
        ];
    }
}

class_alias(Event::class, 'Ibexa\Platform\Personalization\Value\Performance\Details\Event');
