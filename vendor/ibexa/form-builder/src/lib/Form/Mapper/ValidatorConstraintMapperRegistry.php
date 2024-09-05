<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException;

class ValidatorConstraintMapperRegistry
{
    /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface[] */
    private $mappers;

    /**
     * @param iterable $mappers
     */
    public function __construct(iterable $mappers)
    {
        /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface $mapper */
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper->getTypeIdentifier(), $mapper);
        }
    }

    /**
     * @return \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    /**
     * @param \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface[] $mappers
     */
    public function setMappers(array $mappers): void
    {
        $this->mappers = $mappers;
    }

    /**
     * @param string $type
     * @param \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface $mapper
     */
    public function addMapper(string $type, ValidatorConstraintMapperInterface $mapper): void
    {
        $this->mappers[$type] = $mapper;
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function getMapper(string $type): ValidatorConstraintMapperInterface
    {
        if ($this->hasMapper($type)) {
            return $this->mappers[$type];
        }

        throw new ValidatorConstraintMapperNotFoundException($type);
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
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator $validator
     *
     * @return \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function getMapperForValidator(Validator $validator): ValidatorConstraintMapperInterface
    {
        return $this->getMapper($validator->getIdentifier());
    }
}

class_alias(ValidatorConstraintMapperRegistry::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\ValidatorConstraintMapperRegistry');
