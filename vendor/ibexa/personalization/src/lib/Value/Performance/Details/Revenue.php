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

final class Revenue extends AbstractEventDetail implements JsonSerializable
{
    private float $revenue;

    private int $purchasedRecommended;

    private function __construct(
        float $revenue,
        int $purchasedRecommended,
        DateTimeImmutable $timespanBegin,
        string $timespanDuration
    ) {
        parent::__construct($timespanBegin, $timespanDuration);

        $this->revenue = $revenue;
        $this->purchasedRecommended = $purchasedRecommended;
    }

    public function getRevenue(): float
    {
        return $this->revenue;
    }

    public function getPurchasedRecommended(): int
    {
        return $this->purchasedRecommended;
    }

    public function getTimespanBegin(): DateTimeImmutable
    {
        return parent::getTimespanBegin();
    }

    public function getTimespanDuration(): string
    {
        return parent::getTimespanDuration();
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (float)$properties['revenue'],
            (int)$properties['purchased_recommended'],
            new DateTimeImmutable($properties['timespan_begin']),
            $properties['timespan_duration'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'revenue' => $this->getRevenue(),
            'purchasedRecommended' => $this->getPurchasedRecommended(),
            'timespanBegin' => $this->getTimespanBegin(),
            'timespanDuration' => $this->getTimespanDuration(),
        ];
    }
}

class_alias(Revenue::class, 'Ibexa\Platform\Personalization\Value\Performance\Details\Revenue');
