<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event;

use Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class FieldValidatorDefinitionEvent extends Event
{
    /** @var \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder */
    private $definitionBuilder;

    /** @var array */
    private $configuration;

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder $definitionBuilder
     * @param array $configuration
     */
    public function __construct(FieldValidatorDefinitionBuilder $definitionBuilder, array $configuration)
    {
        $this->definitionBuilder = $definitionBuilder;
        $this->configuration = $configuration;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldValidatorDefinitionBuilder
     */
    public function getDefinitionBuilder(): FieldValidatorDefinitionBuilder
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

class_alias(FieldValidatorDefinitionEvent::class, 'EzSystems\EzPlatformFormBuilder\Event\FieldValidatorDefinitionEvent');
