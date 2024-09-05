<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Registry;

use Ibexa\FieldTypePage\Exception\LayoutDefinitionNotFoundException;
use Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition;

/**
 * Registry of layout definitions.
 */
class LayoutDefinitionRegistry
{
    /** @var array<int|string, \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition> */
    private array $layoutDefinitions;

    /**
     * @param array<int|string, \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition> $layoutDefinitions
     */
    public function __construct(array $layoutDefinitions = [])
    {
        $this->layoutDefinitions = $layoutDefinitions;
    }

    /**
     * Adds layout definition to registry.
     *
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition $definition
     */
    public function addLayoutDefinition(LayoutDefinition $definition)
    {
        $this->layoutDefinitions[$definition->getId()] = $definition;
    }

    /**
     * Returns layout definitions.
     *
     * @return array
     */
    public function getLayoutDefinitions(): array
    {
        return $this->layoutDefinitions;
    }

    /**
     * Returns layout definition by it's id.
     *
     * @param int $id
     *
     * @return \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition $definition
     */
    public function getLayoutDefinitionById($id): LayoutDefinition
    {
        if (!isset($this->layoutDefinitions[$id])) {
            return $this->getDefaultLayout();
        }

        return $this->layoutDefinitions[$id];
    }

    /**
     * Returns default layout definition.
     *
     * @return \Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition
     */
    public function getDefaultLayout(?array $availableLayouts = null): LayoutDefinition
    {
        if (!isset($this->layoutDefinitions['default'])) {
            throw new LayoutDefinitionNotFoundException('default');
        }

        if ($availableLayouts !== null && !in_array('default', $availableLayouts, true)) {
            foreach ($this->layoutDefinitions as $layoutDefinition) {
                if (\in_array($layoutDefinition->getId(), $availableLayouts, true)) {
                    return $layoutDefinition;
                }
            }
        }

        return $this->layoutDefinitions['default'];
    }
}

class_alias(LayoutDefinitionRegistry::class, 'EzSystems\EzPlatformPageFieldType\Registry\LayoutDefinitionRegistry');
