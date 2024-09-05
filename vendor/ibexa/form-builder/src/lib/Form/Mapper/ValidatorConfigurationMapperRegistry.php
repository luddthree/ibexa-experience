<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper;

use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use Ibexa\FormBuilder\Exception\ValidatorConfigurationMapperNotFoundException;

class ValidatorConfigurationMapperRegistry
{
    /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface[] */
    private $mappers;

    /**
     * @param iterable $mappers
     */
    public function __construct(iterable $mappers)
    {
        /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface $mapper */
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper->getTypeIdentifier(), $mapper);
        }
    }

    /**
     * @return \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    /**
     * @param \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface[] $mappers
     */
    public function setMappers(array $mappers): void
    {
        $this->mappers = $mappers;
    }

    /**
     * @param string $type
     * @param \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface $mapper
     */
    public function addMapper(string $type, ValidatorConfigurationMapperInterface $mapper): void
    {
        $this->mappers[$type] = $mapper;
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConfigurationMapperNotFoundException
     */
    public function getMapper(string $type): ValidatorConfigurationMapperInterface
    {
        if ($this->hasMapper($type)) {
            return $this->mappers[$type];
        }

        throw new ValidatorConfigurationMapperNotFoundException($type);
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
     * @param \Ibexa\FormBuilder\Definition\FieldValidatorDefinition $fieldValidatorDefinition
     *
     * @return \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConfigurationMapperNotFoundException
     */
    public function getMapperForValidator(FieldValidatorDefinition $fieldValidatorDefinition): ValidatorConfigurationMapperInterface
    {
        return $this->getMapper($fieldValidatorDefinition->getIdentifier());
    }
}

class_alias(ValidatorConfigurationMapperRegistry::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\ValidatorConfigurationMapperRegistry');
