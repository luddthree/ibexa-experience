<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

class FieldValidatorDefinition
{
    public const DEFAULT_CATEGORY = 'default';

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

    /**
     * @param string $identifier
     * @param string $category
     * @param array $constraints
     * @param array $options
     * @param null $defaultValue
     */
    public function __construct(
        string $identifier,
        string $category = self::DEFAULT_CATEGORY,
        array $constraints = [],
        array $options = [],
        $defaultValue = null
    ) {
        $this->identifier = $identifier;
        $this->category = $category;
        $this->constraints = $constraints;
        $this->options = $options;
        $this->defaultValue = $defaultValue;
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
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}

class_alias(FieldValidatorDefinition::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldValidatorDefinition');
