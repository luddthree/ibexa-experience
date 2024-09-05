<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event;

use Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class FieldAttributeDefinitionEvent extends Event
{
    /** @var \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder */
    private $definitionBuilder;

    /** @var array */
    private $configuration;

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder $definitionBuilder
     * @param array $configuration
     */
    public function __construct(FieldAttributeDefinitionBuilder $definitionBuilder, array $configuration)
    {
        $this->definitionBuilder = $definitionBuilder;
        $this->configuration = $configuration;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder
     */
    public function getDefinitionBuilder(): FieldAttributeDefinitionBuilder
    {
        return $this->definitionBuilder;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}

class_alias(FieldAttributeDefinitionEvent::class, 'EzSystems\EzPlatformFormBuilder\Event\FieldAttributeDefinitionEvent');
