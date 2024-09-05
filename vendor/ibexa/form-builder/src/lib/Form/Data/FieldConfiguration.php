<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Data;

class FieldConfiguration
{
    /** @var string */
    private $id;

    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var array */
    private $attributes;

    /** @var array */
    private $validators;

    /**
     * @param string|null $id
     * @param string|null $identifier
     * @param string|null $name
     * @param array $attributes
     * @param array $validators
     */
    public function __construct(
        string $id = null,
        string $identifier = null,
        string $name = null,
        array $attributes = [],
        array $validators = []
    ) {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->attributes = $attributes;
        $this->validators = $validators;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
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
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param array $validators
     */
    public function setValidators(array $validators): void
    {
        $this->validators = $validators;
    }
}

class_alias(FieldConfiguration::class, 'EzSystems\EzPlatformFormBuilder\Form\Data\FieldConfiguration');
