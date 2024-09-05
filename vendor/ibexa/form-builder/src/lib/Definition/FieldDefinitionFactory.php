<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition;

use Ibexa\FormBuilder\Event\FieldAttributeDefinitionEvent;
use Ibexa\FormBuilder\Event\FieldDefinitionEvent;
use Ibexa\FormBuilder\Event\FieldValidatorDefinitionEvent;
use Ibexa\FormBuilder\Event\FormEvents as Events;
use Ibexa\FormBuilder\Exception\FieldDefinitionNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FieldDefinitionFactory
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var array */
    private $configuration = [];

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param array $configuration
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, array $configuration)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->configuration = $configuration;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasFieldDefinition(string $identifier): bool
    {
        return isset($this->configuration[$identifier]);
    }

    /**
     * @return array
     */
    public function getFieldDefinitions(): array
    {
        $definitions = [];

        foreach (array_keys($this->configuration) as $identifier) {
            $definitions[] = $this->getFieldDefinition($identifier);
        }

        return $definitions;
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\FormBuilder\Definition\FieldDefinition
     */
    public function getFieldDefinition(string $identifier): FieldDefinition
    {
        if (!$this->hasFieldDefinition($identifier)) {
            throw new FieldDefinitionNotFoundException($identifier);
        }

        $configuration = $this->configuration[$identifier];

        $attributes = $this->getAttributesDefinitions(
            $identifier,
            $configuration['attributes']
        );

        $validators = $this->getValidatorsDefinitions(
            $identifier,
            $configuration['validators']
        );

        $fieldDefinition = new FieldDefinitionBuilder();
        $fieldDefinition->setIdentifier($identifier);
        $fieldDefinition->setName($configuration['name'] ?? $identifier);
        $fieldDefinition->setCategory($configuration['category']);
        $fieldDefinition->setThumbnail($configuration['thumbnail']);
        $fieldDefinition->setAttributes($attributes);
        $fieldDefinition->setValidators($validators);

        $event = new FieldDefinitionEvent($fieldDefinition, $configuration);

        $this->eventDispatcher->dispatch($event, Events::FIELD_DEFINITION);
        $this->eventDispatcher->dispatch($event, Events::getFieldDefinitionEventName($identifier));

        return $fieldDefinition->buildDefinition();
    }

    /**
     * @param string $fieldDefinitionIdentifier
     * @param array $attributes
     *
     * @return array
     */
    private function getAttributesDefinitions(string $fieldDefinitionIdentifier, array $attributes): array
    {
        $definitions = [];

        foreach ($attributes as $identifier => $configuration) {
            $attributeDefinition = new FieldAttributeDefinitionBuilder();
            $attributeDefinition->setIdentifier($identifier);
            $attributeDefinition->setName($configuration['name'] ?? $identifier);
            $attributeDefinition->setCategory($configuration['category']);
            $attributeDefinition->setType($configuration['type']);
            $attributeDefinition->setOptions($configuration['options']);
            $attributeDefinition->setDefaultValue($configuration['default_value']);
            $attributeDefinition->setConstraints((array)$configuration['validators']);

            $event = new FieldAttributeDefinitionEvent($attributeDefinition, $configuration);

            $this->eventDispatcher->dispatch($event, Events::FIELD_DEFINITION_ATTRIBUTE);
            $this->eventDispatcher->dispatch($event, Events::getFieldAttributeDefinitionEventName($fieldDefinitionIdentifier, $identifier));

            $definitions[] = $attributeDefinition->buildDefinition();
        }

        return $definitions;
    }

    /**
     * @param string $fieldDefinitionIdentifier
     * @param array $validators
     *
     * @return array
     */
    private function getValidatorsDefinitions(string $fieldDefinitionIdentifier, array $validators): array
    {
        $definitions = [];

        foreach ($validators as $identifier => $configuration) {
            $validatorDefinition = new FieldValidatorDefinitionBuilder();
            $validatorDefinition->setIdentifier($identifier);
            $validatorDefinition->setCategory($configuration['category']);
            $validatorDefinition->setOptions($configuration['options']);
            $validatorDefinition->setDefaultValue($configuration['default_value']);
            $validatorDefinition->setConstraints((array)$configuration['validators']);

            $event = new FieldValidatorDefinitionEvent($validatorDefinition, $configuration);

            $this->eventDispatcher->dispatch($event, Events::FIELD_DEFINITION_ATTRIBUTE);
            $this->eventDispatcher->dispatch($event, Events::getFieldValidatorDefinitionEventName($fieldDefinitionIdentifier, $identifier));

            $definitions[] = $validatorDefinition->buildDefinition();
        }

        return $definitions;
    }
}

class_alias(FieldDefinitionFactory::class, 'EzSystems\EzPlatformFormBuilder\Definition\FieldDefinitionFactory');
