<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

class FieldAttributeDefinitionBuilder
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $category;

    /** @var array */
    private $constraints;

    /** @var array */
    private $options;

    /** @var mixed|null */
    private $defaultValue;

    public function __construct()
    {
        $this->category = FieldAttributeDefinition::DEFAULT_CATEGORY;
        $this->constraints = [];
        $this->options = [];
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
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
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
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function setType(string $type): self
    {
        $this->type = $type;

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
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return array
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param array $constraints
     *
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinition
     */
    public function buildDefinition(): FieldAttributeDefinition
    {
        return new FieldAttributeDefinition(
            $this->identifier,
            $this->name,
            $this->type,
            $this->category,
            $this->constraints,
            $this->options,
            $this->defaultValue
        );
    }
}

class_alias(FieldAttributeDefinitionBuilder::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldAttributeDefinitionBuilder');
