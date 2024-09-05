<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

use Ibexa\FormBuilder\Exception\FieldAttributeDefinitionNotFoundException;
use Ibexa\FormBuilder\Exception\FieldValidatorDefinitionNotFoundException;

class FieldDefinition
{
    public const DEFAULT_CATEGORY = 'default';

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

    /**
     * @param string $identifier
     * @param string $name
     * @param string $category
     * @param \Ibexa\FormBuilder\Definition\FieldAttributeDefinition[] $attributes
     * @param array $validators
     * @param string|null $thumbnail
     */
    public function __construct(
        string $identifier,
        string $name,
        string $category = self::DEFAULT_CATEGORY,
        array $attributes = [],
        array $validators = [],
        string $thumbnail = null
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->category = $category;
        $this->attributes = $attributes;
        $this->validators = $validators;
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinition[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinition
     */
    public function getAttribute(string $identifier): FieldAttributeDefinition
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return $attribute;
            }
        }

        throw new FieldAttributeDefinitionNotFoundException($identifier);
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinition[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasValidator(string $identifier): bool
    {
        foreach ($this->validators as $validator) {
            if ($validator->getIdentifier() === $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinition
     */
    public function getValidator(string $identifier): FieldValidatorDefinition
    {
        foreach ($this->validators as $validator) {
            if ($validator->getIdentifier() === $identifier) {
                return $validator;
            }
        }

        throw new FieldValidatorDefinitionNotFoundException($identifier);
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }
}

class_alias(FieldDefinition::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldDefinition');
