<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper;

use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Exception\AttributeConfigurationMapperNotFoundException;

class AttributeConfigurationMapperRegistry
{
    /** @var \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface[] */
    private $mappers;

    /**
     * @param iterable $mappers
     */
    public function __construct(iterable $mappers)
    {
        /** @var \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface $mapper */
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper->getTypeIdentifier(), $mapper);
        }
    }

    /**
     * @return \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    /**
     * @param \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface[] $mappers
     */
    public function setMappers(array $mappers): void
    {
        $this->mappers = $mappers;
    }

    /**
     * @param string $type
     * @param \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface $mapper
     */
    public function addMapper(string $type, AttributeConfigurationMapperInterface $mapper): void
    {
        $this->mappers[$type] = $mapper;
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\AttributeConfigurationMapperNotFoundException
     */
    public function getMapper(string $type): AttributeConfigurationMapperInterface
    {
        if ($this->hasMapper($type)) {
            return $this->mappers[$type];
        }

        throw new AttributeConfigurationMapperNotFoundException($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasMapper(string $type): bool
    {
        return isset($this->mappers[$type]);
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldAttributeDefinition $fieldAttributeDefinition
     *
     * @return \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\AttributeConfigurationMapperNotFoundException
     */
    public function getMapperForAttribute(FieldAttributeDefinition $fieldAttributeDefinition): AttributeConfigurationMapperInterface
    {
        return $this->getMapper($fieldAttributeDefinition->getType());
    }
}

class_alias(AttributeConfigurationMapperRegistry::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\AttributeConfigurationMapperRegistry');
