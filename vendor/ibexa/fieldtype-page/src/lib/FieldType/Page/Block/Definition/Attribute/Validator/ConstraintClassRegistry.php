<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator;

class ConstraintClassRegistry
{
    /**
     * @var array
     */
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
     * @phpstan-return class-string<\Symfony\Component\Validator\Constraint>|null
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
     * @phpstan-return array<class-string<\Symfony\Component\Validator\Constraint>>
     */
    public function getConstraintClasses(): array
    {
        return $this->registry;
    }

    /**
     * @param string $identifier
     * @param string $constraintClass
     * @phpstan-param class-string<\Symfony\Component\Validator\Constraint> $constraintClass
     */
    public function setConstraintClass(string $identifier, string $constraintClass): void
    {
        $this->registry[$identifier] = $constraintClass;
    }

    /**
     * @phpstan-param array<non-empty-string, class-string<\Symfony\Component\Validator\Constraint>> $constraintClasses
     */
    public function setConstraintClasses(array $constraintClasses)
    {
        $this->registry = $constraintClasses;
    }
}

class_alias(ConstraintClassRegistry::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintClassRegistry');
