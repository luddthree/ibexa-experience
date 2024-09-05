<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

use JsonSerializable;

final class LegendItem implements JsonSerializable
{
    private string $name;

    private ?string $summary;

    public function __construct(
        string $name,
        ?string $summary = null
    ) {
        $this->name = $name;
        $this->summary = $summary;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @phpstan-return array{
     *  'name': string,
     *  'summary': ?string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'summary' => $this->getSummary(),
        ];
    }
}

class_alias(LegendItem::class, 'Ibexa\Platform\Personalization\Value\Chart\LegendItem');
