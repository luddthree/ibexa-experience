<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use JsonSerializable;

final class RecommendationCall implements JsonSerializable
{
    public const TOTAL = 'all_calls';

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var float */
    private $percentage;

    /** @var int */
    private $calls;

    private function __construct(
        string $id,
        string $name,
        float $percentage,
        int $calls
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->percentage = $percentage;
        $this->calls = $calls;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPercentage(): float
    {
        return $this->percentage;
    }

    public function getCalls(): int
    {
        return $this->calls;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (string)$properties['id'],
            (string)$properties['name'],
            (float)$properties['percentage'],
            (int)$properties['calls'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'calls' => $this->getCalls(),
            'percentage' => $this->getPercentage(),
        ];
    }
}

class_alias(RecommendationCall::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\RecommendationCall');
