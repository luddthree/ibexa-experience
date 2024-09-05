<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use JsonSerializable;

final class ConversionRate implements JsonSerializable
{
    public const TOTAL = 'all_scenarios';

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var float */
    private $percentage;

    public function __construct(
        string $id,
        string $name,
        float $percentage
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->percentage = $percentage;
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

    public static function fromArray(array $properties): self
    {
        return new self(
            (string)$properties['id'],
            (string)$properties['name'],
            (float)$properties['percentage'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'percentage' => $this->getPercentage(),
        ];
    }
}

class_alias(ConversionRate::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\ConversionRate');
