<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Symfony\Contracts\EventDispatcher\Event;

class BlockDefinitionEvent extends Event
{
    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition */
    protected $definition;

    /** @var array */
    protected $configuration;

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $definition
     * @param array $configuration
     */
    public function __construct(BlockDefinition $definition, array $configuration)
    {
        $this->definition = $definition;
        $this->configuration = $configuration;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition
     */
    public function getDefinition(): BlockDefinition
    {
        return $this->definition;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $definition
     */
    public function setDefinition(BlockDefinition $definition): void
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

class_alias(BlockDefinitionEvent::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent');
