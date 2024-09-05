<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypeAddress\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class MapFieldEvent extends Event
{
    private string $identifier;

    private string $addressType;

    private ?string $country;

    private ?string $type = null;

    /** @var string|false|null */
    private $label = null;

    private array $options = [];

    public function __construct(
        string $identifier,
        string $addressType,
        ?string $country = null
    ) {
        $this->identifier = $identifier;
        $this->addressType = $addressType;
        $this->country = $country;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAddressType(): string
    {
        return $this->addressType;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool|string|null
     */
    public function getLabel()
    {
        return $this->label ?? null;
    }

    /**
     * @param bool|string|null $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function setOption(string $option, $value): void
    {
        $this->options[$option] = $value;
    }
}
