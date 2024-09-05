<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition;

class BlockDefinition
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var string */
    protected $category;

    /** @var string */
    protected $thumbnail;

    /** @var bool */
    protected $visible;

    /** @var string */
    protected $configurationTemplate;

    /** @var string[] */
    protected $views = [];

    /** @var array<string, BlockAttributeDefinition> */
    protected $attributes = [];

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return string[]
     */
    public function getViews(): array
    {
        return $this->views;
    }

    /**
     * @param string[] $views
     */
    public function setViews(array $views): void
    {
        $this->views = $views;
    }

    /**
     * @return array<string, BlockAttributeDefinition>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, BlockAttributeDefinition> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttribute(string $identifier): ?BlockAttributeDefinition
    {
        return $this->attributes[$identifier] ?? null;
    }

    /**
     * @return string
     */
    public function getConfigurationTemplate(): string
    {
        return $this->configurationTemplate;
    }

    /**
     * @param string $configurationTemplate
     */
    public function setConfigurationTemplate(string $configurationTemplate): void
    {
        $this->configurationTemplate = $configurationTemplate;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
}

class_alias(BlockDefinition::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\BlockDefinition');
