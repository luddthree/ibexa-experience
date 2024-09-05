<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Definition\Validator;

use Ibexa\FormBuilder\Exception\ValidatorNotFoundException;

class ConstraintFactory
{
    /** @var \Ibexa\FormBuilder\Definition\Validator\ConstraintClassRegistry */
    protected $classRegistry;

    /**
     * @param \Ibexa\FormBuilder\Definition\Validator\ConstraintClassRegistry $classRegistry
     */
    public function __construct(ConstraintClassRegistry $classRegistry)
    {
        $this->classRegistry = $classRegistry;
    }

    /**
     * @param string $identifier
     * @param array $constraint
     *
     * @return mixed
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorNotFoundException
     */
    public function getConstraint(string $identifier, array $constraint)
    {
        if (!$this->classRegistry->hasConstraintClass($identifier)) {
            throw new ValidatorNotFoundException($identifier);
        }

        $constraintClass = $this->classRegistry->getConstraintClass($identifier);

        $object = new $constraintClass($constraint['options'] ?? []);
        $object->message = $constraint['message'];

        return $object;
    }
}

class_alias(ConstraintFactory::class, 'EzSystems\EzPlatformFormBuilder\Definition\Validator\ConstraintFactory');
