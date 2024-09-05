<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Symfony\Contracts\EventDispatcher\Event;

class BlockAttributeDefinitionEvent extends Event
{
    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition */
    protected $definition;

    /** @var array */
    protected $configuration;

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $definition
     * @param array $configuration
     */
    public function __construct(BlockAttributeDefinition $definition, array $configuration)
    {
        $this->definition = $definition;
        $this->configuration = $configuration;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition
     */
    public function getDefinition(): BlockAttributeDefinition
    {
        return $this->definition;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $definition
     */
    public function setDefinition(BlockAttributeDefinition $definition): void
    {
        $this->definition = $definition;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}

class_alias(BlockAttributeDefinitionEvent::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\Event\BlockAttributeDefinitionEvent');
