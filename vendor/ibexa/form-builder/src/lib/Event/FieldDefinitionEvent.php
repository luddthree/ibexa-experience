<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event;

use Ibexa\FormBuilder\Definition\FieldDefinitionBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class FieldDefinitionEvent extends Event
{
    /** @var \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder */
    private $definitionBuilder;

    /** @var array */
    private $configuration;

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder $definitionBuilder
     * @param array $configuration
     */
    public function __construct(FieldDefinitionBuilder $definitionBuilder, array $configuration)
    {
        $this->definitionBuilder = $definitionBuilder;
        $this->configuration = $configuration;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder
     */
    public function getDefinitionBuilder(): FieldDefinitionBuilder
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

class_alias(FieldDefinitionEvent::class, 'EzSystems\EzPlatformFormBuilder\Event\FieldDefinitionEvent');
