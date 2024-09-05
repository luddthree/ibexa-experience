<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

class FieldDefinitionBuilder
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var string */
    private $category;

    /** @var \Ibexa\FormBuilder\Definition\FieldAttributeDefinition[] */
    private $attributes;

    /** @var \Ibexa\FormBuilder\Definition\FieldValidatorDefinition[] */
    private $validators;

    /** @var string|null */
    private $thumbnail;

    public function __construct()
    {
        $this->category = FieldDefinition::DEFAULT_CATEGORY;
        $this->attributes = [];
        $this->validators = [];
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
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
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldAttributeDefinition $attribute
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function addAttribute(FieldAttributeDefinition $attribute): self
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldAttributeDefinition $attribute
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function removeAttribute(FieldAttributeDefinition $attribute): self
    {
        if (($index = array_search($attribute, $this->attributes)) !== false) {
            unset($this->attributes[$index]);
        }

        return $this;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinition[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldValidatorDefinition[] $validators
     */
    public function setValidators(array $validators): void
    {
        $this->validators = $validators;
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldValidatorDefinition $validator
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function addValidator(FieldValidatorDefinition $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldValidatorDefinition $validator
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function removeValidator(FieldValidatorDefinition $validator): self
    {
        if (($index = array_search($validator, $this->validators)) !== false) {
            unset($this->validators[$index]);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldDefinition
     */
    public function buildDefinition(): FieldDefinition
    {
        return new FieldDefinition(
            $this->identifier,
            $this->name,
            $this->category,
            $this->attributes,
            $this->validators,
            $this->thumbnail
        );
    }
}

class_alias(FieldDefinitionBuilder::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldDefinitionBuilder');
