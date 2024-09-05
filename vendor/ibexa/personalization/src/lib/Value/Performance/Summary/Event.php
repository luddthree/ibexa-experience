<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use Ibexa\Personalization\Value\Performance\Details\Event as EventDetails;
use JsonSerializable;

final class Event implements JsonSerializable
{
    public const TOTAL = 'total_events';
    public const EVENT_LABELS = [
        EventDetails::CLICK => 'Clicked',
        EventDetails::CLICKED_RECOMMENDED => 'Clicked recommended',
        EventDetails::PURCHASE => 'Purchase',
        EventDetails::PURCHASED_RECOMMENDED => 'Purchased recommended',
        EventDetails::CONSUME => 'Consume',
        EventDetails::RATE => 'Rate',
        EventDetails::RENDERED => 'Rendered',
        EventDetails::BLACKLIST => 'Blacklist',
        EventDetails::BASKET => 'Basket',
    ];

    private string $name;

    private int $hits;

    public function __construct(
        string $name,
        int $hits
    ) {
        $this->name = $name;
        $this->hits = $hits;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @phpstan-param array{
     *  'name': string,
     *  'hits': int,
     * } $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['name'],
            $properties['hits'],
        );
    }

    /**
     * @phpstan-return array{
     *  'name': string,
     *  'hits': int,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'hits' => $this->getHits(),
        ];
    }
}

class_alias(Event::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\Event');
