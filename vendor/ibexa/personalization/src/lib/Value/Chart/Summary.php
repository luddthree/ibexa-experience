<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

use JsonSerializable;

final class Summary implements JsonSerializable
{
    private string $label;

    private string $value;

    public function __construct(string $label, string $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @phpstan-return array{
     *  'label': string,
     *  'value': string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
        ];
    }
}

class_alias(Summary::class, 'Ibexa\Platform\Personalization\Value\Chart\Summary');
