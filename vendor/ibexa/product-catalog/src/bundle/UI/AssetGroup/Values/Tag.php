<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values;

final class Tag
{
    private string $name;

    /** @var mixed */
    private $value;

    private string $label;

    private ?string $formattedValue;

    /**
     * @param mixed $value
     */
    public function __construct(string $name, $value, string $label, ?string $formattedValue)
    {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->formattedValue = $formattedValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFormattedValue(): ?string
    {
        return $this->formattedValue;
    }
}
