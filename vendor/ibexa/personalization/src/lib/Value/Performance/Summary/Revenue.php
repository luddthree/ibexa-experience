<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use JsonSerializable;

final class Revenue implements JsonSerializable
{
    private ?string $currency;

    private ?int $itemsPurchased;

    private ?float $revenue;

    private function __construct(
        ?string $currency = null,
        ?int $itemsPurchased = null,
        ?float $revenue = null
    ) {
        $this->currency = $currency;
        $this->itemsPurchased = $itemsPurchased;
        $this->revenue = $revenue;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getItemsPurchased(): ?int
    {
        return $this->itemsPurchased;
    }

    public function getRevenue(): ?float
    {
        return $this->revenue;
    }

    /**
     * @phpstan-param array{
     *   'currency'?: string|null,
     *   'items_purchased'?: float|int|string|null,
     *   'revenue'?: float|int|string|null,
     * } $parameters
     */
    public static function fromArray(array $parameters): self
    {
        return new self(
            $parameters['currency'] ?? null,
            isset($parameters['items_purchased']) ? (int)$parameters['items_purchased'] : null,
            isset($parameters['revenue']) ? (float)$parameters['revenue'] : null,
        );
    }

    /**
     * @phpstan-return array{
     *  'currency': ?string,
     *  'itemsPurchased': ?int,
     *  'revenue': ?float,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'currency' => $this->getCurrency(),
            'itemsPurchased' => $this->getItemsPurchased(),
            'revenue' => $this->getRevenue(),
        ];
    }
}

class_alias(Revenue::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\Revenue');
