<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

class FieldValidatorDefinitionBuilder
{
    /** @var string */
    private $identifier;

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
        $this->category = FieldValidatorDefinition::DEFAULT_CATEGORY;
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
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

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
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder
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
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder
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
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder
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
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinition
     */
    public function buildDefinition(): FieldValidatorDefinition
    {
        return new FieldValidatorDefinition(
            $this->identifier,
            $this->category,
            $this->constraints,
            $this->options,
            $this->defaultValue
        );
    }
}

class_alias(FieldValidatorDefinitionBuilder::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldValidatorDefinitionBuilder');
