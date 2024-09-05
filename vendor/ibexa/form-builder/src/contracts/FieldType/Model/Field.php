<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Model;

use Ibexa\FormBuilder\Exception\AttributeNotFoundException;

class Field
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute[] */
    private $attributes;

    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator[] */
    private $validators;

    /** @var string */
    private $id;

    /**
     * @param string|null $id
     * @param string|null $identifier
     * @param string|null $name
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute[] $attributes
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator[] $validators
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
     * @param string $identifier
     *
     * @return bool
     */
    public function hasAttribute(string $identifier): bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute
     *
     * @throws \Ibexa\FormBuilder\Exception\AttributeNotFoundException
     */
    public function getAttribute(string $identifier): Attribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return $attribute;
            }
        }

        throw new AttributeNotFoundException($identifier);
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
     * @param string $identifier
     *
     * @return mixed
     *
     * @throws \Ibexa\FormBuilder\Exception\AttributeNotFoundException
     */
    public function getAttributeValue(string $identifier)
    {
        return $this->getAttribute($identifier)->getValue();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return array<\Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<\Ibexa\Contracts\FormBuilder\FieldType\Model\Validator>
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param array<\Ibexa\Contracts\FormBuilder\FieldType\Model\Validator> $validators
     */
    public function setValidators(array $validators): void
    {
        $this->validators = $validators;
    }
}

class_alias(Field::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Model\Field');
