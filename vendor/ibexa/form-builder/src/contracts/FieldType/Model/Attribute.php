<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Model;

class Attribute
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $value;

    /**
     * @param string $identifier
     * @param string $value
     */
    public function __construct(
        string $identifier = null,
        string $value = null
    ) {
        $this->identifier = $identifier;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}

class_alias(Attribute::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Model\Attribute');
