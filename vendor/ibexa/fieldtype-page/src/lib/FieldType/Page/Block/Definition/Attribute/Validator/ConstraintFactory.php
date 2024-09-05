<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator;

use Ibexa\FieldTypePage\Exception\Registry\AttributeValidatorNotFoundException;

class ConstraintFactory
{
    /**
     * @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintClassRegistry
     */
    protected $classRegistry;

    /**
     * ConstraintFactory constructor.
     *
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintClassRegistry $classRegistry
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
     * @throws \Ibexa\FieldTypePage\Exception\Registry\AttributeValidatorNotFoundException
     */
    public function getConstraint(string $identifier, array $constraint)
    {
        if (!$this->classRegistry->hasConstraintClass($identifier)) {
            throw new AttributeValidatorNotFoundException($identifier);
        }

        $constraintClass = $this->classRegistry->getConstraintClass($identifier);

        $object = new $constraintClass($constraint['options'] ?? []);
        if (isset($constraint['message'])) {
            $object->message = $constraint['message'];
        }

        return $object;
    }
}

class_alias(ConstraintFactory::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory');
