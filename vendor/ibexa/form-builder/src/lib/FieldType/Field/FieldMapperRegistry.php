<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\Exception\FieldMapperNotFoundException;

class FieldMapperRegistry
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface[] */
    private $mappers;

    /**
     * @param iterable<\Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface>|null $mappers
     */
    public function __construct(?iterable $mappers = null)
    {
        $this->mappers = [];
        if ($mappers !== null) {
            foreach ($mappers as $mapper) {
                $this->mappers[$mapper->getSupportedField()] = $mapper;
            }
        }
    }

    /**
     * @return iterable
     */
    public function getMappers(): iterable
    {
        return array_values($this->mappers);
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface $mapper
     */
    public function addMapper(FieldMapperInterface $mapper): void
    {
        $this->mappers[$mapper->getSupportedField()] = $mapper;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasMapper(string $identifier): bool
    {
        return isset($this->mappers[$identifier]);
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\FieldMapperNotFoundException
     */
    public function getMapper(string $identifier): FieldMapperInterface
    {
        if ($this->hasMapper($identifier)) {
            return $this->mappers[$identifier];
        }

        throw new FieldMapperNotFoundException($identifier);
    }
}

class_alias(FieldMapperRegistry::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\FieldMapperRegistry');
