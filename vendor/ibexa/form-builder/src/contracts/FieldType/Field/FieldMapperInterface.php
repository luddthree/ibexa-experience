<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Field;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Symfony\Component\Form\FormBuilderInterface;

interface FieldMapperInterface
{
    /**
     * Builds form for the specified field.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     */
    public function mapField(FormBuilderInterface $builder, Field $field, array $constraints = []): void;

    /**
     * Returns identifier of the field definition supported by the mapper.
     *
     * @return string
     */
    public function getSupportedField(): string;
}

class_alias(FieldMapperInterface::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\FieldMapperInterface');
