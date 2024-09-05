<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance;

use DateTimeImmutable;

final class RecommendationSummary
{
    /**
     * @var int|null
     */
    private $totalRecoCalls;

    /**
     * @var float|null
     */
    private $currentClickedRecoPercent;

    /**
     * @var float|null
     */
    private $previousClickedRecoPercent;

    /**
     * @var float|null
     */
    private $clickedRecoDelta;

    /**
     * @var int|null
     */
    private $totalImportedItems;

    /**
     * @var \DateTimeImmutable|null
     */
    private $lastProductFeed;

    /**
     * @var int|null
     */
    private $eventsCollected;

    private function __construct(
        ?int $totalRecoCalls = null,
        ?float $currentClickedRecoPercent = null,
        ?float $previousClickedRecoPercent = null,
        ?float $clickedRecoDelta = null,
        ?int $totalImportedItems = null,
        ?DateTimeImmutable $lastProductFeed = null,
        ?int $eventsCollected = null
    ) {
        $this->totalRecoCalls = $totalRecoCalls;
        $this->currentClickedRecoPercent = $currentClickedRecoPercent;
        $this->previousClickedRecoPercent = $previousClickedRecoPercent;
        $this->clickedRecoDelta = $clickedRecoDelta;
        $this->totalImportedItems = $totalImportedItems;
        $this->lastProductFeed = $lastProductFeed;
        $this->eventsCollected = $eventsCollected;
    }

    public function getTotalRecoCalls(): ?int
    {
        return $this->totalRecoCalls;
    }

    public function getCurrentClickedRecoPercent(): ?float
    {
        return $this->currentClickedRecoPercent;
    }

    public function getPreviousClickedRecoPercent(): ?float
    {
        return $this->previousClickedRecoPercent;
    }

    public function getClickedRecoDelta(): ?float
    {
        return $this->clickedRecoDelta;
    }

    public function getTotalImportedItems(): ?int
    {
        return $this->totalImportedItems;
    }

    public function getLastProductFeed(): ?DateTimeImmutable
    {
        return $this->lastProductFeed;
    }

    public function getEventsCollected(): ?int
    {
        return $this->eventsCollected;
    }

    public static function fromArray(array $parameters): self
    {
        return new self(
            isset($parameters['totalRecoCalls']) ? (int)$parameters['totalRecoCalls'] : null,
            isset($parameters['currentClickedRecoPercent']) ? (float)$parameters['currentClickedRecoPercent'] : null,
            isset($parameters['previousClickedRecoPercent']) ? (float)$parameters['previousClickedRecoPercent'] : null,
            isset($parameters['clickedRecoDelta']) ? (float)$parameters['clickedRecoDelta'] : null,
            isset($parameters['totalImportedItems']) ? (int)$parameters['totalImportedItems'] : null,
            isset($parameters['lastProductFeed']) && !empty($parameters['lastProductFeed'])
                ? new DateTimeImmutable($parameters['lastProductFeed'])
                : null,
            isset($parameters['eventsCollected']) ? (int)$parameters['eventsCollected'] : null,
        );
    }
}

class_alias(RecommendationSummary::class, 'Ibexa\Platform\Personalization\Value\Performance\RecommendationSummary');
