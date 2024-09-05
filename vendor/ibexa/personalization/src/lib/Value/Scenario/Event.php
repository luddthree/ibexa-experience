<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use DateTimeImmutable;
use Ibexa\Personalization\Value\AbstractEventDetail;
use JsonSerializable;

final class Event extends AbstractEventDetail implements JsonSerializable
{
    /** @var int */
    private $scenarioCalls;

    /** @var int */
    private $deliveredRecommendations;

    /** @var float|null */
    private $conversionRatePercent;

    private function __construct(
        int $scenarioCalls,
        int $deliveredRecommendations,
        DateTimeImmutable $timespanBegin,
        string $timespanDuration,
        ?float $conversionRatePercent = null
    ) {
        parent::__construct($timespanBegin, $timespanDuration);

        $this->scenarioCalls = $scenarioCalls;
        $this->deliveredRecommendations = $deliveredRecommendations;
        $this->conversionRatePercent = $conversionRatePercent;
    }

    public function getScenarioCalls(): int
    {
        return $this->scenarioCalls;
    }

    public function getDeliveredRecommendations(): int
    {
        return $this->deliveredRecommendations;
    }

    public function getTimespanBegin(): DateTimeImmutable
    {
        return parent::getTimespanBegin();
    }

    public function getTimespanDuration(): string
    {
        return parent::getTimespanDuration();
    }

    public function getConversionRatePercent(): ?float
    {
        return $this->conversionRatePercent;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (int)$properties['scenarioCalls'],
            (int)$properties['deliveredRecommendations'],
            new DateTimeImmutable($properties['timespanBegin']),
            $properties['timespanDuration'],
            isset($properties['conversionRatePercent']) ? (float)$properties['conversionRatePercent'] : null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'scenarioCalls' => $this->getScenarioCalls(),
            'deliveredRecommendations' => $this->getDeliveredRecommendations(),
            'timespanBegin' => $this->getTimespanBegin(),
            'timespanDuration' => $this->getTimespanDuration(),
            'conversionRatePercent' => $this->getConversionRatePercent(),
        ];
    }
}

class_alias(Event::class, 'Ibexa\Platform\Personalization\Value\Scenario\Event');
