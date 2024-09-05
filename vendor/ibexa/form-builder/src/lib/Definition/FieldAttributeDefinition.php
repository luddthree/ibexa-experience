<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

class FieldAttributeDefinition
{
    public const DEFAULT_CATEGORY = 'default';

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

    /**
     * @param string $identifier
     * @param string $name
     * @param string $type
     * @param string $category
     * @param array $constraints
     * @param array $options
     * @param null $defaultValue
     */
    public function __construct(
        string $identifier,
        string $name,
        string $type,
        string $category = self::DEFAULT_CATEGORY,
        array $constraints = [],
        array $options = [],
        $defaultValue = null
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->type = $type;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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

class_alias(FieldAttributeDefinition::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldAttributeDefinition');
