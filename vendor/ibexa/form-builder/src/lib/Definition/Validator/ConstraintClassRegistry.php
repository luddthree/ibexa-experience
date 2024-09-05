<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition\Validator;

class ConstraintClassRegistry
{
    /** @var array */
    protected $registry = [];

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConstraintClass(string $identifier)
    {
        return isset($this->registry[$identifier]);
    }

    /**
     * @param string $identifier
     *
     * @return string|null
     */
    public function getConstraintClass(string $identifier): ?string
    {
        return $this->hasConstraintClass($identifier)
            ? $this->registry[$identifier]
            : null;
    }

    /**
     * @return array
     */
    public function getConstraintClasses(): array
    {
        return $this->registry;
    }

    /**
     * @param string $identifier
     * @param string $constraintClass
     */
    public function setConstraintClass(string $identifier, string $constraintClass): void
    {
        $this->registry[$identifier] = $constraintClass;
    }

    /**
     * @param array $constraintClasses
     */
    public function setConstraintClasses(array $constraintClasses)
    {
        $this->registry = $constraintClasses;
    }
}

class_alias(ConstraintClassRegistry::class, 'EzSystems\EzPlatformFormBuilder\Definition\Validator\ConstraintClassRegistry');
